<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class XenditPaymentService
{
    protected const PAYMENT_REQUESTS_API_VERSION = '2024-11-11';

    protected $apiKey;
    protected $apiUrl;
    protected $isProduction;

    public function __construct()
    {
        $this->apiKey = config('xendit.api_key');
        $this->apiUrl = config('xendit.api_base_url');
        $this->isProduction = config('xendit.is_production', false);
    }

    /**
     * Create an invoice with payment method
     * 
     * @param array $data
     * @return array
     */
    public function createInvoice(array $data)
    {
        try {
            $payload = [
                'external_id' => $data['reference_id'] ?? $data['external_id'] ?? uniqid('PAYOU-'),
                'amount' => (int) $data['amount'],
                'description' => $data['description'] ?? 'Payment',
                'currency' => $data['currency'] ?? 'IDR',
                'invoice_duration' => $data['invoice_duration'] ?? 86400,
            ];

            if (!empty($data['customer_name']) || !empty($data['customer_email'])) {
                $payload['customer'] = array_filter([
                    'email' => $data['customer_email'] ?? null,
                    'mobile_number' => $data['customer_phone'] ?? null,
                    'given_names' => $data['customer_name'] ?? null,
                ]);
                $payload['customer'] = array_filter($payload['customer']);
            }

            if (!empty($data['success_url'])) {
                $payload['success_redirect_url'] = $data['success_url'];
            }
            if (!empty($data['failure_url'])) {
                $payload['failure_redirect_url'] = $data['failure_url'];
            }

            if (!empty($data['items'])) {
                $payload['items'] = $data['items'];
            }

            if (!empty($data['fees'])) {
                $payload['fees'] = $data['fees'];
            }

            if (!empty($data['payment_methods']) && is_array($data['payment_methods'])) {
                $payload = array_merge($payload, $this->buildInvoiceChannelRestrictions($data['payment_methods']));
            }

            Log::info('Xendit createInvoice', [
                'url_v2' => $this->apiUrl . '/v2/invoices',
                'api_key_prefix' => substr($this->apiKey, 0, 20) . '...',
                'external_id' => $payload['external_id'],
                'amount' => $payload['amount'],
            ]);

            $response = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/v2/invoices', $payload);

            $statusCode = $response->status();
            $result = $response->json();

            if (
                $statusCode === 400
                && ($result['error_code'] ?? null) === 'UNAVAILABLE_PAYMENT_METHOD_ERROR'
                && !empty($data['payment_methods'])
            ) {
                $fallbackPayload = $payload;
                unset(
                    $fallbackPayload['payment_methods'],
                    $fallbackPayload['available_banks'],
                    $fallbackPayload['available_retail_outlets'],
                    $fallbackPayload['available_ewallets'],
                    $fallbackPayload['available_qr_codes'],
                    $fallbackPayload['should_exclude_credit_card']
                );

                Log::warning('Xendit hosted invoice restriction fallback triggered', [
                    'external_id' => $fallbackPayload['external_id'] ?? null,
                    'error_code' => $result['error_code'] ?? null,
                ]);

                $response = Http::withBasicAuth($this->apiKey, '')
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->post($this->apiUrl . '/v2/invoices', $fallbackPayload);

                $statusCode = $response->status();
                $result = $response->json();
            }
            
            Log::info('Xendit Invoice Response', [
                'status_code' => $statusCode,
                'success' => $response->successful(),
                'has_id' => !empty($result['id']),
                'has_invoice_url' => !empty($result['invoice_url']),
            ]);

            return [
                'success' => $response->successful(),
                'data' => $result,
                'status_code' => $statusCode,
                'message' => $result['message'] ?? null,
                'invoice_url' => $result['invoice_url'] ?? $result['url'] ?? $result['web_url'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Xendit Invoice Creation Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    public function createPaymentRequest(array $data): array
    {
        try {
            $channelCode = strtoupper((string) ($data['channel_code'] ?? ''));
            $payload = [
                'reference_id' => $data['reference_id'] ?? uniqid('PAYOU-'),
                'type' => $data['type'] ?? 'PAY',
                'country' => $data['country'] ?? 'ID',
                'currency' => $data['currency'] ?? 'IDR',
                'request_amount' => (int) $data['amount'],
                'capture_method' => $data['capture_method'] ?? 'AUTOMATIC',
                'channel_code' => $channelCode,
                'channel_properties' => $this->buildPaymentRequestChannelProperties($channelCode, $data),
                'description' => $data['description'] ?? 'Payment',
            ];

            if (!empty($data['customer_name']) || !empty($data['customer_email']) || !empty($data['customer_phone'])) {
                $payload['customer'] = array_filter([
                    'reference_id' => (string) ($data['customer_id'] ?? $payload['reference_id']),
                    'type' => 'INDIVIDUAL',
                    'individual_detail' => array_filter([
                        'given_names' => $data['customer_name'] ?? null,
                    ]),
                    'email' => $data['customer_email'] ?? null,
                    'mobile_number' => $data['customer_phone'] ?? null,
                ], fn ($value) => !is_null($value) && $value !== []);
            }

            if (!empty($data['metadata']) && is_array($data['metadata'])) {
                $payload['metadata'] = $data['metadata'];
            }

            if (!empty($data['items']) && is_array($data['items'])) {
                $payload['items'] = $this->mapItemsForPaymentRequest($data['items']);
            }

            Log::info('Xendit createPaymentRequest', [
                'url' => $this->apiUrl . '/v3/payment_requests',
                'reference_id' => $payload['reference_id'],
                'channel_code' => $channelCode,
                'amount' => $payload['request_amount'],
            ]);

            $response = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Idempotency-Key' => (string) $payload['reference_id'],
                    'api-version' => self::PAYMENT_REQUESTS_API_VERSION,
                ])
                ->post($this->apiUrl . '/v3/payment_requests', $payload);

            $result = $response->json();

            return [
                'success' => $response->successful(),
                'data' => $result,
                'status_code' => $response->status(),
                'message' => $result['message'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Xendit Payment Request Creation Exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Get invoice details
     * 
     * @param string $invoiceId or $referenceId
     * @return array
     */
    public function getInvoice(string $invoiceId)
    {
        try {
            // Try as invoice ID on v4 first
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->apiUrl . '/v4/invoices/' . $invoiceId);

            if (!$response->successful()) {
                // Try as external_id parameter on v4
                $response = Http::withBasicAuth($this->apiKey, '')
                    ->get($this->apiUrl . '/v4/invoices', [
                        'external_id' => $invoiceId,
                    ]);
            }

            if (!$response->successful()) {
                // Fallback to v1 endpoint
                $response = Http::withBasicAuth($this->apiKey, '')
                    ->get($this->apiUrl . '/invoices', [
                        'external_id' => $invoiceId,
                    ]);
            }

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Get Xendit Invoice Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    public function getPaymentRequest(string $paymentRequestId): array
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'api-version' => self::PAYMENT_REQUESTS_API_VERSION,
                ])
                ->get($this->apiUrl . '/v3/payment_requests/' . $paymentRequestId);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status_code' => $response->status(),
                'message' => $response->json()['message'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Get Xendit Payment Request Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Create charge/payment request (simple payment without invoice)
     * 
     * @param array $data
     * @return array
     */
    public function createCharge(array $data)
    {
        try {
            // For simplicity, use invoices API which is more flexible
            return $this->createInvoice([
                'reference_id' => $data['order_id'] ?? $data['reference_id'] ?? uniqid('CHG-'),
                'description' => $data['description'] ?? 'Payment',
                'amount' => (int) $data['amount'],
                'customer_name' => $data['buyer_name'] ?? '',
                'customer_email' => $data['buyer_email'] ?? '',
                'customer_phone' => $data['buyer_phone'] ?? '',
                'payment_method' => $data['payment_method'] ?? 'VIRTUAL_ACCOUNT',
                'bank_code' => $data['bank_code'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'items' => $data['items'] ?? [],
                'fees' => $data['fees'] ?? [],
            ]);

        } catch (\Exception $e) {
            Log::error('Xendit Charge Creation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Map Xendit payment methods to our internal payment method codes
     * 
     * @param string $paymentMethod
     * @return string|array
     */
    public function mapPaymentMethod($paymentMethod)
    {
        $mapping = [
            'va' => ['VIRTUAL_ACCOUNT_BCA', 'VIRTUAL_ACCOUNT_BNI', 'VIRTUAL_ACCOUNT_MANDIRI', 'VIRTUAL_ACCOUNT_PERMATA'],
            'qris' => ['QRIS'],
            'ewallet' => ['DANA', 'OVO', 'LINKAJA'],
            'retail' => ['INDOMARET', 'ALFAMART'],
            'gopay' => ['DANA'],          // Map DANA to gopay-like feature
            'credit_card' => ['CREDIT_CARD'],
        ];

        return $mapping[$paymentMethod] ?? ['VIRTUAL_ACCOUNT_BCA'];
    }

    /**
     * Get list of enabled payment methods
     * 
     * @return array
     */
    public function getPaymentMethods()
    {
        return config('xendit.payment_channels', []);
    }

    /**
     * Verify payment status
     * 
     * @param string $invoiceId
     * @return array
     */
    public function verifyPayment(string $invoiceId)
    {
        $invoice = $this->getInvoice($invoiceId);

        if (!$invoice['success']) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Gagal mendapatkan data pembayaran',
            ];
        }

        $data = $invoice['data'];
        
        return [
            'success' => true,
            'status' => strtolower($data['status'] ?? 'PENDING'),
            'invoice_id' => $data['id'],
            'reference_id' => $data['reference_id'] ?? $data['external_id'] ?? null,
            'amount' => $data['amount'],
            'paid_date' => $data['paid_at'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'paid_amount' => $data['paid_amount'] ?? 0,
        ];
    }

    public function verifyPaymentRequest(string $paymentRequestId): array
    {
        $paymentRequest = $this->getPaymentRequest($paymentRequestId);

        if (!$paymentRequest['success']) {
            return [
                'success' => false,
                'status' => 'FAILED',
                'message' => $paymentRequest['message'] ?? 'Gagal mendapatkan data pembayaran',
            ];
        }

        $data = $paymentRequest['data'];

        return [
            'success' => true,
            'status' => strtoupper((string) ($data['status'] ?? 'PENDING')),
            'payment_request_id' => $data['payment_request_id'] ?? $data['id'] ?? $paymentRequestId,
            'reference_id' => $data['reference_id'] ?? null,
            'amount' => $data['request_amount'] ?? $data['amount'] ?? 0,
            'paid_amount' => $data['paid_amount'] ?? $data['request_amount'] ?? $data['amount'] ?? 0,
            'paid_date' => $data['updated'] ?? $data['updated_at'] ?? $data['created'] ?? null,
            'payment_method' => $data['channel_code'] ?? null,
            'actions' => $data['actions'] ?? [],
            'raw' => $data,
        ];
    }

    /**
     * Handle webhook callback from Xendit
     * 
     * @param array $payload
     * @return array
     */
    public function handleWebhook(array $payload)
    {
        try {
            $eventType = $payload['event'] ?? null;
            $data = $payload['data'] ?? [];

            Log::info('Xendit Webhook received:', [
                'event' => $eventType,
                'external_id' => $data['external_id'] ?? null,
            ]);

            if ($eventType === 'invoice.paid') {
                return [
                    'success' => true,
                    'action' => 'payment_confirmed',
                    'external_id' => $data['external_id'],
                    'invoice_id' => $data['id'],
                    'status' => 'PAID',
                    'paid_amount' => $data['paid_amount'],
                    'paid_date' => $data['paid_at'],
                ];
            } elseif ($eventType === 'invoice.expired') {
                return [
                    'success' => true,
                    'action' => 'payment_expired',
                    'external_id' => $data['external_id'],
                    'status' => 'EXPIRED',
                ];
            }

            return [
                'success' => false,
                'message' => 'Unknown event type',
            ];

        } catch (\Exception $e) {
            Log::error('Xendit Webhook Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment URL from invoice response
     * 
     * @param array $invoiceData
     * @return string
     */
    public function getPaymentUrl(array $invoiceData)
    {
        return $invoiceData['invoice_url'] ?? null;
    }

    private function buildInvoiceChannelRestrictions(array $paymentMethods): array
    {
        $banks = [];
        $retailOutlets = [];
        $ewallets = [];
        $qrCodes = [];
        foreach ($paymentMethods as $method) {
            $method = strtoupper((string) $method);

            if (str_starts_with($method, 'VIRTUAL_ACCOUNT_')) {
                $banks[] = ['bank_code' => str_replace('VIRTUAL_ACCOUNT_', '', $method)];
                continue;
            }

            if ($method === 'QRIS') {
                $qrCodes[] = ['qr_code_type' => 'QRIS'];
                continue;
            }

            if (in_array($method, ['DANA', 'OVO', 'LINKAJA', 'SHOPEEPAY', 'ASTRAPAY', 'GOPAY', 'JENIUSPAY', 'NEXCASH'], true)) {
                $ewallets[] = ['ewallet_type' => $method];
                continue;
            }

            if (in_array($method, ['INDOMARET', 'ALFAMART'], true)) {
                $retailOutlets[] = ['retail_outlet_name' => $method];
                continue;
            }
        }

        return array_filter([
            'available_banks' => $banks ?: null,
            'available_retail_outlets' => $retailOutlets ?: null,
            'available_ewallets' => $ewallets ?: null,
            'available_qr_codes' => $qrCodes ?: null,
        ]);
    }

    private function buildPaymentRequestChannelProperties(string $channelCode, array $data): array
    {
        $properties = [];
        $expiresAt = $data['expires_at'] ?? now()->addDay()->toIso8601String();

        if (str_contains($channelCode, 'VIRTUAL_ACCOUNT') || $channelCode === 'QRIS' || in_array($channelCode, ['ALFAMART', 'INDOMARET'], true)) {
            $properties['expires_at'] = $expiresAt;
        }

        if (str_contains($channelCode, 'VIRTUAL_ACCOUNT')) {
            $properties['display_name'] = $data['display_name'] ?? config('app.name', 'Payou');
        }

        if (in_array($channelCode, ['DANA', 'OVO', 'LINKAJA', 'SHOPEEPAY'], true)) {
            if (!empty($data['success_url'])) {
                $properties['success_return_url'] = $data['success_url'];
            }
            if (!empty($data['failure_url'])) {
                $properties['failure_return_url'] = $data['failure_url'];
            }
        }

        if ($channelCode === 'QRIS') {
            $properties['qr_string_type'] = $data['qr_string_type'] ?? 'DYNAMIC';
        }

        return $properties;
    }

    private function mapItemsForPaymentRequest(array $items): array
    {
        return array_map(function (array $item, int $index) {
            return array_filter([
                'reference_id' => $item['reference_id'] ?? ('item-' . ($index + 1)),
                'type' => $item['type'] ?? 'PHYSICAL_PRODUCT',
                'name' => $item['name'] ?? ('Item ' . ($index + 1)),
                'net_unit_amount' => isset($item['price']) ? (float) $item['price'] : null,
                'quantity' => isset($item['quantity']) ? (int) $item['quantity'] : 1,
                'category' => $item['category'] ?? null,
            ], fn ($value) => !is_null($value));
        }, array_values($items), array_keys(array_values($items)));
    }
}
