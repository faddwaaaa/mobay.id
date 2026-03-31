<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey  = config('services.biteship.key');
        $this->baseUrl = config('services.biteship.base_url', 'https://api.biteship.com');
    }

    public function getCouriers(): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/v1/couriers");

        return $response->failed() ? [] : $response->json('couriers', []);
    }

    public function createOrder(array $payload): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/v1/orders", $payload);

        Log::info('[Biteship] createOrder request', $payload);
        Log::info('[Biteship] createOrder response', $response->json() ?? []);

        return [
            'success' => $response->successful(),
            'data'    => $response->json(),
            'status'  => $response->status(),
        ];
    }

    public function trackOrder(string $trackingNumber): ?array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/v1/trackings/{$trackingNumber}/couriers");

        return $response->failed() ? null : $response->json();
    }
}