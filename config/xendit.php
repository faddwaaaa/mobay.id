<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Xendit Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Xendit payment gateway integration
    | Get your credentials from https://dashboard.xendit.co
    |
    */

    // Enable/disable Xendit integration
    'enabled' => env('XENDIT_ENABLED', true),

    // Xendit API credentials
    'api_key' => env('XENDIT_API_KEY', ''),
    'secret_key' => env('XENDIT_SECRET_KEY', ''),
    
    // Customer business ID for Xendit (optional, advanced feature)
    'business_id' => env('XENDIT_BUSINESS_ID', ''),

    // Environment (production or development)
    'is_production' => env('XENDIT_IS_PRODUCTION', false),

    // API base URL
    'api_base_url' => 'https://api.xendit.co',

    // Callback/Webhook URL
    'webhook_url' => env('APP_URL', '') . '/webhook/xendit/invoice',
    'callback_token' => env('XENDIT_CALLBACK_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    */

    // Top-up limits
    'topup' => [
        'min_amount' => 10000,           // Minimum top-up: Rp 10.000
        'max_amount' => 10000000,        // Maximum top-up: Rp 10.000.000
    ],

    // Withdrawal limits
    'withdrawal' => [
        'min_amount' => 20000,           // Minimum withdrawal: Rp 20.000
        'max_amount' => 50000000,        // Maximum withdrawal: Rp 50.000.000
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Payment Methods
    |--------------------------------------------------------------------------
    */

    // Payment channels (payment methods)
    'payment_channels' => [
        // Virtual Account
        'VIRTUAL_ACCOUNT_BCA' => [
            'label' => 'BCA Virtual Account',
            'type' => 'virtual_account',
            'code' => 'BCA',
            'display_name' => 'BCA',
        ],
        'VIRTUAL_ACCOUNT_BNI' => [
            'label' => 'BNI Virtual Account',
            'type' => 'virtual_account',
            'code' => 'BNI',
            'display_name' => 'BNI',
        ],
        'VIRTUAL_ACCOUNT_MANDIRI' => [
            'label' => 'Mandiri Virtual Account',
            'type' => 'virtual_account',
            'code' => 'MANDIRI',
            'display_name' => 'Mandiri',
        ],
        'VIRTUAL_ACCOUNT_PERMATA' => [
            'label' => 'Permata Virtual Account',
            'type' => 'virtual_account',
            'code' => 'PERMATA_NETPAY',
            'display_name' => 'Permata',
        ],

        // QRIS
        'QRIS' => [
            'label' => 'QRIS',
            'type' => 'qris',
            'code' => 'QRIS',
            'display_name' => 'QRIS',
        ],

        // E-Wallet
        'DANA' => [
            'label' => 'DANA E-Wallet',
            'type' => 'ewallet',
            'code' => 'DANA',
            'display_name' => 'DANA',
        ],
        'OVO' => [
            'label' => 'OVO E-Wallet',
            'type' => 'ewallet',
            'code' => 'OVO',
            'display_name' => 'OVO',
        ],
        'LINKAJA' => [
            'label' => 'LinkAja E-Wallet',
            'type' => 'ewallet',
            'code' => 'LINKAJA',
            'display_name' => 'LinkAja',
        ],

        // Retail Outlets
        'INDOMARET' => [
            'label' => 'Indomaret',
            'type' => 'retail',
            'code' => 'INDOMARET',
            'display_name' => 'Indomaret',
        ],
        'ALFAMART' => [
            'label' => 'Alfamart',
            'type' => 'retail',
            'code' => 'ALFAMART',
            'display_name' => 'Alfamart',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Disbursement (Payout) Configuration
    |--------------------------------------------------------------------------
    */

    'disbursement' => [
        'enabled' => env('XENDIT_DISBURSEMENT_ENABLED', true),
        'api_key' => env('XENDIT_DISBURSEMENT_API_KEY', ''),
        'fee_flat' => 4000,                    // Flat fee per disbursement
        'fee_percentage' => 0,                 // Percentage fee (0% for Xendit)
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Banks for Disbursement
    |--------------------------------------------------------------------------
    */

    'banks' => [
        'BCA' => ['code' => 'BCA', 'name' => 'Bank Central Asia'],
        'BNI' => ['code' => 'BNI', 'name' => 'Bank Negara Indonesia'],
        'BRI' => ['code' => 'BRI', 'name' => 'Bank Rakyat Indonesia'],
        'MANDIRI' => ['code' => 'MANDIRI', 'name' => 'Bank Mandiri'],
        'PERMATA' => ['code' => 'PERMATA', 'name' => 'Bank Permata'],
        'DANAMON' => ['code' => 'DANAMON', 'name' => 'Bank Danamon'],
        'CIMB' => ['code' => 'CIMB', 'name' => 'Bank CIMB Niaga'],
        'MEGA' => ['code' => 'MEGA', 'name' => 'Bank Mega'],
        'PANIN' => ['code' => 'PANIN', 'name' => 'Bank Panin'],
        'MUAMALAT' => ['code' => 'MUAMALAT', 'name' => 'Bank Muamalat'],
        'OCBC' => ['code' => 'OCBC', 'name' => 'Bank OCBC NISP'],
        'MAYBANK' => ['code' => 'MAYBANK', 'name' => 'Maybank'],
        'SINARMAS' => ['code' => 'SINARMAS', 'name' => 'Bank Sinar Mas'],
        'BNI_SYARIAH' => ['code' => 'BNI_SYARIAH', 'name' => 'BNI Syariah'],
        'BSI' => ['code' => 'BSI', 'name' => 'Bank Syariah Indonesia'],
    ],
];
