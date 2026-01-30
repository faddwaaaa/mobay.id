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
 * 
 * This controller orchestrates payment operations:
 * - Create top-up transactions
 * - Handle top-up responses
 * - Create withdrawal requests
 * - View transaction history
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
        $minAmount = config('midtrans.topup.min_amount', 10000);
        $maxAmount = config('midtrans.topup.max_amount', 10000000);

        return view('dashboard.topup', [
            'user' => $user,
            'balance' => $user->balance,
            'minAmount' => $minAmount,
            'maxAmount' => $maxAmount,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    /**
     * Create top-up transaction
     * POST /api/topup
     * 
     * @param TopUpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTopUp(TopUpRequest $request)
    {
        try {
            $user = auth()->user();
            $amount = $request->validated('amount');
            $ipAddress = $request->ip();

            Log::info('Top-up requested', [
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            // Create top-up transaction
            $response = $this->paymentService->createTopUp($user, $amount, $ipAddress);

            return response()->json([
                'success' => true,
                'snap_token' => $response['snap_token'],
                'message' => 'Top-up transaction created successfully',
            ]);
        } catch (Exception $e) {
            Log::error('Top-up creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Handle top-up success
     * GET /dashboard/topup/success
     */
    public function topupSuccess(Request $request)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect('/dashboard')->with('error', 'Transaction ID not found');
        }

        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('dashboard.topup-success', [
            'transaction' => $transaction,
            'balance' => auth()->user()->balance,
        ]);
    }

    /**
     * Handle top-up error
     * GET /dashboard/topup/error
     */
    public function topupError(Request $request)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect('/dashboard')->with('error', 'Transaction ID not found');
        }

        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('dashboard.topup-error', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Handle top-up pending
     * GET /dashboard/topup/pending
     */
    public function topupPending(Request $request)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect('/dashboard')->with('error', 'Transaction ID not found');
        }

        $transaction = \App\Models\Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('dashboard.topup-pending', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Get transaction history
     * GET /api/transactions
     */
    public function getTransactionHistory(Request $request)
    {
        $user = auth()->user();
        $limit = $request->query('limit', 10);

        $transactions = $user->transactions()
            ->latest()
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'balance' => $user->balance,
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'amount' => $transaction->amount,
                    'formatted_amount' => $transaction->formattedAmount(),
                    'status' => $transaction->status,
                    'payment_method' => $transaction->payment_method,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Show withdrawal form
     * GET /dashboard/withdraw
     */
    public function showWithdrawForm()
    {
        $user = auth()->user();
        $minAmount = config('midtrans.withdrawal.min_amount', 50000);
        $maxAmount = config('midtrans.withdrawal.max_amount', 50000000);

        // Check if user has earned from links
        $totalEarned = $this->paymentService->getTotalEarned($user);

        return view('dashboard.withdraw', [
            'user' => $user,
            'balance' => $user->balance,
            'totalEarned' => $totalEarned,
            'minAmount' => $minAmount,
            'maxAmount' => $maxAmount,
        ]);
    }

    /**
     * Create withdrawal request
     * POST /api/withdraw
     * 
     * @param WithdrawRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createWithdraw(WithdrawRequest $request)
    {
        try {
            $user = auth()->user();
            $data = $request->validated();
            $amount = $data['amount'];

            // Validate sufficient balance
            if ($user->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak cukup untuk penarikan ini',
                ], 400);
            }

            // Create withdrawal request
            $withdrawal = DB::transaction(function () use ($user, $data) {
                return Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $data['amount'],
                    'bank_name' => $data['bank_name'] ?? null,
                    'account_name' => $data['account_name'] ?? null,
                    'account_number' => $data['account_number'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'ip_address' => request()->ip(),
                    'status' => 'pending',
                ]);
            });

            Log::info('Withdrawal request created', [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'amount' => $amount,
            ]);

            return response()->json([
                'success' => true,
                'withdrawal_id' => $withdrawal->id,
                'message' => 'Permintaan penarikan berhasil dibuat. Menunggu persetujuan admin.',
            ]);
        } catch (Exception $e) {
            Log::error('Withdrawal creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get withdrawal history
     * GET /api/withdrawals
     */
    public function getWithdrawalHistory(Request $request)
    {
        $user = auth()->user();
        $limit = $request->query('limit', 10);

        $withdrawals = $user->withdrawals()
            ->latest()
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'withdrawals' => $withdrawals->map(function ($withdrawal) {
                return [
                    'id' => $withdrawal->id,
                    'amount' => $withdrawal->amount,
                    'formatted_amount' => $withdrawal->formattedAmount(),
                    'status' => $withdrawal->status,
                    'bank_name' => $withdrawal->bank_name,
                    'account_number' => $withdrawal->account_number,
                    'approved_at' => $withdrawal->approved_at?->format('Y-m-d H:i:s'),
                    'created_at' => $withdrawal->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }
}
