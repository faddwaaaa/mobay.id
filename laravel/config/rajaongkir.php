<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Binderbyte API Key
    |--------------------------------------------------------------------------
    | Daftar gratis di https://binderbyte.com
    | Isi di .env:  RAJAONGKIR_API_KEY=your_api_key_here
    */
    'api_key'  => env('RAJAONGKIR_API_KEY', ''),

    'base_url' => 'https://api.binderbyte.com/v1',

    /*
    |--------------------------------------------------------------------------
    | Kurir (pisah koma)
    | Supported: jne, sicepat, jnt, anteraja, pos, tiki, lion, ide, sap
    */
    'couriers' => explode(',', env('RAJAONGKIR_COURIERS', 'jne,sicepat,jnt,anteraja,pos,tiki,lion,ide,sap')),
];