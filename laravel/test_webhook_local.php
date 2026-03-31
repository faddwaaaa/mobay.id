<?php
/**
 * Test Xendit Webhook Locally
 * Simulates invoice.paid event from Xendit
 */

$webhookUrl = 'http://localhost:8000/webhook/xendit/invoice';

// Simulate Xendit invoice.paid webhook payload
$payload = json_encode([
    'event' => 'invoice.paid',
    'created' => time(),
    'data' => [
        'id' => 'xendit-invoice-' . uniqid(),
        'status' => 'PAID',
        'external_id' => 'PAYOU-TEST-' . time(),
        'amount' => 50000,
        'paid_amount' => 50000,
        'paid_at' => date('Y-m-d\TH:i:s.000\Z'),
        'payer_email' => 'test@example.com',
        'description' => 'Test Payment',
        'payment_method' => [
            'type' => 'VIRTUAL_ACCOUNT',
            'channel_code' => 'BCA'
        ],
        'currency' => 'IDR',
    ]
]);

echo "=== TESTING XENDIT WEBHOOK ===\n";
echo "URL: $webhookUrl\n";
echo "Payload:\n" . json_encode(json_decode($payload), JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init($webhookUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-Callback-Token: test-token',
    ],
    CURLOPT_TIMEOUT => 30,
]);

echo "Sending webhook...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($curlError) {
    echo "cURL Error: $curlError\n";
}
echo "Response:\n$response\n";

// If success, check logs
if ($httpCode === 200) {
    echo "\n✅ Webhook received! Now check logs:\n";
    echo "tail -50 storage/logs/laravel.log\n";
} else {
    echo "\n❌ Webhook failed. Check if server is running.\n";
}
