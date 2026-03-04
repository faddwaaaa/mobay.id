<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Menggunakan api.co.id — GRATIS
 * Endpoint ongkir : GET https://use.api.co.id/expedition/shipping-cost
 * Endpoint wilayah: GET https://use.api.co.id/regional/indonesia/villages?search=...
 *
 * Auth header: x-api-co-id: YOUR_API_KEY
 */
class RajaOngkirService
{
    protected string $baseUrl = 'https://use.api.co.id';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
    }

    // =========================================================
    // CEK ONGKIR
    // GET /expedition/shipping-cost
    //   ?origin_village_code=3204282001
    //   &destination_village_code=3204402005
    //   &weight=1   ← dalam KG
    // =========================================================

    public function getCost(string $originVillageCode, string $destinationVillageCode, int $weightGram): array
    {
        $weightKg = max(ceil($weightGram / 1000), 1);

        $cacheKey = "ongkir_{$originVillageCode}_{$destinationVillageCode}_{$weightKg}";

        $raw = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($originVillageCode, $destinationVillageCode, $weightKg) {
            $res = Http::withHeaders(['x-api-co-id' => $this->apiKey])
                ->timeout(15)
                ->get($this->baseUrl . '/expedition/shipping-cost', [
                    'origin_village_code'      => $originVillageCode,
                    'destination_village_code' => $destinationVillageCode,
                    'weight'                   => $weightKg,
                ]);

            $body = $res->json();

            if (!($body['is_success'] ?? false)) {
                throw new \RuntimeException($body['message'] ?? 'api.co.id error');
            }

            return $body['data']['couriers'] ?? [];
        });

        // Normalize ke format frontend
        $results = array_map(fn($item) => [
            'courier'      => $item['courier_code'] ?? '',
            'courier_name' => $item['courier_name'] ?? '',
            'service'      => $item['courier_code'] ?? '',
            'description'  => $item['courier_name'] ?? '',
            'cost'         => (int)($item['price'] ?? 0),
            'etd'          => $item['estimation'] ?? '-',
        ], $raw);

        // Filter harga 0, sort termurah
        $results = array_values(array_filter($results, fn($r) => $r['cost'] > 0));
        usort($results, fn($a, $b) => $a['cost'] <=> $b['cost']);

        return $results;
    }

    // =========================================================
    // SEARCH KELURAHAN (untuk autocomplete kota/kelurahan)
    // GET /regional/indonesia/villages?search=purwokerto&limit=20
    // Response: { data: [ { village_code, village_name, district_name, city_name, province_name } ] }
    // =========================================================

    public function searchVillages(string $keyword, int $limit = 20): array
    {
        if (strlen(trim($keyword)) < 2) return [];

        $cacheKey = 'villages_' . md5($keyword . $limit);

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($keyword, $limit) {
            $res = Http::withHeaders(['x-api-co-id' => $this->apiKey])
                ->timeout(10)
                ->get($this->baseUrl . '/regional/indonesia/villages', [
                    'search' => $keyword,
                    'limit'  => $limit,
                ]);

            $body = $res->json();
            return $body['data'] ?? [];
        });
    }
}