<?php

return [

    'domain' => null,

    'path' => 'horizon',

    'use' => 'default',

    'prefix' => env('HORIZON_PREFIX', 'horizon'),

    // TIP: protege el dashboard de Horizon con auth en routes/web.php (ver nota abajo)
    'middleware' => ['web'],

    // Esperas máximas para métricas (incluye mail y maintenance)
    'waits' => [
        'redis:default'      => 10,
        'redis:mail'         => 10,
        'redis:maintenance'  => 10,
    ],

    'trim' => [
        'recent'        => 60,
        'pending'       => 60,
        'completed'     => 60,
        'recent_failed' => 10080, // 7 días
        'failed'        => 10080, // 7 días
        'monitored'     => 120,
    ],

    // Métricas por cola (agregamos 'mail')
    'metrics' => [
        'queue' => ['default', 'mail', 'maintenance'],
        'job'   => [],
    ],

    'fast_termination' => false,

    'memory_limit' => 128,

    'environments' => [

        // Producción: supervisores separados por cola
        'production' => [
            // Cola de correos: más timeout y procesos elásticos
            'supervisor-mail' => [
                'connection'      => 'redis',
                'queue'           => ['mail'],
                'balance'         => 'auto',
                'minProcesses'    => 2,
                'maxProcesses'    => 10,
                'balanceCooldown' => 3,
                'sleep'           => 3,
                'maxTime'         => 3600,
                'maxJobs'         => 0,
                'nice'            => 0,
                'tries'           => 3,
                'timeout'         => 120, // PDFs/email pueden tardar más
            ],

            // Cola por defecto
            'supervisor-default' => [
                'connection'      => 'redis',
                'queue'           => ['default'],
                'balance'         => 'auto',
                'minProcesses'    => 1,
                'maxProcesses'    => 5,
                'balanceCooldown' => 3,
                'sleep'           => 3,
                'maxTime'         => 3600,
                'maxJobs'         => 0,
                'nice'            => 0,
                'tries'           => 3,
                'timeout'         => 90,
            ],

            // Mantenimiento / jobs pesados diferidos
            'supervisor-maintenance' => [
                'connection'      => 'redis',
                'queue'           => ['maintenance'],
                'balance'         => 'simple',
                'minProcesses'    => 1,
                'maxProcesses'    => 2,
                'sleep'           => 5,
                'maxTime'         => 3600,
                'maxJobs'         => 0,
                'nice'            => 0,
                'tries'           => 3,
                'timeout'         => 120,
            ],
        ],

        // Otros entornos: un solo supervisor que escucha todas
        '*' => [
            'supervisor-1' => [
                'connection'   => 'redis',
                'queue'        => ['mail', 'default', 'maintenance'],
                'balance'      => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'sleep'        => 3,
                'tries'        => 3,
                'timeout'      => 120,
            ],
        ],
    ],
];
