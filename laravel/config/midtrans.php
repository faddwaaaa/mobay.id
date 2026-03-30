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
    'is_production' => env('MIDTRANS_IS_PRODUCTION', true), // Changed to true for production

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
        'min_amount' => 20000,      // Minimum withdraw: Rp 20.000
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

    /*
    |--------------------------------------------------------------------------
    | Disbursement Configuration
    |--------------------------------------------------------------------------
    */

    // Disbursement API Key (different from payment keys)
    'disbursement_api_key' => env('MIDTRANS_DISBURSEMENT_API_KEY', ''),

    // Disbursement environment
    'disbursement_is_production' => env('MIDTRANS_DISBURSEMENT_IS_PRODUCTION', true),

    // Disbursement API base URL
    'disbursement_api_base_url' => env('MIDTRANS_DISBURSEMENT_IS_PRODUCTION', true)
        ? 'https://dashboard.midtrans.com/disbursement/v1'
        : 'https://dashboard.sandbox.midtrans.com/disbursement/v1',

    // Withdrawal fees
    'withdrawal_fee_flat' => 6000, // Rp 6.000 flat fee
    'withdrawal_fee_ppn_percent' => 11, // 11% PPN
];
