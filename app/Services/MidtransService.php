<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * MidtransService - Handle all Midtrans API interactions
 * 
 * This service is responsible for:
 * - Creating Snap transactions
 * - Verifying transaction status
 * - Verifying callback signatures
 * - Building transaction details
 */
class MidtransService
{
    protected string $merchantId;
    protected string $clientKey;
    protected string $serverKey;
    protected string $apiBaseUrl;
    protected string $snapBaseUrl;
    protected bool $isProduction;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->merchantId = config('midtrans.merchant_id');
        $this->clientKey = config('midtrans.client_key');
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->apiBaseUrl = config('midtrans.api_base_url');
        $this->snapBaseUrl = config('midtrans.snap_base_url');
        $this->verifySsl = config('midtrans.verify_ssl', true);

        if (!config('midtrans.enabled')) {
            return;
        }

        $this->validateConfiguration();
    }

    /**
     * Validate Midtrans configuration
     * 
     * @throws Exception
     */
    protected function validateConfiguration(): void
    {
        if (empty($this->merchantId) || empty($this->clientKey) || empty($this->serverKey)) {
            throw new Exception('Midtrans configuration is incomplete. Please check your .env file.');
        }
    }

    /**
     * Create Snap transaction and return token
     * 
     * @param string $orderId
     * @param int $amount
     * @param string $customerEmail
     * @param string $customerName
     * @param array $additionalData
     * @return array ['token' => string, 'redirect_url' => string]
     * @throws Exception
     */
    public function createSnapTransaction(
        string $orderId,
        int $amount,
        string $customerEmail,
        string $customerName,
        array $additionalData = []
    ): array {
        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
            ],
            'callbacks' => [
                'finish' => config('midtrans.finish_url'),
                'error' => config('midtrans.error_url'),
                'unfinish' => config('midtrans.unfinish_url'),
            ],
            'enabled_payments' => $this->getEnabledPaymentMethods(),
            'item_details' => [
                [
                    'id' => 'topup-' . $orderId,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => config('midtrans.item_name', 'Top Up Balance'),
                ]
            ],
        ];

        // Merge additional data
        $payload = array_merge($payload, $additionalData);

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withoutVerifying(!$this->verifySsl)
                ->post($this->apiBaseUrl . '/snap/v1/transactions', $payload);

            if (!$response->successful()) {
                throw new Exception('Failed to create Midtrans transaction: ' . $response->body());
            }

            $data = $response->json();

            return [
                'token' => $data['token'] ?? null,
                'redirect_url' => $this->snapBaseUrl . '/snap/v1/transactions/' . ($data['token'] ?? '') . '/redirect',
            ];
        } catch (Exception $e) {
            \Log::error('Midtrans Snap Error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            throw $e;
        }
    }

    /**
     * Get transaction status from Midtrans
     * 
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function getTransactionStatus(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withoutVerifying(!$this->verifySsl)
                ->get($this->apiBaseUrl . '/v2/' . $this->merchantId . '/' . $orderId . '/status');

            if (!$response->successful()) {
                throw new Exception('Failed to get transaction status: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            \Log::error('Midtrans Status Check Error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            throw $e;
        }
    }

    /**
     * Verify callback signature from Midtrans
     * 
     * IMPORTANT: Always verify signature before processing callback!
     * This prevents unauthorized balance manipulation.
     * 
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    public function verifyCallbackSignature(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey
    ): bool {
        // Build signature string according to Midtrans specification
        // Format: {order_id}{status_code}{gross_amount}{server_key}
        $data = $orderId . $statusCode . $grossAmount . $this->serverKey;
        $hash = hash('sha512', $data);

        // Compare with provided signature key (constant-time comparison)
        return hash_equals($hash, $signatureKey);
    }

    /**
     * Get enabled payment methods
     * 
     * @return array
     */
    protected function getEnabledPaymentMethods(): array
    {
        $config = config('midtrans.payment_methods', []);
        $enabled = [];

        // Map enabled payment methods
        if ($config['credit_card'] ?? true) {
            $enabled[] = 'credit_card';
        }
        if ($config['bank_transfer'] ?? true) {
            $enabled[] = 'bank_transfer';
        }
        if ($config['e_wallet'] ?? true) {
            $enabled[] = 'gopay';
            $enabled[] = 'ovo';
            $enabled[] = 'dana';
        }
        if ($config['qris'] ?? true) {
            $enabled[] = 'qris';
        }

        return $enabled ?: ['credit_card', 'bank_transfer', 'gopay', 'ovo', 'dana', 'qris'];
    }

    /**
     * Cancel transaction
     * 
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function cancelTransaction(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withoutVerifying(!$this->verifySsl)
                ->post($this->apiBaseUrl . '/v2/' . $this->merchantId . '/' . $orderId . '/cancel');

            if (!$response->successful()) {
                throw new Exception('Failed to cancel transaction: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            \Log::error('Midtrans Cancel Error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique order ID for transaction
     * Format: PAYOU-{timestamp}-{random}
     * Example: PAYOU-1706594400-a1b2c3d4
     * 
     * @return string
     */
    public static function generateOrderId(): string
    {
        return 'PAYOU-' . time() . '-' . Str::random(8);
    }

    /**
     * Get client key for frontend (Snap.js)
     * 
     * @return string
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Check if Midtrans is enabled
     * 
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return config('midtrans.enabled', false);
    }
}
