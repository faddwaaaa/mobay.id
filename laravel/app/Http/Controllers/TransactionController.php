<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUpRequest;
use App\Http\Requests\WithdrawRequest;
use App\Models\AdminWalletLedger;
use App\Models\Withdrawal;
use App\Models\User;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth');
    }

    /**
     * Show transaction history page
     * GET /riwayat
     */
    public function history()
    {
        $user = auth()->user();

        // Ambil transaksi pembayaran (checkout)
        $payments = \App\Models\Transaction::where('user_id', $user->id)
            ->whereIn('payment_method', ['gopay', 'qris', 'bca_va', 'bni_va', 'echannel', 'shopeepay', 'credit_card'])
            ->latest()
            ->paginate(10, ['*'], 'payments');

        // Ambil transaksi penarikan
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'withdrawals');

        return view('transactions.history', compact('payments', 'withdrawals'));
    }

    /**
     * Show payment detail page
     * GET /riwayat/pembayaran/{id}
     */
    public function paymentDetail($id)
    {
        $transaction = \App\Models\Transaction::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $notes = is_string($transaction->notes) 
            ? json_decode($transaction->notes, true) 
            : ($transaction->notes ?? []);

        return view('transactions.payment-detail', compact('transaction', 'notes'));
    }

    /**
     * Show withdrawal detail page
     * GET /riwayat/penarikan/{id}
     */
    public function withdrawalDetail($id)
    {
        $withdrawal = Withdrawal::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        return view('transactions.withdrawal-detail', compact('withdrawal'));
    }

    // ... method lain tetap sama (showTopupForm, createTopUp, dll)

    public function showTopupForm()
    {
        $user = auth()->user();
        return view('dashboard.topup', [
            'user'      => $user,
            'balance'   => $user->balance,
            'minAmount' => config('midtrans.topup.min_amount', 10000),
            'maxAmount' => config('midtrans.topup.max_amount', 10000000),
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function createTopUp(TopUpRequest $request)
    {
        try {
            $user      = auth()->user();
            $amount    = $request->validated('amount');
            $ipAddress = $request->ip();
            Log::info('Top-up requested', ['user_id' => $user->id, 'amount' => $amount]);
            $response = $this->paymentService->createTopUp($user, $amount, $ipAddress);
            return response()->json([
                'success'    => true,
                'snap_token' => $response['snap_token'],
                'message'    => 'Top-up transaction created successfully',
            ]);
        } catch (Exception $e) {
            Log::error('Top-up creation failed', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function topupSuccess(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) return redirect('/dashboard')->with('error', 'Transaction ID not found');
        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        return view('dashboard.topup-success', [
            'transaction' => $transaction,
            'balance'     => auth()->user()->balance,
        ]);
    }

    public function topupError(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) return redirect('/dashboard')->with('error', 'Transaction ID not found');
        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        return view('dashboard.topup-error', compact('transaction'));
    }

    public function topupPending(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) return redirect('/dashboard')->with('error', 'Transaction ID not found');
        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        return view('dashboard.topup-pending', compact('transaction'));
    }

    public function getTransactionHistory(Request $request)
    {
        $user         = auth()->user();
        $transactions = $user->transactions()->latest()->take($request->query('limit', 10))->get();
        return response()->json([
            'success'      => true,
            'balance'      => $user->balance,
            'transactions' => $transactions->map(fn ($t) => [
                'id'             => $t->id,
                'order_id'       => $t->order_id,
                'amount'         => $t->amount,
                'formatted_amount' => 'Rp ' . number_format($t->amount, 0, ',', '.'),
                'status'         => $t->status,
                'payment_method' => $t->payment_method,
                'created_at'     => $t->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    public function showWithdrawForm()
    {
        $user = auth()->user();
        return view('dashboard.withdraw', [
            'user'        => $user,
            'balance'     => $user->balance,
            'totalEarned' => $this->paymentService->getTotalEarned($user),
            'minAmount'   => config('midtrans.withdrawal.min_amount', 50000),
            'maxAmount'   => config('midtrans.withdrawal.max_amount', 50000000),
        ]);
    }

    public function createWithdraw(WithdrawRequest $request)
    {
        try {
            $user   = auth()->user();
            $data   = $request->validated();
            $amount = (int) $data['amount'];
            $withdrawFee = (int) config('payment.withdraw_fee', 3000);
            $totalDeduction = $amount + $withdrawFee;

            if ((int) $user->balance < $totalDeduction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak cukup. Total potongan (nominal + fee): Rp ' . number_format($totalDeduction, 0, ',', '.'),
                ], 400);
            }

            $minAmount = config('midtrans.withdrawal.min_amount', 50000);
            if ($amount < $minAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimal penarikan Rp ' . number_format($minAmount, 0, ',', '.'),
                ], 400);
            }

            $withdrawal = DB::transaction(function () use ($user, $data, $amount, $withdrawFee, $totalDeduction) {
                $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
                if ((int) $lockedUser->balance < $totalDeduction) {
                    throw new Exception('Saldo tidak cukup untuk nominal + fee penarikan.');
                }

                $lockedUser->decrement('balance', $totalDeduction);

                $noteParts = [];
                if (!empty($data['notes'])) {
                    $noteParts[] = trim((string) $data['notes']);
                }
                if ($withdrawFee > 0) {
                    $noteParts[] = 'Fee withdraw Rp ' . number_format($withdrawFee, 0, ',', '.');
                }

                $withdrawal = Withdrawal::create([
                    'user_id'        => $user->id,
                    'amount'         => $amount,
                    'status'         => 'pending',
                    'bank_name'      => $data['bank_name'] ?? null,
                    'account_name'   => $data['account_name'] ?? null,
                    'account_number' => $data['account_number'] ?? null,
                    'notes'          => !empty($noteParts) ? implode(' | ', $noteParts) : null,
                    'ip_address'     => request()->ip(),
                ]);

                if ($withdrawFee > 0) {
                    $lastBalance = (int) (AdminWalletLedger::query()
                        ->lockForUpdate()
                        ->latest('id')
                        ->value('balance_after') ?? 0);

                    AdminWalletLedger::create([
                        'source' => 'fee_withdraw',
                        'direction' => 'credit',
                        'amount' => $withdrawFee,
                        'balance_after' => $lastBalance + $withdrawFee,
                        'reference_type' => Withdrawal::class,
                        'reference_id' => $withdrawal->id,
                        'description' => 'Fee withdraw user #' . $user->id,
                        'created_by' => null,
                    ]);
                }

                return $withdrawal;
            });

            Log::info('Withdrawal request created', [
                'user_id'       => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'amount'        => $amount,
                'fee'           => $withdrawFee,
                'total_deduction' => $totalDeduction,
                'balance_now'   => $user->fresh()->balance,
            ]);

            return response()->json([
                'success'       => true,
                'withdrawal_id' => $withdrawal->id,
                'message'       => 'Permintaan penarikan berhasil diajukan. Biaya withdraw Rp ' . number_format($withdrawFee, 0, ',', '.') . ' telah dipotong.',
            ]);

        } catch (Exception $e) {
            Log::error('Withdrawal creation failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ], 400);
        }
    }

    public function getWithdrawalHistory(Request $request)
    {
        $user        = auth()->user();
        $withdrawals = $user->withdrawals()->latest()->take($request->query('limit', 10))->get();
        return response()->json([
            'success'     => true,
            'withdrawals' => $withdrawals->map(fn ($w) => [
                'id'             => $w->id,
                'amount'         => $w->amount,
                'formatted_amount' => 'Rp ' . number_format($w->amount, 0, ',', '.'),
                'status'         => $w->status,
                'bank_name'      => $w->bank_name,
                'account_number' => $w->account_number,
                'approved_at'    => $w->approved_at?->format('Y-m-d H:i:s'),
                'created_at'     => $w->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }
}
