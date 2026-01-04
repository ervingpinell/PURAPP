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

        'paypal' => [
            'enabled' => env('PAYPAL_ENABLED', false),
            'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
            'brand_name' => env('APP_NAME', 'Tour Booking'),
            'landing_page' => 'LOGIN', // LOGIN, BILLING, or NO_PREFERENCE
        ],

        'alignet' => [
            'enabled' => env('ALIGNET_ENABLED', false),
            'acquirer_id' => env('ALIGNET_ACQUIRER_ID', '99'),
            'commerce_id' => env('ALIGNET_COMMERCE_ID', '8056'),
            'secret_key' => env('ALIGNET_SECRET_KEY'),
            'wallet_entity_id' => env('ALIGNET_WALLET_ENTITY_ID', '580'),
            'wallet_secret_key' => env('ALIGNET_WALLET_SECRET_KEY'),
            'environment' => env('ALIGNET_ENVIRONMENT', 'testing'), // 'testing' or 'production'
            'urls' => [
                'testing' => [
                    // Base URL para openModal() - según ejemplo oficial línea 52
                    'base' => 'https://integracion.alignetsac.com/',
                    // URL del script JS que se carga en el <head>
                    'vpos2_script' => 'https://integracion.alignetsac.com/VPOS2/js/modalcomercio.js',
                    // URL del servicio SOAP de Wallet
                    'wallet_wsdl' => 'https://integracion.alignetsac.com/WALLETWS/services/WalletCommerce?wsdl',
                    // URL de la API REST para consultar transacciones
                    'query_api' => 'https://integracion.alignetsac.com/VPOS2/rest/operationAcquirer/consulte',
                ],
                'production' => [
                    'base' => 'https://vpayment.verifika.com/',
                    'vpos2_script' => 'https://vpayment.verifika.com/VPOS2/js/modalcomercio.js',
                    'wallet_wsdl' => 'https://www.pay-me.pe/WALLETWS/services/WalletCommerce?wsdl',
                    'query_api' => 'https://vpayment.verifika.com/VPOS2/rest/operationAcquirer/consulte',
                ]
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Routes
    |--------------------------------------------------------------------------
    */

    'webhook_routes' => [
        'stripe' => '/webhooks/stripe',
        'paypal' => '/webhooks/paypal',
        'alignet' => '/webhooks/alignet',
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
