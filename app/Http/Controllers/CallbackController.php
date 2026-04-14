<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * CallbackController - Handle Midtrans webhook callbacks
 * 
 * CRITICAL SECURITY:
 * - Always verify signature before processing
 * - This prevents unauthorized balance updates
 * - Never trust client-sent data, only Midtrans server
 * 
 * Midtrans will POST to this endpoint with payment status updates
 * Reference: https://docs.midtrans.com/en/http-notification
 */
class CallbackController extends Controller
{
    protected MidtransService $midtransService;
    protected PaymentService $paymentService;

    public function __construct(
        MidtransService $midtransService,
        PaymentService $paymentService
    ) {
        $this->midtransService = $midtransService;
        $this->paymentService = $paymentService;
    }

    /**
     * Handle Midtrans callback notification
     * 
     * POST /api/callback/midtrans
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleMidtransCallback(Request $request)
    {
        try {
            // Get callback data
            $data = $request->all();

            Log::info('Midtrans callback received', [
                'order_id' => $data['order_id'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
                'transaction_status' => $data['transaction_status'] ?? null,
            ]);

            // Validate required fields
            if (empty($data['order_id']) || empty($data['transaction_status'])) {
                Log::error('Callback missing required fields', ['data' => $data]);
                return response('Invalid callback data', Response::HTTP_BAD_REQUEST);
            }

            // ============================================
            // CRITICAL: Verify Midtrans signature
            // ============================================
            // This is the most important security check
            // Never process callback without signature verification!
            
            $isValid = $this->midtransService->verifyCallbackSignature(
                (string) $data['order_id'],
                (string) $data['transaction_status'],
                (string) ($data['gross_amount'] ?? 0),
                (string) ($data['signature_key'] ?? '')
            );

            if (!$isValid) {
                Log::warning('Invalid callback signature', [
                    'order_id' => $data['order_id'],
                    'ip_address' => $request->ip(),
                ]);
                return response('Invalid signature', Response::HTTP_UNAUTHORIZED);
            }

            // ============================================
            // Process based on transaction_status
            // ============================================

            $transactionStatus = $data['transaction_status'];
            $orderId = $data['order_id'];

            switch ($transactionStatus) {
                case 'settlement':
                    // Payment successful - credit balance
                    $this->handleSettlement($data);
                    break;

                case 'pending':
                    // Payment pending - user hasn't completed payment yet
                    Log::info('Transaction pending', ['order_id' => $orderId]);
                    break;

                case 'deny':
                    // Payment denied
                    $this->handleDeny($data);
                    break;

                case 'cancel':
                    // Payment cancelled
                    $this->handleCancel($data);
                    break;

                case 'expire':
                    // Payment expired
                    $this->handleExpire($data);
                    break;

                case 'failure':
                    // Payment failed
                    $this->handleFailure($data);
                    break;

                default:
                    Log::warning('Unknown transaction status', [
                        'status' => $transactionStatus,
                        'order_id' => $orderId,
                    ]);
            }

            // Always respond with 200 OK to Midtrans
            // Midtrans will retry if we don't respond with 200
            return response('OK', Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error processing Midtrans callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Still return 200 to prevent Midtrans retries on our errors
            return response('OK', Response::HTTP_OK);
        }
    }

    /**
     * Handle settlement (successful payment)
     * 
     * @param array $data
     */
    protected function handleSettlement(array $data): void
    {
        try {
            $orderId = $data['order_id'];
            $transactionId = $data['transaction_id'];
            $paymentType = $data['payment_type'] ?? 'unknown';

            Log::info('Processing settlement', [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
            ]);

            // Process the payment
            $this->paymentService->handleSuccessfulPayment(
                $orderId,
                $transactionId,
                $paymentType,
                $data
            );

            Log::info('Settlement processed successfully', [
                'order_id' => $orderId,
            ]);
        } catch (Exception $e) {
            Log::error('Error processing settlement', [
                'order_id' => $data['order_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle deny (payment denied by payment provider)
     * 
     * @param array $data
     */
    protected function handleDeny(array $data): void
    {
        try {
            $orderId = $data['order_id'];

            Log::warning('Payment denied', [
                'order_id' => $orderId,
                'fraud_status' => $data['fraud_status'] ?? null,
            ]);

            $this->paymentService->handleFailedPayment(
                $orderId,
                'Payment denied by provider'
            );
        } catch (Exception $e) {
            Log::error('Error processing deny', [
                'order_id' => $data['order_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle cancel (payment cancelled by user)
     * 
     * @param array $data
     */
    protected function handleCancel(array $data): void
    {
        try {
            $orderId = $data['order_id'];

            Log::info('Payment cancelled by user', ['order_id' => $orderId]);

            $this->paymentService->handleFailedPayment(
                $orderId,
                'Payment cancelled by user'
            );
        } catch (Exception $e) {
            Log::error('Error processing cancel', [
                'order_id' => $data['order_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle expire (payment expired - user took too long)
     * 
     * @param array $data
     */
    protected function handleExpire(array $data): void
    {
        try {
            $orderId = $data['order_id'];

            Log::info('Payment expired', ['order_id' => $orderId]);

            $this->paymentService->handleExpiredTransaction($orderId);
        } catch (Exception $e) {
            Log::error('Error processing expire', [
                'order_id' => $data['order_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failure (payment processing failed)
     * 
     * @param array $data
     */
    protected function handleFailure(array $data): void
    {
        try {
            $orderId = $data['order_id'];

            Log::warning('Payment processing failed', ['order_id' => $orderId]);

            $this->paymentService->handleFailedPayment(
                $orderId,
                'Payment processing failed'
            );
        } catch (Exception $e) {
            Log::error('Error processing failure', [
                'order_id' => $data['order_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
