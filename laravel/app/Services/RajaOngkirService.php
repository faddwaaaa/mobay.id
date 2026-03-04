<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RajaOngkirService
{
    protected string $baseUrl = 'https://api.binderbyte.com/v1';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
    }

    // =========================================================
    // CEK ONGKIR
    // GET /cost?api_key=...&origin=...&destination=...&weight=...&courier=...
    // Weight dalam KG (bukan gram)
    // =========================================================

    /**
     * Cek ongkir ke semua kurir
     *
     * @param  string $origin       nama kota asal, e.g. "purwokerto"
     * @param  string $destination  nama kota tujuan, e.g. "jakarta"
     * @param  int    $weightGram   berat dalam GRAM (akan dikonversi ke KG)
     */
    public function getCost(string $origin, string $destination, int $weightGram): array
    {
        // Binderbyte pakai satuan KG, minimal 1 kg
        $weightKg = max(ceil($weightGram / 1000), 1);

        $couriers = config('rajaongkir.couriers', ['jne', 'sicepat', 'jnt', 'anteraja', 'pos', 'tiki', 'lion', 'ide', 'sap']);
        $courierStr = implode(',', $couriers);

        $cacheKey = 'ongkir_' . md5("{$origin}_{$destination}_{$weightKg}_{$courierStr}");

        try {
            $raw = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($origin, $destination, $weightKg, $courierStr) {
                $res = Http::timeout(15)->get($this->baseUrl . '/cost', [
                    'api_key'     => $this->apiKey,
                    'origin'      => strtolower(trim($origin)),
                    'destination' => strtolower(trim($destination)),
                    'weight'      => $weightKg,
                    'courier'     => $courierStr,
                ]);

                $body = $res->json();

                if (($body['status'] ?? 0) !== 200) {
                    throw new \RuntimeException($body['message'] ?? 'Binderbyte API error');
                }

                return $body['data']['costs'] ?? [];
            });

            // Normalize ke format yang dipakai frontend
            $results = array_map(fn($item) => [
                'courier'      => strtoupper($item['code'] ?? ''),
                'courier_name' => $item['name'] ?? '',
                'service'      => $item['service'] ?? '',
                'description'  => $item['type'] ?? '',
                'cost'         => (int)($item['price'] ?? 0),
                'etd'          => $item['estimated'] ?? '-',
            ], $raw);

            // Filter yang harganya 0 (service tidak tersedia)
            $results = array_values(array_filter($results, fn($r) => $r['cost'] > 0));

            // Sort termurah dulu
            usort($results, fn($a, $b) => $a['cost'] <=> $b['cost']);

            return $results;

        } catch (\Throwable $e) {
            \Log::error('Binderbyte getCost error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAvailableCouriers(): array
    {
        return config('rajaongkir.couriers', ['jne', 'sicepat', 'jnt', 'anteraja', 'pos', 'tiki', 'lion', 'ide', 'sap']);
    }
}