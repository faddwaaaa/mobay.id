<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Services\XenditPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected XenditPaymentService $xenditService;

    public function __construct(XenditPaymentService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Create payment request via Xendit
     * POST /api/payment/create
     */
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'channel_code' => 'required|string',
                'amount' => 'required|integer|min:1000',
                'order_id' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'product_id' => 'nullable|integer',
                'user_id' => 'nullable|integer',
                'buyer_address' => 'nullable|string',
                'qty' => 'nullable|integer|min:1',
            ]);

            // Check if transaction already exists
            $existingTransaction = Transaction::where('order_id', $validated['order_id'])->first();
            if ($existingTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order sudah pernah dibuat.',
                ], 422);
            }

            // Get product & seller ID
            $product = null;
            $userId = $validated['user_id'] ?? null;
            if (!empty($validated['product_id'])) {
                $product = Product::find($validated['product_id']);
                if ($product) {
                    $userId = $product->user_id;
                }
            }

            // Create transaction record first
            $transaction = Transaction::create([
                'user_id' => $userId,
                'order_id' => $validated['order_id'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['channel_code'],
                'status' => 'pending',
                'notes' => json_encode([
                    'buyer_name' => $validated['name'],
                    'buyer_email' => $validated['email'],
                    'buyer_phone' => $validated['phone'] ?? null,
                    'product_id' => $validated['product_id'] ?? null,
                    'buyer_address' => $validated['buyer_address'] ?? null,
                    'qty' => $validated['qty'] ?? 1,
                ]),
                'ip_address' => $request->ip(),
            ]);

            $paymentResponse = $this->xenditService->createPaymentRequest([
                'reference_id' => $validated['order_id'],  // Unique ID from system
                'description' => 'Payment Order ' . $validated['order_id'],
                'amount' => (int) $validated['amount'],
                'currency' => 'IDR',  // Required
                'channel_code' => $validated['channel_code'],
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'] ?? null,
                'customer_id' => $validated['user_id'] ?? null,
                'success_url' => config('app.url') . '/payment/success?order_id=' . $validated['order_id'],
                'failure_url' => config('app.url') . '/payment/failed?order_id=' . $validated['order_id'],
                'metadata' => [
                    'order_id' => $validated['order_id'],
                    'product_id' => $validated['product_id'] ?? null,
                ],
            ]);

            if (!$paymentResponse['success']) {
                $transaction->update(['status' => 'failed']);
                Log::error('Xendit payment request creation failed', $paymentResponse);
                return response()->json([
                    'success' => false,
                    'message' => $paymentResponse['message'] ?? 'Gagal membuat payment request',
                ], 500);
            }

            $transaction->update([
                'transaction_id' => $paymentResponse['data']['payment_request_id'] ?? $paymentResponse['data']['id'] ?? null,
                'midtrans_response' => $paymentResponse['data'],
            ]);

            $actions = $this->extractActions($paymentResponse['data'], $validated['channel_code']);

            return response()->json([
                'success' => true,
                'payment_request_id' => $paymentResponse['data']['payment_request_id'] ?? $paymentResponse['data']['id'] ?? null,
                'order_id' => $validated['order_id'],
                'invoice_url' => $paymentResponse['data']['invoice_url'] ?? null,
                'status' => $paymentResponse['data']['status'] ?? 'PENDING',
                'actions' => $actions,
                'message' => 'Payment request berhasil dibuat',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Payment creation error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Check payment status
     * GET /api/payment/status/{paymentId}
     */
    public function status($paymentId)
    {
        try {
            $result = $this->xenditService->verifyPaymentRequest($paymentId);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Tidak dapat mengambil status',
                ], 404);
            }

            // Map Xendit status to our internal status
            $statusMap = [
                'SUCCEEDED' => 'SUCCEEDED',
                'REQUIRES_ACTION' => 'ACCEPTING_PAYMENTS',
                'PENDING' => 'ACCEPTING_PAYMENTS',
                'ACCEPTING_PAYMENTS' => 'ACCEPTING_PAYMENTS',
                'EXPIRED' => 'EXPIRED',
                'FAILED' => 'FAILED',
                'CANCELED' => 'CANCELED',
            ];

            $status = $statusMap[$result['status']] ?? $result['status'];
            $transaction = Transaction::where('transaction_id', $paymentId)->first();

            if ($transaction) {
                $dbStatus = match ($status) {
                    'SUCCEEDED' => 'settlement',
                    'EXPIRED' => 'expired',
                    'FAILED', 'CANCELED' => 'failed',
                    default => 'pending',
                };

                $transaction->update([
                    'status' => $dbStatus,
                    'midtrans_response' => $result['raw'] ?? $transaction->midtrans_response,
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'invoice_id' => $result['payment_request_id'],
                'external_id' => $result['reference_id'],
                'order_id' => $transaction?->order_id ?? $result['reference_id'],
                'amount' => $result['amount'],
                'paid_amount' => $result['paid_amount'],
                'paid_date' => $result['paid_date'],
                'payment_method' => $result['payment_method'],
                'actions' => $result['actions'] ?? [],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status',
            ], 500);
        }
    }

    /**
     * Map channel code to Xendit payment method (single channel)
     */
    private function extractActions(array $invoiceData, string $channelCode): array
    {
        $actions = $invoiceData['actions'] ?? [];
        $expiresAt = $invoiceData['channel_properties']['expires_at'] ?? null;

        if ($expiresAt && !collect($actions)->contains(fn ($action) => ($action['descriptor'] ?? null) === 'EXPIRY_DATE')) {
            $actions[] = [
                'descriptor' => 'EXPIRY_DATE',
                'value' => $expiresAt,
            ];
        }

        return $actions;
    }
}
