<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PaymentService - Handle payment business logic
 * 
 * This service orchestrates payment operations:
 * - Creating top-up transactions
 * - Processing successful payments
 * - Handling payment failures
 * - Managing withdrawal requests
 */
class PaymentService
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Create top-up transaction
     * 
     * @param User $user
     * @param int $amount
     * @param string $ipAddress
     * @return array ['snap_token' => string, 'redirect_url' => string, 'transaction' => Transaction]
     * @throws Exception
     */
    public function createTopUp(User $user, int $amount, string $ipAddress): array
    {
        try {
            // Validate amount
            $this->validateTopUpAmount($amount);

            // Generate unique order ID
            $orderId = MidtransService::generateOrderId();

            // Create Snap transaction with Midtrans
            $snapResponse = $this->midtransService->createSnapTransaction(
                $orderId,
                $amount,
                $user->email,
                $user->name,
                [
                    'custom_field1' => 'topup',
                    'custom_field2' => $user->id,
                ]
            );

            // Create transaction record in database with pending status
            $transaction = DB::transaction(function () use ($user, $orderId, $amount, $ipAddress, $snapResponse) {
                return Transaction::create([
                    'user_id' => $user->id,
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'status' => 'pending',
                    'payment_method' => null, // Will be set after user selects method
                    'midtrans_response' => [
                        'snap_token' => $snapResponse['snap_token'] ?? null,
                        'redirect_url' => $snapResponse['redirect_url'] ?? null,
                    ],
                    'ip_address' => $ipAddress,
                    'notes' => 'Top-up request created',
                ]);
            });

            Log::info('Top-up transaction created', [
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $amount,
            ]);

            return [
                'snap_token' => $snapResponse['snap_token'],
                'redirect_url' => $snapResponse['redirect_url'],
                'transaction' => $transaction,
            ];
        } catch (Exception $e) {
            Log::error('Failed to create top-up transaction', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle successful payment (called from callback)
     * 
     * @param string $orderId
     * @param string $transactionId
     * @param string $paymentMethod
     * @param array $callbackData
     * @return bool
     * @throws Exception
     */
    public function handleSuccessfulPayment(
        string $orderId,
        string $transactionId,
        string $paymentMethod,
        array $callbackData = []
    ): bool {
        try {
            return DB::transaction(function () use ($orderId, $transactionId, $paymentMethod, $callbackData) {
                // Find transaction
                $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

                // Check if already processed
                if ($transaction->isSuccessful()) {
                    Log::warning('Payment already processed', [
                        'order_id' => $orderId,
                        'transaction_id' => $transaction->id,
                    ]);
                    return true;
                }

                // Update transaction
                $transaction->update([
                    'transaction_id' => $transactionId,
                    'status' => 'settlement',
                    'payment_method' => $paymentMethod,
                    'midtrans_response' => $callbackData,
                    'notes' => 'Payment successful - balance credited',
                ]);

                // Increment user balance
                $transaction->user->increment('balance', $transaction->amount);

                Log::info('Payment processed successfully', [
                    'user_id' => $transaction->user_id,
                    'order_id' => $orderId,
                    'amount' => $transaction->amount,
                    'new_balance' => $transaction->user->fresh()->balance,
                ]);

                return true;
            });
        } catch (Exception $e) {
            Log::error('Failed to process successful payment', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle failed payment
     * 
     * @param string $orderId
     * @param string $failureReason
     * @return bool
     * @throws Exception
     */
    public function handleFailedPayment(string $orderId, string $failureReason = ''): bool
    {
        try {
            return DB::transaction(function () use ($orderId, $failureReason) {
                $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

                // Don't update if already processed
                if ($transaction->isSuccessful()) {
                    return true;
                }

                // Update transaction status
                $transaction->update([
                    'status' => 'failed',
                    'notes' => 'Payment failed: ' . $failureReason,
                ]);

                Log::warning('Payment failed', [
                    'user_id' => $transaction->user_id,
                    'order_id' => $orderId,
                    'reason' => $failureReason,
                ]);

                return true;
            });
        } catch (Exception $e) {
            Log::error('Failed to process payment failure', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle expired transaction
     * 
     * @param string $orderId
     * @return bool
     * @throws Exception
     */
    public function handleExpiredTransaction(string $orderId): bool
    {
        try {
            return DB::transaction(function () use ($orderId) {
                $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

                // Only update if still pending
                if (!$transaction->isPending()) {
                    return true;
                }

                $transaction->update([
                    'status' => 'expired',
                    'notes' => 'Payment expired - no action taken',
                ]);

                Log::info('Transaction expired', [
                    'user_id' => $transaction->user_id,
                    'order_id' => $orderId,
                ]);

                return true;
            });
        } catch (Exception $e) {
            Log::error('Failed to process expired transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate top-up amount
     * 
     * @param int $amount
     * @throws Exception
     */
    protected function validateTopUpAmount(int $amount): void
    {
        $minAmount = config('midtrans.topup.min_amount', 10000);
        $maxAmount = config('midtrans.topup.max_amount', 10000000);

        if ($amount < $minAmount) {
            throw new Exception("Minimum top-up amount is Rp " . number_format($minAmount, 0, ',', '.'));
        }

        if ($amount > $maxAmount) {
            throw new Exception("Maximum top-up amount is Rp " . number_format($maxAmount, 0, ',', '.'));
        }
    }

    /**
     * Get transaction history for user
     * 
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserTransactionHistory(User $user, int $limit = 10)
    {
        return $user->transactions()
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get user balance
     * 
     * @param User $user
     * @return int
     */
    public function getUserBalance(User $user): int
    {
        return $user->fresh()->balance;
    }

    /**
     * Get total earned from successful transactions
     * 
     * @param User $user
     * @return int
     */
    public function getTotalEarned(User $user): int
    {
        return $user->transactions()
            ->successful()
            ->sum('amount');
    }
}
