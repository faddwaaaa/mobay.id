<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('rajaongkir.base_url', 'https://api.biteship.com/v1'), '/');
        $this->apiKey = (string) config('rajaongkir.api_key', '');
    }

    public function getCost(string $originAreaId, string $destinationAreaId, int $weightGram): array
    {
        if ($this->apiKey === '') {
            throw new \RuntimeException('BITESHIP_API_KEY belum diatur.');
        }

        $weightGram = max($weightGram, 1);
        $couriers = array_filter(array_map('trim', config('rajaongkir.couriers', [])));
        $courierStr = implode(',', $couriers);

        $cacheKey = 'biteship_rates_' . md5($originAreaId . '|' . $destinationAreaId . '|' . $weightGram . '|' . $courierStr);

        $raw = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($originAreaId, $destinationAreaId, $weightGram, $courierStr) {
            $res = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(20)->post($this->baseUrl . '/rates/couriers', [
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'couriers' => $courierStr,
                'items' => [
                    [
                        'name' => 'Produk',
                        'description' => 'Produk fisik',
                        'value' => 10000,
                        'weight' => $weightGram,
                        'quantity' => 1,
                    ],
                ],
            ]);

            $body = $res->json();

            if (!$res->successful()) {
                $message = $body['error'] ?? $body['message'] ?? ('HTTP ' . $res->status());
                throw new \RuntimeException((string) $message);
            }

            return $body['pricing'] ?? $body['data']['pricing'] ?? $body['data'] ?? [];
        });

        $results = array_map(function ($item) {
            $price = (int) ($item['price'] ?? $item['final_price'] ?? 0);
            $service = $item['courier_service_name'] ?? $item['service_type'] ?? ($item['courier_name'] ?? '');
            $duration = $item['duration'] ?? $item['estimated_delivery_time'] ?? '-';

            return [
                'courier' => strtoupper((string) ($item['courier_code'] ?? '')),
                'courier_name' => (string) ($item['courier_name'] ?? ''),
                'service' => (string) $service,
                'description' => (string) ($item['courier_type'] ?? ''),
                'cost' => $price,
                'etd' => is_array($duration) ? ($duration['from'] ?? '-') . '-' . ($duration['to'] ?? '-') : (string) $duration,
            ];
        }, is_array($raw) ? $raw : []);

        $results = array_values(array_filter($results, fn($r) => $r['cost'] > 0));
        usort($results, fn($a, $b) => $a['cost'] <=> $b['cost']);

        return $results;
    }

    public function searchVillages(string $keyword, int $limit = 20): array
    {
        $keyword = preg_replace('/\s+/', ' ', trim($keyword));

        if ($this->apiKey === '' || strlen($keyword) < 2) {
            return [];
        }

        $cacheKey = 'biteship_areas_' . md5($keyword . '|' . $limit);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($keyword, $limit) {
            $res = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->get($this->baseUrl . '/maps/areas', [
                'countries' => 'ID',
                'input' => $keyword,
                'type' => 'single',
            ]);

            $body = $res->json();

            if (!$res->successful()) {
                return [];
            }

            $areas = $body['areas'] ?? $body['data']['areas'] ?? $body['data'] ?? [];
            if (!is_array($areas)) {
                return [];
            }

            $areas = array_slice($areas, 0, $limit);

            return array_map(function ($area) {
                return [
                    'village_code' => (string) ($area['id'] ?? ''),
                    'village_name' => (string) ($area['name'] ?? ''),
                    'district_name' => (string) ($area['administrative_division_level_3_name'] ?? ''),
                    'city_name' => (string) ($area['administrative_division_level_2_name'] ?? ''),
                    'province_name' => (string) ($area['administrative_division_level_1_name'] ?? ''),
                ];
            }, $areas);
        });
    }
}
