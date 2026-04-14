<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class ProSubscriptionService
{
    private $client;
    private $baseUrl = 'https://api.xendit.co';
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('xendit.secret_key');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$this->apiKey, ''],
        ]);
    }

    /**
     * Buat invoice QRIS untuk paket Pro
     */
    public function createInvoice(User $user, string $packageType): array
    {
        $prices = [
            'monthly' => 49900,
            'yearly' => 500000,
        ];

        if (!isset($prices[$packageType])) {
            throw new \Exception('Paket tidak válid');
        }

        $amount = $prices[$packageType];
        $externalId = 'pro-' . $user->id . '-' . $packageType . '-' . Str::random(8);

        try {
            $response = $this->client->post('/v2/invoices', [
                'json' => [
                    'external_id' => $externalId,
                    'amount' => $amount,
                    'payer_email' => $user->email,
                    'description' => 'Paket Pro ' . ($packageType === 'monthly' ? 'Bulanan (30 hari)' : 'Tahunan (365 hari)'),
                    'invoice_duration' => 86400, // 24 jam
                    'reminder_time' => 1,
                    'customer' => [
                        'given_names' => $user->name,
                        'email' => $user->email,
                    ],
                    'fees' => [
                        [
                            'type' => 'XENDIT_ADMIN_FEE',
                            'value' => 0,
                        ],
                    ],
                    'items' => [
                        [
                            'name' => 'Pro ' . ($packageType === 'monthly' ? 'Bulanan' : 'Tahunan'),
                            'quantity' => 1,
                            'price' => $amount,
                        ],
                    ],
                    'payment_methods' => ['QRIS'],
                    'success_redirect_url' => route('pro.payment.success'),
                    'failure_redirect_url' => route('pro.payment.failed'),
                ],
            ]);

            $invoiceData = json_decode($response->getBody(), true);

            // Simpan reference ke user
            $user->update([
                'xendit_invoice_id' => $invoiceData['id'],
                'xendit_external_id' => $externalId,
                'pro_type' => $packageType,
            ]);

            return [
                'success' => true,
                'invoice_id' => $invoiceData['id'],
                'external_id' => $externalId,
                'amount' => $amount,
                'qr_code' => $invoiceData['qr_code'] ?? null,
                'invoice_url' => $invoiceData['invoice_url'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Aktifkan Pro user sesuai paket
     */
    public function activatePro(User $user, string $packageType): bool
    {
        $duration = $packageType === 'yearly' ? 365 : 30;
        $proUntil = Carbon::now()->addDays($duration);

        // Jika sudah Pro dan belum expired, perpanjang dari waktu expire
        if ($user->isProActive()) {
            $proUntil = $user->pro_until->addDays($duration);
        }

        $user->update([
            'pro_until' => $proUntil,
            'pro_type' => $packageType,
            'subscription_plan' => 'pro',
        ]);

        return true;
    }

    /**
     * Validasi callback dari Xendit
     */
    public function validateCallback(array $data): bool
    {
        $xCallbackToken = request()->header('X-Callback-Token');
        $expectedToken = config('xendit.callback_token');

        return $xCallbackToken === $expectedToken;
    }

    /**
     * Handle pembayaran sukses (callback dari Xendit)
     */
    public function handlePaymentSuccess(array $data): bool
    {
        // Data dari Xendit:
        // {
        //   "id": "invoice_id",
        //   "external_id": "pro-user_id-monthly-xxx",
        //   "status": "PAID",
        //   "payment_method": "QRIS"
        // }

        $externalId = $data['external_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$externalId || $status !== 'PAID') {
            return false;
        }

        // Parse external_id
        $parts = explode('-', $externalId);
        if (count($parts) < 3 || $parts[0] !== 'pro') {
            return false;
        }

        $userId = $parts[1];
        $packageType = $parts[2]; // 'monthly' atau 'yearly'

        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Aktivasi Pro
        return $this->activatePro($user, $packageType);
    }

    /**
     * Ambil invoice details dari Xendit
     */
    public function getInvoice(string $invoiceId): array
    {
        try {
            $response = $this->client->get("/v2/invoices/{$invoiceId}");
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
