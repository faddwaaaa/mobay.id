<?php
// Quick Xendit API Test - Direct cURL
header('Content-Type: application/json');

echo "=== QUICK XENDIT TEST ===\n";

// Parse .env
$envContent = file_get_contents('.env');
$lines = explode("\n", $envContent);
$env = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos($line, 'XENDIT') === 0) {
        [$key, $val] = explode('=', $line, 2);
        $env[trim($key)] = trim($val);
    }
}

$apiKey = $env['XENDIT_API_KEY'] ?? '';
echo "API Key: " . substr($apiKey, 0, 25) . "...\n\n";

if (!$apiKey) {
    die("ERROR: No API key found\n");
}

// Test v4
echo "=== Testing v4/invoices ===\n";
$payload_v4 = json_encode([
    'external_id' => 'TEST-' . time(),
    'amount' => 50000,
    'description' => 'Test',
    'currency' => 'IDR',
    'payment_method' => [
        'type' => 'VIRTUAL_ACCOUNT',
        'channel_code' => 'BCA'
    ]
]);

$ch = curl_init('https://api.xendit.co/v4/invoices');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $payload_v4,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_USERPWD => "$apiKey:",
    CURLOPT_TIMEOUT => 10,
]);
$resp_v4 = curl_exec($ch);
$code_v4 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $code_v4\n";
echo "Response: " . substr($resp_v4, 0, 200) . "\n";

// If v4 failed, test v1
if ($code_v4 >= 400) {
    echo "\n=== Testing v1/invoices ===\n";
    $payload_v1 = json_encode([
        'external_id' => 'TEST-' . time(),
        'amount' => 50000,
        'description' => 'Test'
    ]);
    
    $ch = curl_init('https://api.xendit.co/invoices');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $payload_v1,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_USERPWD => "$apiKey:",
        CURLOPT_TIMEOUT => 10,
    ]);
    $resp_v1 = curl_exec($ch);
    $code_v1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $code_v1\n";
    echo "Response: " . substr($resp_v1, 0, 200) . "\n";
}

echo "\nDone.\n";
