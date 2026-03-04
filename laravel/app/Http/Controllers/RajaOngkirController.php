<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use App\Models\RajaongkirCity;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    public function __construct(protected RajaOngkirService $ongkir) {}

    // =========================================================
    // SEARCH KELURAHAN/KOTA (autocomplete)
    // GET /api/ongkir/cities?q=purwokerto
    // Cari dari DB dulu (seeder), fallback ke api.co.id
    // =========================================================

    public function cities(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        // 1. Cari dari DB lokal dulu (cepat, tidak hit API)
        $fromDb = RajaongkirCity::where('city_name', 'like', "%{$q}%")
            ->orWhere('village_name', 'like', "%{$q}%")
            ->orWhere('province', 'like', "%{$q}%")
            ->orderBy('city_name')
            ->limit(20)
            ->get()
            ->map(fn($c) => [
                'village_code' => $c->village_code,
                'label'        => $c->village_name . ', ' . $c->district_name . ', ' . $c->city_name . ', ' . $c->province,
                'village_name' => $c->village_name,
                'district_name'=> $c->district_name,
                'city_name'    => $c->city_name,
                'province'     => $c->province,
            ]);

        if ($fromDb->isNotEmpty()) {
            return response()->json($fromDb);
        }

        // 2. Fallback: cari dari api.co.id
        try {
            $results = $this->ongkir->searchVillages($q, 20);
            return response()->json(array_map(fn($v) => [
                'village_code' => $v['village_code'],
                'label'        => ($v['village_name'] ?? '') . ', ' . ($v['district_name'] ?? '') . ', ' . ($v['city_name'] ?? '') . ', ' . ($v['province_name'] ?? ''),
                'village_name' => $v['village_name'] ?? '',
                'district_name'=> $v['district_name'] ?? '',
                'city_name'    => $v['city_name'] ?? '',
                'province'     => $v['province_name'] ?? '',
            ], $results));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal memuat data wilayah'], 500);
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
            return response()->json([
                'success' => false,
                'error'   => 'Gagal mengambil data ongkir: ' . $e->getMessage(),
            ], 500);
        }
    }
}