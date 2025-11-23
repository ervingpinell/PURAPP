<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    // Default gateway to use
    'default_gateway' => env('PAYMENT_GATEWAY', 'stripe'),

    // Supported currencies
    'currencies' => [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
            'stripe_divisor' => 100, // Stripe uses cents
        ],
        'CRC' => [
            'name' => 'Costa Rican Colón',
            'symbol' => '₡',
            'decimal_places' => 2,
            'stripe_divisor' => 1, // CRC doesn't use cents in Stripe
        ],
    ],

    // Default currency
    'default_currency' => env('PAYMENT_CURRENCY', 'USD'),

    // Payment timeout (minutes)
    'payment_timeout' => env('PAYMENT_TIMEOUT', 30),

    // Auto-confirm booking on successful payment
    'auto_confirm' => env('PAYMENT_AUTO_CONFIRM', true),

    /*
    |--------------------------------------------------------------------------
    | Gateway Configurations
    |--------------------------------------------------------------------------
    */

    'gateways' => [
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', true),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'api_version' => '2023-10-16',
            'capture_method' => 'automatic', // automatic or manual
        ],

        'tilopay' => [
            'enabled' => env('TILOPAY_ENABLED', false),
            'merchant_id' => env('TILOPAY_MERCHANT_ID'),
            'api_key' => env('TILOPAY_API_KEY'),
            'api_secret' => env('TILOPAY_API_SECRET'),
            'base_url' => env('TILOPAY_BASE_URL', 'https://api.tilopay.com'),
            'webhook_secret' => env('TILOPAY_WEBHOOK_SECRET'),
        ],

        'banco_nacional' => [
            'enabled' => env('BN_ENABLED', false),
            'merchant_id' => env('BN_MERCHANT_ID'),
            'terminal_id' => env('BN_TERMINAL_ID'),
            'api_key' => env('BN_API_KEY'),
            'api_secret' => env('BN_API_SECRET'),
            'base_url' => env('BN_BASE_URL', 'https://api.bncr.fi.cr/payments/v1'),
            'webhook_secret' => env('BN_WEBHOOK_SECRET'),
        ],

        'bac' => [
            'enabled' => env('BAC_ENABLED', false),
            'merchant_id' => env('BAC_MERCHANT_ID'),
            'api_key' => env('BAC_API_KEY'),
            'api_secret' => env('BAC_API_SECRET'),
            'base_url' => env('BAC_BASE_URL', 'https://api.baccredomatic.com/v1'),
            'webhook_secret' => env('BAC_WEBHOOK_SECRET'),
        ],

        'bcr' => [
            'enabled' => env('BCR_ENABLED', false),
            'merchant_id' => env('BCR_MERCHANT_ID'),
            'commerce_id' => env('BCR_COMMERCE_ID'),
            'api_key' => env('BCR_API_KEY'),
            'api_secret' => env('BCR_API_SECRET'),
            'base_url' => env('BCR_BASE_URL', 'https://api.bancobcr.com/payments/v1'),
            'webhook_secret' => env('BCR_WEBHOOK_SECRET'),
        ],

        'paypal' => [
            'enabled' => false, // Controlled via AppServiceProvider from DB setting
            'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
            'brand_name' => env('APP_NAME', 'Tour Booking'),
            'landing_page' => 'LOGIN', // LOGIN, BILLING, or NO_PREFERENCE
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Routes
    |--------------------------------------------------------------------------
    */

    'webhook_routes' => [
        'stripe' => '/webhooks/stripe',
        'tilopay' => '/webhooks/tilopay',
        'banco_nacional' => '/webhooks/banco-nacional',
        'bac' => '/webhooks/bac',
        'bcr' => '/webhooks/bcr',
        'paypal' => '/webhooks/paypal',
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Settings
    |--------------------------------------------------------------------------
    */

    'refunds' => [
        'enabled' => env('PAYMENT_REFUNDS_ENABLED', true),
        'partial_enabled' => env('PAYMENT_PARTIAL_REFUNDS_ENABLED', true),
        'auto_refund_on_cancel' => env('PAYMENT_AUTO_REFUND_ON_CANCEL', false),
    ],
];
