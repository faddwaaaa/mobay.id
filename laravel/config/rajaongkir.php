<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Biteship API Key (Testing Mode / Production)
    |--------------------------------------------------------------------------
    | Isi di .env:
    |   BITESHIP_API_KEY=your_api_key_here
    |
    | Catatan:
    | Tetap fallback ke RAJAONGKIR_API_KEY untuk backward compatibility.
    */
    'api_key' => env('BITESHIP_API_KEY', env('RAJAONGKIR_API_KEY', '')),
    'base_url' => env('BITESHIP_BASE_URL', 'https://api.biteship.com/v1'),
    'couriers' => explode(',', env('BITESHIP_COURIERS', 'jne,sicepat,jnt,anteraja,tiki,pos')),
];
