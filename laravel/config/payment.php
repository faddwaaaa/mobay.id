<?php

// File: config/payment.php
// Jalankan: php artisan config:clear setelah edit .env

return [

    /*
    |--------------------------------------------------------------------------
    | Dev / Testing Mode
    |--------------------------------------------------------------------------
    |
    | Jika true:
    |  - Verifikasi rekening tidak hit Midtrans (mock response)
    |  - PIN menggunakan dev_pin di bawah, bukan hash dari database
    |
    | WAJIB false di production. Atur via .env:
    |   PAYMENT_DEV_MODE=true   ← untuk development / testing
    |   PAYMENT_DEV_MODE=false  ← untuk production
    |
    */
    'dev_mode' => env('PAYMENT_DEV_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | PIN untuk Dev Mode
    |--------------------------------------------------------------------------
    |
    | PIN yang digunakan saat dev_mode=true.
    | Default: 123456
    |
    |   PAYMENT_DEV_PIN=123456
    |
    */
    'dev_pin' => env('PAYMENT_DEV_PIN', '123456'),

    /*
    |--------------------------------------------------------------------------
    | Platform Fee
    |--------------------------------------------------------------------------
    |
    | payment_fee_percent: fee checkout untuk pembeli (dalam persen)
    | withdraw_fee: biaya tetap per penarikan saldo user (dalam Rupiah)
    |
    */
    'payment_fee_percent' => (float) env('PAYMENT_FEE_PERCENT', 5),
    'withdraw_fee' => (int) env('PAYMENT_WITHDRAW_FEE', 3000),

];
