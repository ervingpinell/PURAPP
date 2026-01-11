<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'microsoft' => [
        // Credenciales de Azure AD App
        'tenant_id'     => env('MSFT_TENANT_ID'),
        'client_id'     => env('MSFT_CLIENT_ID'),
        'client_secret' => env('MSFT_CLIENT_SECRET'),

        // Buzón desde el cual enviar (User Principal Name)
        // Buzón desde el cual enviar (User Principal Name)
        // Debe tener permisos en la AAC (Application Access Policy)
        'sender_upn'    => env('MSFT_SENDER_UPN', env('MAIL_FROM_ADDRESS', 'noreply@greenvacationscr.com')),

        // Reply-To por defecto (opcional)
        'reply_to'      => env('MSFT_REPLY_TO', env('MAIL_REPLY_TO_ADDRESS', 'info@greenvacationscr.com')),
    ],
    // === Reviews / Partners ===

    'viator' => [
        'url'        => env('VIATOR_API_URL', ''),
        'key'        => env('VIATOR_API_KEY', ''),
        'key_header' => env('VIATOR_API_KEY_HEADER', 'exp-api-key'),
    ],

    // Google Places (Reviews por Place Details API; ojo con ToS y uso/caching)
    'google_places' => [
        'url' => env('GOOGLE_PLACES_DETAILS_URL', 'https://maps.googleapis.com/maps/api/place/details/json'),
        'key' => env('GOOGLE_PLACES_API_KEY', ''),
        // campo que enviarás como product_code en tu app (normalmente el place_id)
        'product_field' => env('GOOGLE_PLACES_PRODUCT_FIELD', 'place_id'),
    ],

    // GetYourGuide (requiere partner API; reemplaza URL/headers según tu contrato)
    'gyg' => [
        'url'        => env('GYG_API_URL', ''),
        'key'        => env('GYG_API_KEY', ''),
        'key_header' => env('GYG_API_KEY_HEADER', 'X-API-KEY'),
    ],

    // Expedia Group (no hay endpoint público estándar de reviews; usa tu contrato)
    'expedia' => [
        'url'        => env('EXPEDIA_API_URL', ''),
        'key'        => env('EXPEDIA_API_KEY', ''),
        'key_header' => env('EXPEDIA_API_KEY_HEADER', 'Authorization'),
    ],

    // Booking.com (normalmente no expone reviews públicamente; usa tu integración)
    'booking' => [
        'url'        => env('BOOKING_API_URL', ''),
        'key'        => env('BOOKING_API_KEY', ''),
        'key_header' => env('BOOKING_API_KEY_HEADER', 'Authorization'),
    ],

    // TripAdvisor (API oficial suele ir vía RapidAPI/partners; ajusta según contrato)
    'tripadvisor' => [
        'url'        => env('TRIPADVISOR_API_URL', ''),
        'key'        => env('TRIPADVISOR_API_KEY', ''),
        'key_header' => env('TRIPADVISOR_API_KEY_HEADER', 'X-API-KEY'),
    ],

    // === Otros servicios ya presentes ===
    'deepl' => [
        'auth_key'   => env('DEEPL_AUTH_KEY'),
        'formality'  => env('DEEPL_FORMALITY', 'default'),
        'en_variant' => env('DEEPL_EN_VARIANT', 'en-US'),
        'pt_variant' => env('DEEPL_PT_VARIANT', 'pt-BR'),
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY', ''),
        'secret'   => env('TURNSTILE_SECRET', ''),
    ],

    'google' => [
        'analytics_id' => env('GOOGLE_ANALYTICS_ID', null),
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY', ''),
        'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
        'enabled' => env('RECAPTCHA_ENABLED', false),
        'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
        'fail_open' => env('RECAPTCHA_FAIL_OPEN', false), // Allow requests if reCAPTCHA service is down
    ],

    'meta' => [
        'pixel_id' => env('META_PIXEL_ID', null),
    ],
];
