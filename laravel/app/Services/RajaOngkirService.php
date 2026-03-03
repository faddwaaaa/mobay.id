<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\RajaongkirCity;
use App\Models\RajaongkirProvince;

class RajaOngkirService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey  = config('rajaongkir.api_key');
        $this->baseUrl = config('rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
    }

    // =========================================================
    // PROVINSI
    // =========================================================

    public function getProvinces(): array
    {
        return Cache::remember('rajaongkir_provinces', now()->addDays(7), function () {
            $res = $this->get('/province');
            return $res['rajaongkir']['results'] ?? [];
        });
    }

    public function getProvince(int $id): ?array
    {
        $provinces = $this->getProvinces();
        foreach ($provinces as $p) {
            if ((int)$p['province_id'] === $id) return $p;
        }
        return null;
    }

    // =========================================================
    // KOTA
    // =========================================================

    public function getCities(?int $provinceId = null): array
    {
        $cacheKey = 'rajaongkir_cities' . ($provinceId ? "_prov_{$provinceId}" : '_all');

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($provinceId) {
            $params = $provinceId ? ['province' => $provinceId] : [];
            $res    = $this->get('/city', $params);
            return $res['rajaongkir']['results'] ?? [];
        });
    }

    public function getCity(int $cityId): ?array
    {
        $cacheKey = "rajaongkir_city_{$cityId}";

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($cityId) {
            $res = $this->get('/city', ['id' => $cityId]);
            return $res['rajaongkir']['results'] ?? null;
        });
    }

    /**
     * Search kota berdasarkan nama (untuk autocomplete)
     */
    public function searchCities(string $keyword): array
    {
        $all     = $this->getCities();
        $keyword = strtolower(trim($keyword));

        return array_values(array_filter($all, function ($city) use ($keyword) {
            return str_contains(strtolower($city['city_name']), $keyword)
                || str_contains(strtolower($city['province']), $keyword);
        }));
    }

    // =========================================================
    // ONGKIR
    // =========================================================

    /**
     * Cek ongkir semua kurir
     *
     * @param  int    $origin      city_id asal
     * @param  int    $destination city_id tujuan
     * @param  int    $weight      berat dalam gram
     * @param  string $courier     jne|pos|tiki|sicepat|jnt|anteraja|... atau 'all'
     */
    public function getCost(int $origin, int $destination, int $weight, string $courier = 'all'): array
    {
        // RajaOngkir starter tidak support 'all', harus per kurir
        // Untuk sandbox gunakan kurir yang tersedia
        $couriers = $courier === 'all'
            ? $this->getAvailableCouriers()
            : [$courier];

        $results = [];

        foreach ($couriers as $c) {
            try {
                $res = $this->post('/cost', [
                    'origin'      => $origin,
                    'destination' => $destination,
                    'weight'      => max($weight, 1), // min 1 gram
                    'courier'     => $c,
                ]);

                $data = $res['rajaongkir']['results'] ?? [];

                foreach ($data as $item) {
                    foreach ($item['costs'] as $cost) {
                        $results[] = [
                            'courier'     => strtoupper($item['code']),
                            'courier_name'=> $item['name'],
                            'service'     => $cost['service'],
                            'description' => $cost['description'],
                            'cost'        => $cost['cost'][0]['value'] ?? 0,
                            'etd'         => $this->formatEtd($cost['cost'][0]['etd'] ?? ''),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Skip kurir yang error, lanjut ke kurir berikutnya
                \Log::warning("RajaOngkir error for courier {$c}: " . $e->getMessage());
            }
        }

        // Sort by harga termurah
        usort($results, fn($a, $b) => $a['cost'] <=> $b['cost']);

        return $results;
    }

    /**
     * Daftar kurir yang didukung RajaOngkir starter
     */
    public function getAvailableCouriers(): array
    {
        return config('rajaongkir.couriers', ['jne', 'pos', 'tiki']);
    }

    // =========================================================
    // SYNC KE DATABASE (untuk autocomplete cepat)
    // =========================================================

    public function syncProvincesToDb(): int
    {
        $provinces = $this->getProvinces();
        $count = 0;

        foreach ($provinces as $p) {
            RajaongkirProvince::updateOrCreate(
                ['province_id' => $p['province_id']],
                ['province'    => $p['province']]
            );
            $count++;
        }

        return $count;
    }

    public function syncCitiesToDb(): int
    {
        $cities = $this->getCities();
        $count  = 0;

        foreach ($cities as $c) {
            RajaongkirCity::updateOrCreate(
                ['city_id' => $c['city_id']],
                [
                    'province_id' => $c['province_id'],
                    'province'    => $c['province'],
                    'type'        => $c['type'],
                    'city_name'   => $c['city_name'],
                    'postal_code' => $c['postal_code'] ?? null,
                ]
            );
            $count++;
        }

        return $count;
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function get(string $endpoint, array $params = []): array
    {
        $res = Http::withHeaders(['key' => $this->apiKey])
            ->timeout(10)
            ->get($this->baseUrl . $endpoint, $params);

        $this->throwIfError($res);
        return $res->json();
    }

    private function post(string $endpoint, array $data = []): array
    {
        $res = Http::withHeaders(['key' => $this->apiKey])
            ->timeout(15)
            ->post($this->baseUrl . $endpoint, $data);

        $this->throwIfError($res);
        return $res->json();
    }

    private function throwIfError($res): void
    {
        if (!$res->successful()) {
            throw new \RuntimeException('RajaOngkir HTTP error: ' . $res->status());
        }

        $body = $res->json();
        $status = $body['rajaongkir']['status'] ?? null;

        if ($status && $status['code'] !== 200) {
            throw new \RuntimeException('RajaOngkir API error: ' . ($status['description'] ?? 'Unknown error'));
        }
    }

    private function formatEtd(string $etd): string
    {
        if (!$etd) return '-';
        $etd = trim($etd);
        if (is_numeric($etd)) return "{$etd} hari";
        return str_replace(['HARI', 'hari', 'Hari'], 'hari', $etd);
    }
}