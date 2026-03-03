<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RajaOngkir API Key
    |--------------------------------------------------------------------------
    | Isi di file .env:
    |   RAJAONGKIR_API_KEY=your_api_key_here
    |   RAJAONGKIR_TYPE=starter   (starter/basic/pro)
    */
    'api_key'  => env('RAJAONGKIR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL sesuai tipe akun
    |--------------------------------------------------------------------------
    | starter : https://api.rajaongkir.com/starter
    | basic   : https://api.rajaongkir.com/basic
    | pro     : https://pro.rajaongkir.com/api
    */
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com/starter'),

    /*
    |--------------------------------------------------------------------------
    | Kurir yang tersedia
    |--------------------------------------------------------------------------
    | starter mendukung: jne, pos, tiki
    | basic/pro mendukung lebih banyak: sicepat, jnt, anteraja, dsb
    */
    'couriers' => explode(',', env('RAJAONGKIR_COURIERS', 'jne,pos,tiki')),
];