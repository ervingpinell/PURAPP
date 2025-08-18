<?php

return [

    // Usa smtp por defecto (o failover en prod si quieres respaldo a log)
    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            // Para SMTPS (465) puedes usar 'scheme' => 'smtps'
            'scheme' => env('MAIL_SCHEME'),          // opcional
            'url' => env('MAIL_URL'),                // opcional
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            // IMPORTANTE: en la mayoría de proveedores usa 'tls' (587) o 'ssl' (465)
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'timeout' => null,
            'local_domain' => env(
                'MAIL_EHLO_DOMAIN',
                parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)
            ),
        ],

        'ses' => [ 'transport' => 'ses' ],
        'postmark' => [ 'transport' => 'postmark' ],
        'resend' => [ 'transport' => 'resend' ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [ 'transport' => 'array' ],

        // Útil en producción: intenta smtp y si falla, cae a log.
        'failover' => [
            'transport' => 'failover',
            'mailers' => ['smtp', 'log'],
        ],

        // Ejemplo si quieres repartir entre varios proveedores
        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => ['ses', 'postmark'],
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'info@greenvacationscr.com'),
        'name' => env('MAIL_FROM_NAME', 'Green Vacations CR'),
    ],

    // (Opcional) Destinatarios por defecto para ciertas funciones propias
    'to' => [
        'contact' => env('MAIL_TO_CONTACT', 'info@greenvacationscr.com'),
    ],
];
