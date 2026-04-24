<?php

namespace App\Services;

use App\Models\AdminWalletLedger;
use App\Models\Ledger;
use App\Models\PaymentAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
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
    protected XenditPayoutService $xenditPayoutService;

    public function __construct(
        MidtransService $midtransService,
        XenditPayoutService $xenditPayoutService
    )
    {
        $this->midtransService = $midtransService;
        $this->xenditPayoutService = $xenditPayoutService;
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

    public function createWithdrawal(User $user, array $data, string $ipAddress): array
    {
        $amount = (int) ($data['amount'] ?? 0);
        $summary = $this->calculateWithdrawalSummary($amount);
        $this->validateWithdrawalAmount($amount, (int) $user->balance);

        $paymentAccount = PaymentAccount::query()
            ->where('user_id', $user->id)
            ->whereKey($data['payment_account_id'] ?? null)
            ->first();

        if (!$paymentAccount) {
            throw new Exception('Rekening tujuan tidak ditemukan.');
        }

        $pendingExists = Withdrawal::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($pendingExists) {
            throw new Exception('Masih ada penarikan yang sedang diproses.');
        }

        if ($summary['received_amount'] <= 0) {
            throw new Exception('Nominal penarikan harus lebih besar dari biaya withdraw.');
        }

        $externalId = $this->generateWithdrawalExternalId($user->id);

        return DB::transaction(function () use ($user, $data, $ipAddress, $summary, $paymentAccount, $externalId) {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ((int) $lockedUser->balance < $summary['amount']) {
                throw new Exception('Saldo tidak cukup untuk melakukan penarikan.');
            }

            $payoutResult = $this->xenditPayoutService->createPayout([
                'external_id' => $externalId,
                'amount' => $summary['received_amount'],
                'account_name' => $paymentAccount->account_holder,
                'account_number' => $paymentAccount->account_number,
                'bank_name' => $paymentAccount->bank_code ?: $paymentAccount->bank_name,
                'email' => $lockedUser->email,
                'notes' => $data['notes'] ?? 'Penarikan saldo Payou.id',
            ]);

            if (!($payoutResult['success'] ?? false)) {
                throw new Exception($payoutResult['message'] ?? 'Gagal membuat disbursement Xendit.');
            }

            $payoutPayload = $payoutResult['data'] ?? [];
            $payoutId = $payoutPayload['id'] ?? ($payoutResult['reference_no'] ?? null);
            $withdrawalStatus = $this->mapXenditPayoutStatus($payoutPayload['status'] ?? null);
            $transactionStatus = $withdrawalStatus === 'completed' ? 'settlement' : 'pending';

            $withdrawal = Withdrawal::create([
                'user_id' => $lockedUser->id,
                'amount' => $summary['amount'],
                'fee' => $summary['fee'],
                'disbursement_amount' => $summary['received_amount'],
                'status' => $withdrawalStatus,
                'bank_name' => $paymentAccount->bank_name,
                'account_name' => $paymentAccount->account_holder,
                'account_number' => $paymentAccount->account_number,
                'payout_id' => $payoutId,
                'midtrans_response' => [
                    'gateway' => 'xendit',
                    'external_id' => $externalId,
                    'payload' => $payoutPayload,
                ],
                'approved_by' => $lockedUser->id,
                'approved_at' => now(),
                'notes' => $data['notes'] ?? null,
                'ip_address' => $ipAddress,
            ]);

            $lockedUser->decrement('balance', $summary['amount']);

            Transaction::create([
                'user_id' => $lockedUser->id,
                'order_id' => $externalId,
                'transaction_id' => $payoutId,
                'amount' => $summary['amount'],
                'status' => $transactionStatus,
                'payment_method' => 'xendit_disbursement',
                'midtrans_response' => [
                    'gateway' => 'xendit',
                    'type' => 'disbursement',
                    'external_id' => $externalId,
                    'payload' => $payoutPayload,
                ],
                'notes' => json_encode([
                    'type' => 'withdrawal',
                    'withdrawal_id' => $withdrawal->id,
                    'payment_account_id' => $paymentAccount->id,
                    'fee' => $summary['fee'],
                    'disbursement_amount' => $summary['received_amount'],
                    'bank_code' => $paymentAccount->bank_code,
                    'bank_name' => $paymentAccount->bank_name,
                    'account_last4' => $paymentAccount->account_number_last4,
                    'gateway' => 'xendit',
                    'note' => $data['notes'] ?? null,
                ], JSON_UNESCAPED_UNICODE),
                'ip_address' => $ipAddress,
            ]);

            Ledger::create([
                'transaction_type' => 'withdrawal_request',
                'reference_id' => 'WD-' . $withdrawal->id,
                'user_id' => $lockedUser->id,
                'amount' => -$summary['amount'],
                'description' => 'Withdrawal request via Xendit disbursement',
                'metadata' => [
                    'gateway' => 'xendit',
                    'payout_id' => $payoutId,
                    'external_id' => $externalId,
                    'bank_code' => $paymentAccount->bank_code,
                    'account_last4' => $paymentAccount->account_number_last4,
                    'fee' => $summary['fee'],
                    'disbursement_amount' => $summary['received_amount'],
                ],
            ]);

            $this->creditWithdrawalFee($withdrawal, $summary['fee'], $lockedUser->id);

            if ($withdrawalStatus === 'completed') {
                Ledger::create([
                    'transaction_type' => 'withdrawal_completed',
                    'reference_id' => 'WD-' . $withdrawal->id,
                    'user_id' => $lockedUser->id,
                    'amount' => -$summary['received_amount'],
                    'description' => 'Withdrawal completed via Xendit disbursement',
                    'metadata' => [
                        'gateway' => 'xendit',
                        'payout_id' => $payoutId,
                        'external_id' => $externalId,
                    ],
                ]);
            }

            Log::info('Withdrawal created through Xendit', [
                'user_id' => $lockedUser->id,
                'withdrawal_id' => $withdrawal->id,
                'payout_id' => $payoutId,
                'external_id' => $externalId,
                'status' => $withdrawalStatus,
            ]);

            return [
                'withdrawal' => $withdrawal->fresh(),
                'summary' => $summary,
                'payout' => $payoutPayload,
                'external_id' => $externalId,
            ];
        });
    }

    public function handleXenditPayoutWebhook(array $payload): array
    {
        $data = $payload['data'] ?? $payload;
        $payoutId = $data['id'] ?? $payload['id'] ?? null;
        $externalId = $data['external_id'] ?? $payload['external_id'] ?? null;

        if (!$payoutId && !$externalId) {
            throw new Exception('Payout identifier tidak ditemukan.');
        }

        return DB::transaction(function () use ($payload, $data, $payoutId, $externalId) {
            $withdrawal = null;

            if ($payoutId) {
                $withdrawal = Withdrawal::query()
                    ->where('payout_id', $payoutId)
                    ->lockForUpdate()
                    ->first();
            }

            if (!$withdrawal && $externalId) {
                $withdrawal = Withdrawal::query()
                    ->where('midtrans_response->external_id', $externalId)
                    ->lockForUpdate()
                    ->first();
            }

            if (!$withdrawal && $externalId) {
                $transaction = Transaction::query()
                    ->where('order_id', $externalId)
                    ->lockForUpdate()
                    ->first();

                if ($transaction) {
                    $notes = json_decode((string) $transaction->notes, true);
                    $withdrawal = Withdrawal::query()
                        ->whereKey($notes['withdrawal_id'] ?? null)
                        ->lockForUpdate()
                        ->first();
                }
            }

            if (!$withdrawal) {
                throw new Exception('Withdrawal tidak ditemukan untuk callback payout.');
            }

            $newStatus = $this->mapXenditPayoutStatus($data['status'] ?? null);
            $currentPayload = is_array($withdrawal->midtrans_response) ? $withdrawal->midtrans_response : [];
            $withdrawal->update([
                'status' => $newStatus,
                'payout_id' => $payoutId ?: $withdrawal->payout_id,
                'midtrans_response' => array_merge($currentPayload, [
                    'gateway' => 'xendit',
                    'external_id' => $externalId,
                    'payload' => $data,
                    'last_webhook' => $payload,
                ]),
                'rejection_reason' => $this->extractPayoutFailureReason($data),
            ]);

            $transaction = Transaction::query()
                ->where('order_id', $externalId ?: ($currentPayload['external_id'] ?? null))
                ->lockForUpdate()
                ->first();

            if ($transaction) {
                $transaction->update([
                    'transaction_id' => $payoutId ?: $transaction->transaction_id,
                    'status' => $newStatus === 'completed' ? 'settlement' : ($newStatus === 'rejected' ? 'failed' : $transaction->status),
                    'midtrans_response' => [
                        'gateway' => 'xendit',
                        'type' => 'disbursement',
                        'external_id' => $externalId,
                        'payload' => $data,
                        'last_webhook' => $payload,
                    ],
                ]);
            }

            if ($newStatus === 'rejected') {
                $this->refundFailedWithdrawal($withdrawal, $data, $transaction);
            }

            if ($newStatus === 'completed') {
                $alreadyLogged = Ledger::query()
                    ->where('transaction_type', 'withdrawal_completed')
                    ->where('reference_id', 'WD-' . $withdrawal->id)
                    ->exists();

                if (!$alreadyLogged) {
                    Ledger::create([
                        'transaction_type' => 'withdrawal_completed',
                        'reference_id' => 'WD-' . $withdrawal->id,
                        'user_id' => $withdrawal->user_id,
                        'amount' => -$withdrawal->disbursement_amount,
                        'description' => 'Withdrawal completed via Xendit disbursement',
                        'metadata' => [
                            'gateway' => 'xendit',
                            'payout_id' => $withdrawal->payout_id,
                            'external_id' => $externalId,
                        ],
                    ]);
                }
            }

            return [
                'withdrawal_id' => $withdrawal->id,
                'status' => $newStatus,
            ];
        });
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

    public function calculateWithdrawalSummary(int $amount): array
    {
        $fee = (int) config('payment.withdraw_fee', config('xendit.disbursement.fee_flat', 6660));

        return [
            'amount' => $amount,
            'fee' => $fee,
            'received_amount' => max(0, $amount - $fee),
            'total_deduction' => $amount,
        ];
    }

    protected function validateWithdrawalAmount(int $amount, int $balance): void
    {
        $minAmount = (int) config('xendit.withdrawal.min_amount', 20000);
        $maxAmount = (int) config('xendit.withdrawal.max_amount', 50000000);

        if ($amount < $minAmount) {
            throw new Exception('Minimal penarikan Rp ' . number_format($minAmount, 0, ',', '.'));
        }

        if ($amount > $maxAmount) {
            throw new Exception('Maksimal penarikan Rp ' . number_format($maxAmount, 0, ',', '.'));
        }

        if ($amount > $balance) {
            throw new Exception('Saldo tidak cukup untuk melakukan penarikan.');
        }
    }

    protected function mapXenditPayoutStatus(?string $status): string
    {
        return match (strtoupper((string) $status)) {
            'SUCCEEDED', 'COMPLETED' => 'completed',
            'FAILED', 'REJECTED', 'CANCELLED' => 'rejected',
            'PENDING', 'ACCEPTED', 'PROCESSING', 'REQUIRES_ACTION' => 'approved',
            default => 'pending',
        };
    }

    protected function extractPayoutFailureReason(array $data): ?string
    {
        return $data['failure_code']
            ?? $data['failure_reason']
            ?? $data['status_message']
            ?? $data['message']
            ?? null;
    }

    protected function creditWithdrawalFee(Withdrawal $withdrawal, int $fee, ?int $createdBy = null): void
    {
        if ($fee <= 0) {
            return;
        }

        $lastBalance = (int) (AdminWalletLedger::query()
            ->lockForUpdate()
            ->latest('id')
            ->value('balance_after') ?? 0);

        AdminWalletLedger::create([
            'source' => 'fee_withdraw_xendit',
            'direction' => 'credit',
            'amount' => $fee,
            'balance_after' => $lastBalance + $fee,
            'reference_type' => Withdrawal::class,
            'reference_id' => $withdrawal->id,
            'description' => 'Fee withdraw user #' . $withdrawal->user_id,
            'created_by' => $createdBy,
        ]);
    }

    protected function refundFailedWithdrawal(Withdrawal $withdrawal, array $data, ?Transaction $transaction): void
    {
        $alreadyRefunded = Ledger::query()
            ->where('transaction_type', 'withdrawal_refund')
            ->where('reference_id', 'WD-' . $withdrawal->id)
            ->exists();

        if ($alreadyRefunded) {
            return;
        }

        $withdrawal->user()->increment('balance', (int) $withdrawal->amount);

        Ledger::create([
            'transaction_type' => 'withdrawal_refund',
            'reference_id' => 'WD-' . $withdrawal->id,
            'user_id' => $withdrawal->user_id,
            'amount' => $withdrawal->amount,
            'description' => 'Refund for failed Xendit withdrawal',
            'metadata' => [
                'gateway' => 'xendit',
                'payout_id' => $withdrawal->payout_id,
                'failure_reason' => $this->extractPayoutFailureReason($data),
            ],
        ]);

        $feeLedger = AdminWalletLedger::query()
            ->lockForUpdate()
            ->where('reference_type', Withdrawal::class)
            ->where('reference_id', $withdrawal->id)
            ->where('source', 'fee_withdraw_xendit')
            ->latest('id')
            ->first();

        if ($feeLedger && (int) $withdrawal->fee > 0) {
            $lastBalance = (int) (AdminWalletLedger::query()
                ->lockForUpdate()
                ->latest('id')
                ->value('balance_after') ?? 0);

            AdminWalletLedger::create([
                'source' => 'fee_withdraw_xendit_refund',
                'direction' => 'debit',
                'amount' => (int) $withdrawal->fee,
                'balance_after' => $lastBalance - (int) $withdrawal->fee,
                'reference_type' => Withdrawal::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Refund fee withdraw user #' . $withdrawal->user_id,
                'created_by' => null,
            ]);
        }

        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'notes' => json_encode(array_merge(
                    json_decode((string) $transaction->notes, true) ?: [],
                    ['failure_reason' => $this->extractPayoutFailureReason($data)]
                ), JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    protected function generateWithdrawalExternalId(int $userId): string
    {
        return 'WD-' . $userId . '-' . now()->format('YmdHis') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
