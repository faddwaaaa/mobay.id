<?php

require_once __DIR__ . '/vendor/autoload.php';

$apiKey = 'xnd_development_dHni59HdHHotreNFdGpnRRJGngUXm9h3fDPt8CPDFFnjssE55EWN1ZmtlqJNH8JZ';
$baseUrl = 'https://api.xendit.co';
$endpoint = '/v4/invoices';

$payload = [
    'reference_id' => 'TEST-XENDIT-' . time(),
    'currency' => 'IDR',
    'amount' => 50000,
    'payment_method' => [
        'type' => 'VIRTUAL_ACCOUNT',
        'channel_code' => 'BCA'
    ]
];

echo "=== XENDIT API TEST ===\n";
echo "Base URL: " . $baseUrl . "\n";
echo "Endpoint: " . $endpoint . "\n";
echo "Full URL: " . $baseUrl . $endpoint . "\n";
echo "API Key: " . substr($apiKey, 0, 20) . "...\n";
echo "Payload:\n";
echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $baseUrl . $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_USERPWD => $apiKey . ':',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 30,
]);

echo "Sending request...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: " . $httpCode . "\n";
echo "cURL Error: " . ($curlError ?: 'None') . "\n\n";
echo "Response:\n";
echo $response . "\n";

$responseData = json_decode($response, true);
if ($responseData) {
    echo "\nParsed Response:\n";
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
}
