<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\User;
use App\Services\MidtransPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WithdrawalController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransPayoutService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display a listing of withdrawals
     */
    public function index()
    {
        $withdrawals = Withdrawal::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('withdrawals.index', compact('withdrawals'));
    }

    /**
     * Store a newly created withdrawal request and process immediately
     * WITH COMPREHENSIVE SECURITY VALIDATIONS
     */
    public function store(Request $request)
    {
        // ===== VALIDASI INPUT =====
        $request->validate([
            'amount' => 'required|numeric|min:50000|max:10000000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|min:8|max:20|regex:/^[0-9]+$/',
            'account_name' => 'required|string|max:100|regex:/^[a-zA-Z\s\.]+$/',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Jumlah penarikan harus diisi',
            'amount.min' => 'Minimal penarikan adalah Rp 50.000',
            'amount.max' => 'Maksimal penarikan adalah Rp 10.000.000',
            'bank_name.required' => 'Nama bank harus diisi',
            'account_number.required' => 'Nomor rekening harus diisi',
            'account_number.regex' => 'Nomor rekening hanya boleh angka',
            'account_number.min' => 'Nomor rekening minimal 8 digit',
            'account_number.max' => 'Nomor rekening maksimal 20 digit',
            'account_name.required' => 'Nama pemilik rekening harus diisi',
            'account_name.regex' => 'Nama pemilik rekening hanya boleh huruf dan spasi',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        // Log withdrawal attempt
        Log::channel('withdrawal')->info('Withdrawal attempt', [
            'user_id' => $user->id,
            'amount' => $amount,
            'bank' => $request->bank_name,
            'account_number' => substr($request->account_number, 0, 4) . '****', // Mask for security
            'ip' => $request->ip(),
        ]);

        // ===== VALIDASI KEAMANAN =====

        // 1. Validasi Bank yang Didukung
        $validBanks = [
            'BCA', 'BNI', 'BRI', 'MANDIRI', 'CIMB', 'PERMATA', 
            'BNI SYARIAH', 'BSI', 'DANAMON', 'MEGA', 'PANIN', 
            'MUAMALAT', 'OCBC', 'MAYBANK', 'BTPN', 'JENIUS', 'SINARMAS'
        ];

        if (!in_array(strtoupper($request->bank_name), $validBanks)) {
            Log::channel('withdrawal')->warning('Invalid bank', [
                'user_id' => $user->id,
                'bank' => $request->bank_name,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bank tidak valid atau tidak didukung'
            ], 400);
        }

        // 2. Validasi Saldo Mencukupi
        if ($user->balance < $amount) {
            Log::channel('withdrawal')->warning('Insufficient balance', [
                'user_id' => $user->id,
                'requested' => $amount,
                'available' => $user->balance,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi. Saldo Anda: Rp ' . number_format($user->balance, 0, ',', '.')
            ], 400);
        }

        // 3. Cek Penarikan yang Sedang Diproses
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($pendingWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki penarikan yang sedang diproses. Tunggu hingga selesai.'
            ], 400);
        }

        // 4. Limit Penarikan Per Hari (Anti-Fraud)
        $todayWithdrawals = Withdrawal::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->sum('amount');

        $dailyLimit = 25000000; // Rp 25 juta per hari

        if (($todayWithdrawals + $amount) > $dailyLimit) {
            Log::channel('withdrawal')->warning('Daily limit exceeded', [
                'user_id' => $user->id,
                'today_total' => $todayWithdrawals,
                'requested' => $amount,
                'limit' => $dailyLimit,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Limit penarikan harian terlampaui. Limit harian: Rp ' . number_format($dailyLimit, 0, ',', '.')
            ], 400);
        }

        // 5. Limit Frekuensi Penarikan (Anti-Spam)
        $countToday = Withdrawal::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        if ($countToday >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 5 kali penarikan per hari'
            ], 400);
        }

        // 6. Cooldown Period (Jeda antar penarikan) - 5 menit
        $lastWithdrawal = Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastWithdrawal && $lastWithdrawal->created_at->diffInMinutes(now()) < 5) {
            return response()->json([
                'success' => false,
                'message' => 'Harap tunggu 5 menit sejak penarikan terakhir'
            ], 400);
        }

        // 7. Anti-Fraud: IP Address Check
        $recentIpCheck = Withdrawal::where('user_id', '!=', $user->id)
            ->where('ip_address', $request->ip())
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->count();

        if ($recentIpCheck > 10) {
            Log::channel('withdrawal')->critical('Suspicious IP detected', [
                'ip' => $request->ip(),
                'count' => $recentIpCheck,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan hubungi customer support.'
            ], 400);
        }

        // 8. Validasi Format Nomor Rekening
        $accountNumber = preg_replace('/[^0-9]/', '', $request->account_number);
        
        if (strlen($accountNumber) < 8 || strlen($accountNumber) > 20) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor rekening harus terdiri dari 8-20 digit'
            ], 400);
        }

        // ===== PROSES PENARIKAN =====
        DB::beginTransaction();
        try {
            // TESTING MODE - Bypass Midtrans (untuk development)
            if (config('app.env') === 'local' && config('midtrans.testing_mode', false)) {
                
                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'bank_name' => strtoupper($request->bank_name),
                    'account_number' => $accountNumber,
                    'account_name' => $request->account_name,
                    'notes' => $request->notes,
                    'status' => 'approved',
                    'payout_id' => 'TEST-' . uniqid(),
                    'midtrans_response' => ['test_mode' => true, 'message' => 'Testing mode - No real payout'],
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

                $user->decrement('balance', $amount);

                DB::table('transactions')->insert([
                    'user_id' => $user->id,
                    'order_id' => 'WD-TEST-' . $withdrawal->id . '-' . time(),
                    'transaction_id' => $withdrawal->payout_id,
                    'amount' => $amount,
                    'status' => 'settlement',
                    'payment_method' => 'withdrawal',
                    'notes' => 'TEST MODE - Penarikan saldo ke ' . $request->bank_name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::commit();

                Log::channel('withdrawal')->info('Withdrawal successful (TEST MODE)', [
                    'withdrawal_id' => $withdrawal->id,
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '[TEST MODE] Penarikan berhasil diproses! (No real money transfer)',
                    'data' => [
                        'withdrawal_id' => $withdrawal->id,
                        'reference_no' => $withdrawal->payout_id,
                        'amount' => 'Rp ' . number_format($amount, 0, ',', '.'),
                        'status' => $withdrawal->status,
                        'mode' => 'testing'
                    ]
                ]);
            }

            // PRODUCTION MODE - Call Midtrans API
            $payoutData = [
                'account_name' => $request->account_name,
                'account_number' => $accountNumber,
                'bank_name' => strtoupper($request->bank_name),
                'amount' => $amount,
                'email' => $user->email,
                'notes' => $request->notes ?? 'Penarikan saldo dari Payou.id',
            ];

            $payoutResult = $this->midtransService->createPayout($payoutData);

            if (!$payoutResult['success']) {
                throw new \Exception($payoutResult['message'] ?? 'Gagal memproses penarikan ke Midtrans');
            }

            $payoutResponse = $payoutResult['data'];
            $payoutStatus = $payoutResponse['payouts'][0]['status'] ?? 'pending';
            $referenceNo = $payoutResponse['payouts'][0]['reference_no'] ?? null;

            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'bank_name' => strtoupper($request->bank_name),
                'account_number' => $accountNumber,
                'account_name' => $request->account_name,
                'notes' => $request->notes,
                'status' => $payoutStatus === 'processed' ? 'completed' : 'approved',
                'payout_id' => $referenceNo,
                'midtrans_response' => $payoutResponse,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            $user->decrement('balance', $amount);

            DB::table('transactions')->insert([
                'user_id' => $user->id,
                'order_id' => 'WD-' . $withdrawal->id . '-' . time(),
                'transaction_id' => $referenceNo ?? 'WITHDRAWAL-' . $withdrawal->id,
                'amount' => $amount,
                'status' => 'settlement',
                'payment_method' => 'withdrawal',
                'midtrans_response' => json_encode($payoutResponse),
                'notes' => 'Penarikan saldo ke ' . $request->bank_name . ' - ' . $accountNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            Log::channel('withdrawal')->info('Withdrawal successful (PRODUCTION)', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $user->id,
                'reference_no' => $referenceNo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penarikan berhasil diproses! Dana akan masuk ke rekening Anda dalam 1-3 hari kerja.',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'reference_no' => $referenceNo,
                    'amount' => 'Rp ' . number_format($amount, 0, ',', '.'),
                    'status' => $withdrawal->status,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('withdrawal')->error('Withdrawal failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses penarikan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel withdrawal request (only if still pending or approved)
     */
    public function cancel($id)
    {
        $withdrawal = Withdrawal::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!in_array($withdrawal->status, ['pending', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan dengan status ' . $withdrawal->status . ' tidak dapat dibatalkan'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'status' => 'cancelled',
                'rejection_reason' => 'Dibatalkan oleh user',
            ]);

            $withdrawal->user->increment('balance', $withdrawal->amount);

            DB::table('transactions')
                ->where('transaction_id', $withdrawal->payout_id ?? 'WITHDRAWAL-' . $withdrawal->id)
                ->update([
                    'status' => 'cancelled',
                    'notes' => 'Penarikan dibatalkan oleh user',
                    'updated_at' => now(),
                ]);

            DB::commit();

            Log::channel('withdrawal')->info('Withdrawal cancelled', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penarikan berhasil dibatalkan dan saldo dikembalikan ke akun Anda'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal Cancel Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan penarikan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check withdrawal status from Midtrans
     */
    public function checkStatus($id)
    {
        $withdrawal = Withdrawal::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$withdrawal->payout_id) {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan ini belum memiliki reference number dari Midtrans'
            ], 400);
        }

        $statusResult = $this->midtransService->getPayoutStatus($withdrawal->payout_id);

        if ($statusResult['success']) {
            $payoutData = $statusResult['data'];
            $status = $payoutData['status'] ?? null;
            
            if ($status === 'processed') {
                $withdrawal->update([
                    'status' => 'completed',
                    'midtrans_response' => $payoutData,
                ]);

                DB::table('transactions')
                    ->where('transaction_id', $withdrawal->payout_id)
                    ->update([
                        'status' => 'settlement',
                        'updated_at' => now(),
                    ]);
            } elseif ($status === 'failed') {
                $withdrawal->update([
                    'status' => 'rejected',
                    'rejection_reason' => $payoutData['failure_reason'] ?? 'Gagal diproses oleh bank',
                    'midtrans_response' => $payoutData,
                ]);

                $withdrawal->user->increment('balance', $withdrawal->amount);

                DB::table('transactions')
                    ->where('transaction_id', $withdrawal->payout_id)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $withdrawal->status,
                    'midtrans_status' => $status,
                    'reference_no' => $withdrawal->payout_id,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal memeriksa status penarikan dari Midtrans'
        ], 500);
    }

    /**
     * Get list of supported banks
     */
    public function getBanks()
    {
        $banks = [
            ['code' => 'BCA', 'name' => 'Bank Central Asia (BCA)'],
            ['code' => 'BNI', 'name' => 'Bank Negara Indonesia (BNI)'],
            ['code' => 'BRI', 'name' => 'Bank Rakyat Indonesia (BRI)'],
            ['code' => 'MANDIRI', 'name' => 'Bank Mandiri'],
            ['code' => 'CIMB', 'name' => 'CIMB Niaga'],
            ['code' => 'PERMATA', 'name' => 'Bank Permata'],
            ['code' => 'BNI SYARIAH', 'name' => 'BNI Syariah'],
            ['code' => 'BSI', 'name' => 'Bank Syariah Indonesia (BSI)'],
            ['code' => 'DANAMON', 'name' => 'Bank Danamon'],
            ['code' => 'MEGA', 'name' => 'Bank Mega'],
            ['code' => 'PANIN', 'name' => 'Bank Panin'],
            ['code' => 'MUAMALAT', 'name' => 'Bank Muamalat'],
            ['code' => 'OCBC', 'name' => 'OCBC NISP'],
            ['code' => 'MAYBANK', 'name' => 'Maybank Indonesia'],
            ['code' => 'BTPN', 'name' => 'Bank BTPN'],
            ['code' => 'JENIUS', 'name' => 'Jenius (BTPN)'],
            ['code' => 'SINARMAS', 'name' => 'Bank Sinarmas'],
        ];

        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }
}