<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    public function __construct(protected RajaOngkirService $ongkir) {}

    // =========================================================
    // SEARCH AREA (autocomplete)
    // GET /api/ongkir/cities?q=purwokerto
    // =========================================================

    public function cities(Request $request)
    {
        $q = preg_replace('/\s+/', ' ', trim((string) $request->get('q', '')));
        if (strlen($q) < 2) return response()->json([]);

        try {
            $queries = [$q];
            if (!str_ends_with($q, ' ')) {
                $queries[] = $q . ' ';
            }
            if (str_contains($q, ' ')) {
                $queries[] = str_replace(' ', '', $q);
            }

            $bucket = [];
            foreach ($queries as $term) {
                $rows = $this->ongkir->searchVillages($term, 20);
                foreach ($rows as $v) {
                    $key = (string) ($v['village_code'] ?? '');
                    if ($key === '' || isset($bucket[$key])) {
                        continue;
                    }
                    $bucket[$key] = $v;
                }

                if (count($bucket) >= 20) {
                    break;
                }
            }

            $results = array_slice(array_values($bucket), 0, 20);
            $normalizedQ = mb_strtolower($q);
            usort($results, function ($a, $b) use ($normalizedQ) {
                $aVillage = mb_strtolower((string) ($a['village_name'] ?? ''));
                $bVillage = mb_strtolower((string) ($b['village_name'] ?? ''));
                $aCity = mb_strtolower((string) ($a['city_name'] ?? ''));
                $bCity = mb_strtolower((string) ($b['city_name'] ?? ''));

                $aStarts = str_starts_with($aVillage, $normalizedQ) || str_starts_with($aCity, $normalizedQ);
                $bStarts = str_starts_with($bVillage, $normalizedQ) || str_starts_with($bCity, $normalizedQ);
                if ($aStarts !== $bStarts) {
                    return $aStarts ? -1 : 1;
                }

                $aContains = str_contains($aVillage, $normalizedQ) || str_contains($aCity, $normalizedQ);
                $bContains = str_contains($bVillage, $normalizedQ) || str_contains($bCity, $normalizedQ);
                if ($aContains !== $bContains) {
                    return $aContains ? -1 : 1;
                }

                return strcmp($aVillage, $bVillage);
            });

            $results = array_slice($results, 0, 20);

            return response()->json(array_map(fn($v) => [
                'village_code' => $v['village_code'],
                'label'        => ($v['village_name'] ?? '') . ', ' . ($v['district_name'] ?? '') . ', ' . ($v['city_name'] ?? '') . ', ' . ($v['province_name'] ?? ''),
                'village_name' => $v['village_name'] ?? '',
                'district_name' => $v['district_name'] ?? '',
                'city_name' => $v['city_name'] ?? '',
                'province' => $v['province_name'] ?? '',
            ], $results));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal memuat data area pengiriman'], 500);
        }
    }

    // =========================================================
    // CEK ONGKIR
    // POST /api/ongkir/cost
    // Body: { origin_village_code, destination_village_code, weight }
    // =========================================================

    public function cost(Request $request)
    {
        $request->validate([
            'origin_village_code'      => 'required|string',
            'destination_village_code' => 'required|string',
            'weight'                   => 'required|integer|min:1',
        ]);

        try {
            $results = $this->ongkir->getCost(
                $request->origin_village_code,
                $request->destination_village_code,
                (int) $request->weight
            );

            return response()->json([
                'success' => true,
                'data'    => $results,
                'meta'    => [
                    'origin'      => $request->origin_village_code,
                    'destination' => $request->destination_village_code,
                    'weight_gram' => $request->weight,
                    'weight_kg'   => max(ceil($request->weight / 1000), 1),
                ],
            ]);
        } catch (\Throwable $e) {
            $rawMessage = (string) $e->getMessage();
            $lowerMessage = strtolower($rawMessage);

            $publicMessage = 'Layanan ongkir sedang tidak tersedia. Silakan coba lagi nanti.';
            if (str_contains($lowerMessage, 'no sufficient balance')) {
                $publicMessage = 'Layanan ongkir belum aktif karena saldo akun pengiriman tidak mencukupi.';
            } elseif (str_contains($lowerMessage, 'invalid or missing postal code')) {
                $publicMessage = 'Area asal/tujuan belum valid. Pilih ulang area dari dropdown lalu simpan pengaturan pengiriman.';
            } elseif (str_contains($lowerMessage, 'unauthorized') || str_contains($lowerMessage, 'invalid token') || str_contains($lowerMessage, 'invalid api')) {
                $publicMessage = 'Konfigurasi layanan ongkir belum valid. Silakan hubungi admin toko.';
            }

            return response()->json([
                'success' => false,
                'error'   => auth()->check() ? ('Gagal mengambil data ongkir: ' . $rawMessage) : $publicMessage,
            ], 500);
        }
    }
}
