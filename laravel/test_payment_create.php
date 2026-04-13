<?php
/**
 * Test Payment Creation
 * Simulates POST request ke /payment/create
 */

$apiUrl = 'http://localhost:8000/payment/create';

$payload = [
    'channel_code' => 'BCA_VIRTUAL_ACCOUNT',
    'amount' => 50000,
    'order_id' => 'TEST-' . time(),
    'name' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '082123456789',
    'product_id' => 1,
    'user_id' => 1,
];

echo "=== TESTING PAYMENT CREATION ===\n";
echo "URL: $apiUrl\n";
echo "Payload:\n";
echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

// Get CSRF token first (if needed)
$ch = curl_init('http://localhost:8000/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
]);
$response = curl_exec($ch);
preg_match('/XCSRF-TOKEN["\']?\s*[=:]\s*["\']?([a-zA-Z0-9\/\+]+)/', $response, $m);
$csrfToken = $m[1] ?? 'no-token';
curl_close($ch);

echo "CSRF Token: " . substr($csrfToken, 0, 20) . "...\n\n";

// Make payment request
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-CSRF-TOKEN: ' . $csrfToken,
    ],
    CURLOPT_TIMEOUT => 30,
]);

echo "Sending request...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($curlError) {
    echo "cURL Error: $curlError\n";
}
echo "\nResponse:\n$response\n";

$parsed = json_decode($response, true);
if ($parsed) {
    echo "\nParsed:\n";
    echo json_encode($parsed, JSON_PRETTY_PRINT) . "\n";
}

echo "\n✅ Test complete. Check logs for details:\n";
echo "Get-Content storage/logs/laravel.log -Tail 50\n";
