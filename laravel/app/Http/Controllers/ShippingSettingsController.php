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
            'origin_city_id'   => 'required|integer',
            'origin_city_name' => 'required|string|max:255',
        ]);

        Auth::user()->update([
            'origin_city_id'   => $request->origin_city_id,
            'origin_city_name' => $request->origin_city_name,
        ]);

        return redirect()->route('settings.shipping')
            ->with('success', 'Kota asal pengiriman berhasil disimpan!');
    }
}