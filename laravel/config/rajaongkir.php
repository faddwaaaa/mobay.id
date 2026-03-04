<?php

return [
    /*
    |--------------------------------------------------------------------------
    | api.co.id API Key — GRATIS
    |--------------------------------------------------------------------------
    | Daftar di https://dashboard.api.co.id
    | Aktifkan: "API Wilayah Indonesia" + "Expedition API"
    |
    | Isi di .env:
    |   RAJAONGKIR_API_KEY=your_api_key_here
    */
    'api_key' => env('RAJAONGKIR_API_KEY', ''),
    'base_url' => 'https://use.api.co.id',
];