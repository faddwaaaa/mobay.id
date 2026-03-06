<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminWalletLedger;
use App\Models\AdminWalletWithdrawal;
use App\Services\MidtransPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class WalletController extends Controller
{
    private const PIN_RATE_KEY = 'admin_wallet_pin_verify:{userId}';
    private const PIN_MAX_TRIES = 3;
    private const PIN_DECAY_SEC = 300;

    public function __construct(private readonly MidtransPayoutService $midtransPayoutService)
    {
    }

    public function index()
    {
        $ledgerQuery = AdminWalletLedger::query()->latest();

        $entries = (clone $ledgerQuery)->paginate(15)->withQueryString();

        $totalCredit = (int) AdminWalletLedger::where('direction', 'credit')->sum('amount');
        $totalDebit = (int) AdminWalletLedger::where('direction', 'debit')->sum('amount');
        $balance = max(0, $totalCredit - $totalDebit);
        $pendingWithdrawalAmount = (int) AdminWalletWithdrawal::where('status', 'pending')->sum('amount');
        $availableBalance = max(0, $balance - $pendingWithdrawalAmount);

        $stats = [
            'balance' => $balance,
            'available_balance' => $availableBalance,
            'pending_withdrawal_amount' => $pendingWithdrawalAmount,
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
            'total_entries' => AdminWalletLedger::count(),
        ];

        $withdrawals = AdminWalletWithdrawal::with(['requester', 'processor'])
            ->latest()
            ->take(10)
            ->get();

        $banks = [
            'BCA', 'BNI', 'BRI', 'MANDIRI', 'CIMB', 'PERMATA', 'BSI', 'OCBC', 'MAYBANK',
        ];

        return view('admin.wallet.index', compact('entries', 'stats', 'withdrawals', 'banks'));
    }

    public function withdraw(Request $request)
    {
        $admin = $request->user();

        if (!$admin || $admin->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:' . (int) config('midtrans.withdrawal.min_amount', 10000), 'max:' . (int) config('midtrans.withdrawal.max_amount', 50000000)],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
            'pin' => ['required', 'digits:6'],
            'password' => ['required', 'string'],
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.digits' => 'PIN harus 6 digit.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $pinKey = str_replace('{userId}', (string) $admin->id, self::PIN_RATE_KEY);
        if (RateLimiter::tooManyAttempts($pinKey, self::PIN_MAX_TRIES)) {
            $seconds = RateLimiter::availableIn($pinKey);
            return back()->with('error', "Terlalu banyak percobaan PIN. Coba lagi dalam {$seconds} detik.");
        }

        $pinValid = config('payment.dev_mode', false)
            ? ($validated['pin'] === config('payment.dev_pin', '123456'))
            : ($admin->pin_hash && Hash::check($validated['pin'], (string) $admin->pin_hash));

        if (!$pinValid) {
            RateLimiter::hit($pinKey, self::PIN_DECAY_SEC);
            $remaining = max(0, self::PIN_MAX_TRIES - RateLimiter::attempts($pinKey));
            return back()->with('error', "PIN salah. Sisa percobaan: {$remaining}");
        }

        RateLimiter::clear($pinKey);

        if (!Hash::check($validated['password'], (string) $admin->password)) {
            return back()->with('error', 'Password akun admin tidak valid.');
        }

        $amount = (int) $validated['amount'];

        $withdrawal = DB::transaction(function () use ($amount, $validated, $admin, $request) {
            $ledgerTotals = AdminWalletLedger::selectRaw("
                COALESCE(SUM(CASE WHEN direction = 'credit' THEN amount ELSE 0 END), 0) AS total_credit,
                COALESCE(SUM(CASE WHEN direction = 'debit' THEN amount ELSE 0 END), 0) AS total_debit
            ")->lockForUpdate()->first();

            $balance = max(0, ((int) $ledgerTotals->total_credit) - ((int) $ledgerTotals->total_debit));
            $pending = (int) AdminWalletWithdrawal::where('status', 'pending')->lockForUpdate()->sum('amount');
            $available = max(0, $balance - $pending);

            if ($amount > $available) {
                return null;
            }

            return AdminWalletWithdrawal::create([
                'amount' => $amount,
                'bank_name' => strtoupper(trim($validated['bank_name'])),
                'account_name' => trim($validated['account_name']),
                'account_number' => trim($validated['account_number']),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'requested_by' => $admin->id,
                'ip_address' => $request->ip(),
            ]);
        });

        if (!$withdrawal) {
            return back()->with('error', 'Saldo tersedia tidak mencukupi untuk penarikan ini.');
        }

        try {
            $payoutResult = $this->midtransPayoutService->createPayout([
                'account_name' => $withdrawal->account_name,
                'account_number' => $withdrawal->account_number,
                'bank_name' => $withdrawal->bank_name,
                'amount' => $withdrawal->amount,
                'email' => $admin->email,
                'notes' => $withdrawal->notes ?: 'Penarikan dana dompet admin Payou.id',
            ]);

            if (!($payoutResult['success'] ?? false)) {
                $withdrawal->update([
                    'status' => 'rejected',
                    'rejection_reason' => $payoutResult['message'] ?? 'Payout Midtrans gagal diproses.',
                    'midtrans_response' => $payoutResult,
                    'processed_by' => $admin->id,
                    'processed_at' => now(),
                ]);

                return back()->with('error', 'Midtrans payout gagal: ' . ($payoutResult['message'] ?? 'unknown error'));
            }

            $response = $payoutResult['data'] ?? [];
            $payoutData = $response['payouts'][0] ?? [];
            $payoutStatus = (string) ($payoutData['status'] ?? 'pending');
            $payoutId = $payoutData['reference_no'] ?? null;
            $isCompleted = $payoutStatus === 'processed';

            DB::transaction(function () use ($withdrawal, $admin, $response, $payoutId, $isCompleted) {
                $withdrawalLocked = AdminWalletWithdrawal::whereKey($withdrawal->id)->lockForUpdate()->firstOrFail();
                if ($withdrawalLocked->status !== 'pending') {
                    return;
                }

                $ledgerTotals = AdminWalletLedger::selectRaw("
                    COALESCE(SUM(CASE WHEN direction = 'credit' THEN amount ELSE 0 END), 0) AS total_credit,
                    COALESCE(SUM(CASE WHEN direction = 'debit' THEN amount ELSE 0 END), 0) AS total_debit
                ")->lockForUpdate()->first();

                $currentBalance = max(0, ((int) $ledgerTotals->total_credit) - ((int) $ledgerTotals->total_debit));
                if ($withdrawalLocked->amount > $currentBalance) {
                    $withdrawalLocked->update([
                        'status' => 'rejected',
                        'rejection_reason' => 'Saldo dompet admin berubah sebelum finalisasi payout.',
                        'midtrans_response' => $response,
                        'processed_by' => $admin->id,
                        'processed_at' => now(),
                    ]);
                    return;
                }

                $newBalance = $currentBalance - (int) $withdrawalLocked->amount;

                AdminWalletLedger::create([
                    'source' => 'manual_adjustment',
                    'direction' => 'debit',
                    'amount' => (int) $withdrawalLocked->amount,
                    'balance_after' => $newBalance,
                    'reference_type' => AdminWalletWithdrawal::class,
                    'reference_id' => $withdrawalLocked->id,
                    'description' => 'Admin withdrawal ke ' . $withdrawalLocked->bank_name . ' - ' . $withdrawalLocked->account_number,
                    'created_by' => $admin->id,
                ]);

                $withdrawalLocked->update([
                    'status' => $isCompleted ? 'completed' : 'approved',
                    'payout_id' => $payoutId,
                    'midtrans_response' => $response,
                    'processed_by' => $admin->id,
                    'processed_at' => now(),
                ]);
            });

            $withdrawal->refresh();
            if ($withdrawal->status === 'rejected') {
                return back()->with('error', $withdrawal->rejection_reason ?: 'Penarikan ditolak saat finalisasi.');
            }

            if ($withdrawal->status === 'completed') {
                return back()->with('success', 'Penarikan admin selesai diproses Midtrans.');
            }

            return back()->with('success', 'Penarikan admin dikirim ke Midtrans dan sedang diproses.');
        } catch (\Throwable $e) {
            Log::error('Admin wallet withdraw failed', [
                'admin_id' => $admin->id,
                'withdrawal_id' => $withdrawal->id,
                'message' => $e->getMessage(),
            ]);

            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => 'Terjadi kesalahan sistem saat menghubungi Midtrans.',
                'processed_by' => $admin->id,
                'processed_at' => now(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses payout Midtrans.');
        }
    }
}
