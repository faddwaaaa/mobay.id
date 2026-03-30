<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle Midtrans Iris payout webhook/callback
     */
    public function handleIrisCallback(Request $request)
    {
        try {
            // Log incoming webhook
            Log::info('Midtrans Iris Webhook Received:', $request->all());

            // Verify signature (optional but recommended)
            $serverKey = config('midtrans.server_key');
            $reference_no = $request->reference_no;
            $status = $request->status;
            $amount = $request->amount;
            
            // Get signature from header or request
            $signature = $request->header('X-Callback-Signature') ?? $request->signature;
            
            // Verify signature
            $calculatedSignature = hash('sha512', $reference_no . $status . $amount . $serverKey);
            
            if ($signature && $signature !== $calculatedSignature) {
                Log::warning('Invalid Midtrans webhook signature');
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // Find withdrawal by payout_id (reference_no)
            $withdrawal = Withdrawal::where('payout_id', $reference_no)->first();

            if (!$withdrawal) {
                Log::warning('Withdrawal not found for reference_no: ' . $reference_no);
                return response()->json(['message' => 'Withdrawal not found'], 404);
            }

            DB::beginTransaction();
            try {
                // Update withdrawal status based on Midtrans status
                $newStatus = $this->mapMidtransStatus($status);
                
                $withdrawal->update([
                    'status' => $newStatus,
                    'midtrans_response' => $request->all(),
                ]);

                // If failed, refund balance to user
                if ($newStatus === 'rejected') {
                    $withdrawal->user->increment('balance', $withdrawal->amount);
                    $withdrawal->update([
                        'rejection_reason' => $request->failure_reason ?? 'Gagal diproses oleh bank',
                    ]);

                    // Update transaction
                    DB::table('transactions')
                        ->where('transaction_id', $reference_no)
                        ->update([
                            'status' => 'failed',
                            'notes' => 'Penarikan gagal: ' . ($request->failure_reason ?? 'Unknown'),
                            'updated_at' => now(),
                        ]);
                }

                // If completed, update transaction
                if ($newStatus === 'completed') {
                    DB::table('transactions')
                        ->where('transaction_id', $reference_no)
                        ->update([
                            'status' => 'settlement',
                            'updated_at' => now(),
                        ]);
                }

                DB::commit();

                Log::info('Withdrawal updated successfully', [
                    'withdrawal_id' => $withdrawal->id,
                    'reference_no' => $reference_no,
                    'new_status' => $newStatus,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processed successfully'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing Midtrans webhook: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing webhook'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Webhook error'
            ], 500);
        }
    }

    /**
     * Map Midtrans status to internal status
     */
    private function mapMidtransStatus($midtransStatus): string
    {
        return match($midtransStatus) {
            'queued' => 'approved',
            'processing' => 'approved',
            'processed' => 'completed',
            'completed' => 'completed',
            'failed' => 'rejected',
            'cancelled' => 'cancelled',
            default => 'approved',
        };
    }

    /**
     * Handle Snap/Core API transaction notification (for top-up)
     */
    public function handleTransactionNotification(Request $request)
    {
        try {
            Log::info('Midtrans Transaction Notification:', $request->all());

            $serverKey = config('midtrans.server_key');
            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $signatureKey = $request->signature_key;

            // Verify signature
            $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signatureKey !== $calculatedSignature) {
                Log::warning('Invalid transaction notification signature');
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $transactionStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status ?? null;

            // Find transaction
            $transaction = DB::table('transactions')
                ->where('order_id', $orderId)
                ->first();

            if (!$transaction) {
                Log::warning('Transaction not found: ' . $orderId);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            DB::beginTransaction();
            try {
                // Update transaction based on status
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $newStatus = 'settlement';
                    } else {
                        $newStatus = 'pending';
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $newStatus = 'settlement';
                    
                    // Add balance to user if this is a top-up
                    if ($transaction->payment_method !== 'withdrawal') {
                        $user = DB::table('users')->where('id', $transaction->user_id)->first();
                        if ($user && $transaction->status !== 'settlement') {
                            DB::table('users')
                                ->where('id', $transaction->user_id)
                                ->increment('balance', $grossAmount);
                        }
                    }
                } elseif ($transactionStatus == 'pending') {
                    $newStatus = 'pending';
                } elseif ($transactionStatus == 'deny') {
                    $newStatus = 'denied';
                } elseif ($transactionStatus == 'expire') {
                    $newStatus = 'expired';
                } elseif ($transactionStatus == 'cancel') {
                    $newStatus = 'cancelled';
                } else {
                    $newStatus = $transactionStatus;
                }

                // Update transaction
                DB::table('transactions')
                    ->where('order_id', $orderId)
                    ->update([
                        'status' => $newStatus,
                        'midtrans_response' => json_encode($request->all()),
                        'updated_at' => now(),
                    ]);

                DB::commit();

                Log::info('Transaction notification processed', [
                    'order_id' => $orderId,
                    'new_status' => $newStatus,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Notification processed'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Transaction Notification Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing notification'
            ], 500);
        }
    }

    /**
     * Handle Midtrans Disbursement API webhook/callback
     */
    public function handleDisbursementCallback(Request $request)
    {
        try {
            // Log incoming webhook
            Log::info('Midtrans Disbursement Webhook Received:', $request->all());

            $disbursementId = $request->id;
            $status = $request->status;
            $amount = $request->amount;

            // Verify signature for Disbursement API
            $disbursementApiKey = config('midtrans.disbursement_api_key');
            $signature = $request->header('X-Midtrans-Signature') ?? $request->signature;

            // Disbursement API signature format: sha512(id + status + amount + disbursement_api_key)
            $calculatedSignature = hash('sha512', $disbursementId . $status . $amount . $disbursementApiKey);

            if ($signature && $signature !== $calculatedSignature) {
                Log::warning('Invalid Midtrans Disbursement webhook signature', [
                    'received' => $signature,
                    'calculated' => $calculatedSignature,
                    'disbursement_id' => $disbursementId
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // Find withdrawal by payout_id (disbursement ID)
            $withdrawal = Withdrawal::where('payout_id', $disbursementId)->first();

            if (!$withdrawal) {
                Log::warning('Withdrawal not found for disbursement_id: ' . $disbursementId);
                return response()->json(['message' => 'Withdrawal not found'], 404);
            }

            DB::beginTransaction();
            try {
                // Map Disbursement API status to internal status
                $newStatus = $this->mapDisbursementStatus($status);

                $withdrawal->update([
                    'status' => $newStatus,
                    'midtrans_response' => $request->all(),
                ]);

                // If failed, refund balance to user
                if ($newStatus === 'rejected') {
                    $withdrawal->user->increment('balance', $withdrawal->amount);
                    $withdrawal->update([
                        'rejection_reason' => $request->failure_reason ?? 'Gagal diproses oleh bank',
                    ]);

                    // Update transaction
                    DB::table('transactions')
                        ->where('transaction_id', $disbursementId)
                        ->update([
                            'status' => 'failed',
                            'notes' => 'Penarikan gagal: ' . ($request->failure_reason ?? 'Unknown'),
                            'updated_at' => now(),
                        ]);

                    // Create refund ledger entry
                    \App\Models\Ledger::create([
                        'transaction_type' => 'withdrawal_refund',
                        'reference_id' => 'WD-' . $withdrawal->id,
                        'user_id' => $withdrawal->user_id,
                        'amount' => $withdrawal->amount,
                        'description' => 'Refund for failed withdrawal',
                        'metadata' => [
                            'disbursement_id' => $disbursementId,
                            'failure_reason' => $request->failure_reason ?? 'Unknown',
                        ],
                    ]);
                }

                // If completed, update transaction
                if ($newStatus === 'completed') {
                    DB::table('transactions')
                        ->where('transaction_id', $disbursementId)
                        ->update([
                            'status' => 'settlement',
                            'updated_at' => now(),
                        ]);

                    // Create completion ledger entry
                    \App\Models\Ledger::create([
                        'transaction_type' => 'withdrawal_completed',
                        'reference_id' => 'WD-' . $withdrawal->id,
                        'user_id' => $withdrawal->user_id,
                        'amount' => -$withdrawal->disbursement_amount,
                        'description' => 'Withdrawal completed to bank account',
                        'metadata' => [
                            'disbursement_id' => $disbursementId,
                            'bank_name' => $withdrawal->bank_name,
                            'account_number' => $withdrawal->account_number,
                        ],
                    ]);
                }

                DB::commit();

                Log::info('Disbursement webhook processed successfully', [
                    'withdrawal_id' => $withdrawal->id,
                    'disbursement_id' => $disbursementId,
                    'new_status' => $newStatus,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Disbursement webhook processed successfully'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing Disbursement webhook: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Error processing webhook'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Midtrans Disbursement Webhook Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Webhook error'
            ], 500);
        }
    }

    /**
     * Map Disbursement API status to internal status
     */
    private function mapDisbursementStatus($disbursementStatus): string
    {
        return match(strtolower($disbursementStatus)) {
            'queued' => 'approved',
            'processing' => 'approved',
            'processed' => 'completed',
            'completed' => 'completed',
            'failed' => 'rejected',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            default => 'approved',
        };
    }
}
