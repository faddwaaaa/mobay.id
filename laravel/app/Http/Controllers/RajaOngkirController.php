<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RajaOngkirController extends Controller
{
    public function __construct(protected RajaOngkirService $ongkir) {}

    // =========================================================
    // SEARCH KOTA (autocomplete dari DB wilayah lokal)
    // GET /api/ongkir/cities?q=purwokerto
    // =========================================================

    public function cities(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        // Pakai tabel rajaongkir_cities yang sudah di-seed
        $results = \App\Models\RajaongkirCity::where('city_name', 'like', "%{$q}%")
            ->orWhere('province', 'like', "%{$q}%")
            ->orderBy('city_name')
            ->limit(20)
            ->get()
            ->map(fn($c) => [
                'city_id'    => $c->city_id,
                'label'      => $c->city_name . ', ' . $c->province,
                'city_name'  => $c->city_name,   // ini yang dikirim ke API Binderbyte
                'province'   => $c->province,
            ]);

        return response()->json($results);
    }

    // =========================================================
    // CEK ONGKIR
    // POST /api/ongkir/cost
    // Body: { origin_city: "purwokerto", destination_city: "jakarta", weight: 1000 }
    // =========================================================

    public function cost(Request $request)
    {
        $request->validate([
            'origin_city'      => 'required|string',
            'destination_city' => 'required|string',
            'weight'           => 'required|integer|min:1',
        ]);

        try {
            $results = $this->ongkir->getCost(
                $request->origin_city,
                $request->destination_city,
                (int) $request->weight
            );

            return response()->json([
                'success' => true,
                'data'    => $results,
                'meta'    => [
                    'origin'      => $request->origin_city,
                    'destination' => $request->destination_city,
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