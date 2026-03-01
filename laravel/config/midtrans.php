<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Midtrans payment gateway integration
    | Get your credentials from https://dashboard.midtrans.com
    |
    */

    // Enable/disable Midtrans integration
    'enabled' => env('MIDTRANS_ENABLED', false),

    // Midtrans merchant credentials
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'testing_mode'  => env('MIDTRANS_TESTING_MODE', false), 


    // Environment (sandbox or production)
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // API base URL
    'api_base_url' => env('MIDTRANS_IS_PRODUCTION', false) 
        ? 'https://api.midtrans.com'
        : 'https://app.sandbox.midtrans.com',

    // Snap base URL for redirect
    'snap_base_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com'
        : 'https://app.sandbox.midtrans.com',

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    */

    // Top-up limits
    'topup' => [
        'min_amount' => 10000,      // Minimum top-up: Rp 10.000
        'max_amount' => 10000000,   // Maximum top-up: Rp 10.000.000
    ],

    // Withdraw limits
    'withdrawal' => [
        'min_amount' => 10000,      // Minimum withdraw: Rp 10.000
        'max_amount' => 50000000,   // Maximum withdraw: Rp 50.000.000
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback Configuration
    |--------------------------------------------------------------------------
    */

    // Callback endpoint URL (Midtrans will POST to this)
    'callback_url' => env('MIDTRANS_CALLBACK_URL', 'http://localhost:8000/callback/midtrans'),

    // Notification URL (for Snap redirect)
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', 'http://localhost:8000/callback/midtrans'),

    // Finish URL (success redirect)
    'finish_url' => env('MIDTRANS_FINISH_URL', 'http://localhost:8000/dashboard/topup/success'),

    // Error URL (error redirect)
    'error_url' => env('MIDTRANS_ERROR_URL', 'http://localhost:8000/dashboard/topup/error'),

    // Unfinish URL (pending redirect)
    'unfinish_url' => env('MIDTRANS_UNFINISH_URL', 'http://localhost:8000/dashboard/topup/pending'),

    /*
    |--------------------------------------------------------------------------
    | Transaction Configuration
    |--------------------------------------------------------------------------
    */

    // Transaction item name (for Midtrans dashboard)
    'item_name' => 'Top Up Balance - Payou.id',

    // Currency
    'currency' => 'IDR',

    // Language
    'language' => 'id',

    // Allowed payment methods
    'payment_methods' => [
        'credit_card' => true,
        'bank_transfer' => true,
        'e_wallet' => true,
        'qris' => true,
        'akulaku' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */

    // Verify SSL certificate
    'verify_ssl' => true,

    // Transaction timeout in minutes
    'transaction_timeout' => 15,

    // Log all transactions (for debugging)
    'log_transactions' => true,
];
