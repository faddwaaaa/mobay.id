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
    'withdraw_fee' => (int) env('PAYMENT_WITHDRAW_FEE', 6660),

    /*
    |--------------------------------------------------------------------------
    | Payment Method Fees
    |--------------------------------------------------------------------------
    |
    | Biaya tambahan untuk setiap metode pembayaran (dalam Rupiah)
    | 0 = gratis
    |
    */
    'payment_method_fees' => [
        'bank_transfer' => 0,
        'qris' => 500,
        'gopay' => 1000,
        'ovo' => 1000,
        'dana' => 1000,
        'shopeepay' => 1000,
        'credit_card' => 2500,
    ],

];
