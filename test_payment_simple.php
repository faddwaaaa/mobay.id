<?php
// Simple payment test without Laravel dependencies
// Test the complete payment flow

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== XENDIT PAYMENT TEST ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Load .env file
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    die("ERROR: .env file not found at $envPath\n");
}

$envContent = file_get_contents($envPath);
preg_match('/XENDIT_API_KEY=(.+)/', $envContent, $matches);
$apiKey = trim($matches[1] ?? '');

if (!$apiKey) {
    die("ERROR: XENDIT_API_KEY not found in .env\n");
}

echo "API Key loaded: " . substr($apiKey, 0, 30) . "...\n";

// Test 1: Create invoice with v4/invoices
echo "\n--- TEST 1: Create Invoice (v4/invoices) ---\n";

$payload = [
    'reference_id' => 'TEST-' . time(),
    'currency' => 'IDR',
    'amount' => 50000,
    'payment_method' => [
        'type' => 'VIRTUAL_ACCOUNT',
        'channel_code' => 'BCA'
    ]
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.xendit.co/v4/invoices',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_USERPWD => $apiKey . ':',
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_VERBOSE => false,
]);

echo "Sending:\n";
echo "URL: https://api.xendit.co/v4/invoices\n";
echo "Auth: Basic with API key\n";
echo "Payload:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($curlError) {
    echo "cURL Error: $curlError\n";
}
echo "Response:\n$response\n\n";

// Try to parse response
$parsed = json_decode($response, true);
if ($parsed) {
    echo "Parsed Response:\n";
    echo json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}

// Save result to file
$result = [
    'timestamp' => date('Y-m-d H:i:s'),
    'test' => 'v4/invoices endpoint',
    'http_code' => $httpCode,
    'response' => $response,
    'parsed' => $parsed
];

file_put_contents(__DIR__ . '/payment_test_result.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "\n✅ Result saved to payment_test_result.json\n";

// If v4 failed, try v1
if ($httpCode >= 400) {
    echo "\n--- TEST 2: Try v1/invoices (fallback) ---\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.xendit.co/v1/invoices',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_USERPWD => $apiKey . ':',
        CURLOPT_POSTFIELDS => json_encode([
            'external_id' => 'TEST-' . time(),
            'amount' => 50000,
            'description' => 'Test Payment'
        ]),
        CURLOPT_TIMEOUT => 30,
    ]);
    
    $response2 = curl_exec($ch);
    $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode2\n";
    echo "Response: $response2\n";
}
