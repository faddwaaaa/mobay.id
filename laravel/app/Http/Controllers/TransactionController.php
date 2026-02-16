<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUpRequest;
use App\Http\Requests\WithdrawRequest;
use App\Models\Withdrawal;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TransactionController - Handle top-up and withdrawal transactions
 */
class TransactionController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth');
    }

    /**
     * Show top-up page
     * GET /dashboard/topup
     */
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

    /**
     * Create top-up transaction
     * POST /api/topup
     */
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

    /**
     * Handle top-up success
     * GET /dashboard/topup/success
     */
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

    /**
     * Handle top-up error
     * GET /dashboard/topup/error
     */
    public function topupError(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) return redirect('/dashboard')->with('error', 'Transaction ID not found');

        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('dashboard.topup-error', compact('transaction'));
    }

    /**
     * Handle top-up pending
     * GET /dashboard/topup/pending
     */
    public function topupPending(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) return redirect('/dashboard')->with('error', 'Transaction ID not found');

        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('dashboard.topup-pending', compact('transaction'));
    }

    /**
     * Get transaction history
     * GET /api/transactions
     */
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

    /**
     * Show withdrawal form
     * GET /dashboard/withdraw
     */
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

    /**
     * Create withdrawal request
     * POST /withdrawal  ← cocok dengan form dashboard
     * POST /api/withdraw ← route lama (tetap berfungsi)
     */
    public function createWithdraw(WithdrawRequest $request)
    {
        try {
            $user   = auth()->user();
            $data   = $request->validated();
            $amount = (int) $data['amount'];

            // Validasi saldo cukup
            if ((int) $user->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak cukup. Saldo Anda: Rp ' . number_format($user->balance, 0, ',', '.'),
                ], 400);
            }

            // Validasi minimal
            $minAmount = config('midtrans.withdrawal.min_amount', 50000);
            if ($amount < $minAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimal penarikan Rp ' . number_format($minAmount, 0, ',', '.'),
                ], 400);
            }

            // Simpan & kurangi saldo dalam satu DB transaction
            $withdrawal = DB::transaction(function () use ($user, $data, $amount) {

                // 1. Kurangi saldo (hold)
                $user->decrement('balance', $amount);

                // 2. Simpan withdrawal request
                return Withdrawal::create([
                    'user_id'        => $user->id,
                    'amount'         => $amount,
                    'status'         => 'pending',
                    'bank_name'      => $data['bank_name'] ?? null,
                    'account_name'   => $data['account_name'] ?? null,
                    'account_number' => $data['account_number'] ?? null,
                    'notes'          => $data['notes'] ?? null,
                    'ip_address'     => request()->ip(),
                ]);
            });

            Log::info('Withdrawal request created', [
                'user_id'       => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'amount'        => $amount,
                'balance_now'   => $user->fresh()->balance,
            ]);

            return response()->json([
                'success'       => true,
                'withdrawal_id' => $withdrawal->id,
                'message'       => 'Permintaan penarikan berhasil diajukan! Saldo akan ditransfer dalam 1-3 hari kerja.',
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

    /**
     * Get withdrawal history
     * GET /api/withdrawals
     */
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