<?php

use App\Services\XenditPaymentService;
use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var XenditPaymentService $service */
$service = $app->make(XenditPaymentService::class);

$create = $service->createPaymentRequest([
    'reference_id' => 'SIM-QRIS-' . time(),
    'channel_code' => 'QRIS',
    'amount' => 15000,
    'currency' => 'IDR',
    'description' => 'Simulation QRIS test',
    'customer_name' => 'Sandbox Tester',
    'customer_email' => 'sandbox@example.com',
    'customer_phone' => '081234567890',
    'success_url' => config('app.url') . '/checkout/success',
    'failure_url' => config('app.url') . '/checkout/pending',
]);

echo "=== CREATE ===\n";
echo json_encode($create, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

if (!($create['success'] ?? false)) {
    exit(1);
}

$paymentRequestId = $create['data']['payment_request_id'];
$apiKey = config('xendit.api_key');

$simulate = Http::withBasicAuth($apiKey, '')
    ->withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'api-version' => '2024-11-11',
    ])
    ->post('https://api.xendit.co/v3/payment_requests/' . $paymentRequestId . '/simulate', [
        'amount' => 15000,
    ]);

echo "=== SIMULATE ===\n";
echo json_encode([
    'status' => $simulate->status(),
    'body' => $simulate->json(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

sleep(4);

$verify = $service->verifyPaymentRequest($paymentRequestId);

echo "=== VERIFY ===\n";
echo json_encode($verify, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
