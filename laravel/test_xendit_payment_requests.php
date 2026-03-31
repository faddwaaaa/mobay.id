<?php

use App\Services\XenditPaymentService;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var XenditPaymentService $service */
$service = $app->make(XenditPaymentService::class);

$tests = [
    'BCA_VIRTUAL_ACCOUNT',
    'QRIS',
];

foreach ($tests as $channelCode) {
    $referenceId = 'MANUAL-TEST-' . $channelCode . '-' . time();

    $response = $service->createPaymentRequest([
        'reference_id' => $referenceId,
        'channel_code' => $channelCode,
        'amount' => 15000,
        'currency' => 'IDR',
        'description' => 'Manual test ' . $channelCode,
        'customer_name' => 'Sandbox Tester',
        'customer_email' => 'sandbox@example.com',
        'customer_phone' => '081234567890',
        'success_url' => config('app.url') . '/checkout/success?order_id=' . $referenceId,
        'failure_url' => config('app.url') . '/checkout/pending?order_id=' . $referenceId,
        'metadata' => [
            'source' => 'manual_script',
            'channel_code' => $channelCode,
        ],
    ]);

    echo "===== {$channelCode} =====\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
}
