<?php

return [

    'default' => env('MAIL_MAILER', 'graph'), // en producción usa siempre graph
    'booking_notify' => env('MAIL_NOTIFY_ADDRESS', 'info@greenvacationscr.com'),

    'mailers' => [

        'smtp' => env('RESCUE_OLD_SMTP', false)
            ? [
                // --- MODO RESCATE ---
                'transport' => 'graph',
            ]
            : [
                // --- MODO NORMAL ---
                'transport' => 'smtp',
                'scheme' => env('MAIL_SCHEME'),
                'url' => env('MAIL_URL'),
                'host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'port' => env('MAIL_PORT', 587),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),

                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'timeout' => null,
                'local_domain' => env(
                    'MAIL_EHLO_DOMAIN',
                    parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)
                ),
            ],

        // ---- Microsoft Graph API ----
        'graph' => [
            'transport' => 'graph',
        ],

        'ses' => ['transport' => 'ses'],
        'postmark' => ['transport' => 'postmark'],
        'resend' => ['transport' => 'resend'],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => ['transport' => 'array'],

        'failover' => [
            'transport' => 'failover',
            'mailers' => ['graph', 'log'],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => ['ses', 'postmark'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Email Addresses
    |--------------------------------------------------------------------------
    |
    | Here we define the standard email addresses for the application.
    | 
    | - from:     The 'noreply' address used to send transactional emails.
    | - reply_to: The 'info' address where customers should reply.
    | - notify:   The internal address that receives system alerts (bookings, etc).
    |
    */
    
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@greenvacationscr.com'),
        'name'    => env('MAIL_FROM_NAME', 'Company Name'),
    ],

    'reply_to' => [
        'address' => env('MAIL_REPLY_TO_ADDRESS', 'info@greenvacationscr.com'),
        'name'    => env('MAIL_FROM_NAME', 'Company Name'),
    ],

    'notifications' => [
        'address' => env('MAIL_NOTIFY_ADDRESS', 'info@greenvacationscr.com'),
    ],
];
