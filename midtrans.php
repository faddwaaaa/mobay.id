<?php

return [
    /**
     * Midtrans Server Key
     * Get your server key from Midtrans Dashboard
     * https://dashboard.midtrans.com/settings/config_info
     */
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    
    /**
     * Midtrans Client Key
     */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    
    /**
     * Midtrans Merchant ID
     */
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    
    /**
     * Set to true for production environment
     * Set to false for sandbox/testing
     */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    
    /**
     * Enable sanitization
     */
    'is_sanitized' => true,
    
    /**
     * Enable 3D Secure
     */
    'is_3ds' => true,
    
    /**
     * Iris API Configuration (for payouts)
     */
    'iris' => [
        'api_key' => env('MIDTRANS_IRIS_API_KEY', env('MIDTRANS_SERVER_KEY')),
        'approval_required' => env('MIDTRANS_IRIS_APPROVAL_REQUIRED', false),
    ],
];
