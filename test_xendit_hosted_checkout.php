<?php

use App\Services\XenditPaymentService;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var XenditPaymentService $service */
$service = $app->make(XenditPaymentService::class);

$tests = [
    ['label' => 'BANK_TRANSFER', 'methods' => ['VIRTUAL_ACCOUNT_BCA', 'VIRTUAL_ACCOUNT_BNI', 'VIRTUAL_ACCOUNT_MANDIRI']],
    ['label' => 'QRIS', 'methods' => ['QRIS']],
];

foreach ($tests as $test) {
    $response = $service->createInvoice([
        'external_id' => 'HOSTED-' . $test['label'] . '-' . time(),
        'description' => 'Hosted checkout test ' . $test['label'],
        'amount' => 20000,
        'customer_name' => 'Hosted Tester',
        'customer_email' => 'hosted@example.com',
        'customer_phone' => '081234567890',
        'payment_methods' => $test['methods'],
        'success_url' => config('app.url') . '/checkout/success',
        'failure_url' => config('app.url') . '/checkout/pending',
    ]);

    echo "===== {$test['label']} =====\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
}
