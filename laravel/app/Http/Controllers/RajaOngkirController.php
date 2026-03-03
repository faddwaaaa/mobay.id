<?php

namespace App\Http\Controllers;

use App\Models\RajaongkirCity;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RajaOngkirController extends Controller
{
    public function __construct(protected RajaOngkirService $ongkir) {}

    // =========================================================
    // SEARCH KOTA (autocomplete)
    // =========================================================

    /**
     * GET /api/ongkir/cities?q=bandung
     * Cari kota dari DB cache (fast) atau API (fallback)
     */
    public function cities(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Cari dari DB cache dulu (lebih cepat)
        $fromDb = RajaongkirCity::where('city_name', 'like', "%{$q}%")
            ->orWhere('province', 'like', "%{$q}%")
            ->orderBy('city_name')
            ->limit(20)
            ->get(['city_id', 'type', 'city_name', 'province', 'postal_code'])
            ->map(fn($c) => [
                'city_id'    => $c->city_id,
                'label'      => "{$c->type} {$c->city_name}, {$c->province}",
                'city_name'  => $c->city_name,
                'type'       => $c->type,
                'province'   => $c->province,
                'postal_code'=> $c->postal_code,
            ]);

        if ($fromDb->isNotEmpty()) {
            return response()->json($fromDb);
        }

        // Fallback: cari dari API RajaOngkir
        try {
            $results = $this->ongkir->searchCities($q);
            return response()->json(array_slice(array_map(fn($c) => [
                'city_id'    => $c['city_id'],
                'label'      => "{$c['type']} {$c['city_name']}, {$c['province']}",
                'city_name'  => $c['city_name'],
                'type'       => $c['type'],
                'province'   => $c['province'],
                'postal_code'=> $c['postal_code'] ?? '',
            ], $results), 0, 20));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Tidak dapat memuat data kota'], 500);
        }
    }

    // =========================================================
    // CEK ONGKIR
    // =========================================================

    /**
     * POST /api/ongkir/cost
     * Body: { origin: int, destination: int, weight: int }
     */
    public function cost(Request $request)
    {
        $request->validate([
            'origin'      => 'required|integer',
            'destination' => 'required|integer',
            'weight'      => 'required|integer|min:1',
        ]);

        $origin      = (int) $request->origin;
        $destination = (int) $request->destination;
        $weight      = (int) $request->weight;

        // Cache per kombinasi origin-destination-weight selama 30 menit
        $cacheKey = "ongkir_{$origin}_{$destination}_{$weight}";

        try {
            $results = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($origin, $destination, $weight) {
                return $this->ongkir->getCost($origin, $destination, $weight);
            });

            return response()->json([
                'success' => true,
                'data'    => $results,
                'origin'  => $origin,
                'destination' => $destination,
                'weight'  => $weight,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Gagal mengambil data ongkir: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================
    // SYNC DATA KE DB (dipanggil artisan command)
    // =========================================================

    public function sync()
    {
        try {
            $provinces = $this->ongkir->syncProvincesToDb();
            $cities    = $this->ongkir->syncCitiesToDb();

            return response()->json([
                'success'   => true,
                'provinces' => $provinces,
                'cities'    => $cities,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}