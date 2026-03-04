<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingSettingsController extends Controller
{
    public function index()
    {
        return view('settings.shipping');
    }

    public function save(Request $request)
    {
        $request->validate([
            'origin_village_code' => 'required|string|max:100',
            'origin_city_name'    => 'required|string|max:255',
        ]);

        Auth::user()->update([
            'origin_village_code' => $request->origin_village_code,
            'origin_city_name'    => $request->origin_city_name,
        ]);

        return redirect()->route('settings.shipping')
            ->with('success', 'Kelurahan asal pengiriman berhasil disimpan!');
    }
}
