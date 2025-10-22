<?php

return [
    'domain' => null, // usa el dominio del site

    'path' => 'horizon', // panel en /horizon

    'use' => 'default',  // conexión redis por defecto

    'prefix' => env('HORIZON_PREFIX', 'horizon'),

    'middleware' => ['web'], // usa session/csrf si lo necesitas

    'waits' => [
        'redis:default' => 60,
    ],

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 120,
    ],

    'metrics' => [
        'queue' => ['default'], // agrega más colas si las usas
        'job' => [],
    ],

    'fast_termination' => false,

    'memory_limit' => 128,

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue'      => ['default'], // o ['high','default','low']
                'balance'    => 'auto',
                'maxProcesses' => 3,
                'maxTime'    => 3600,
                'maxJobs'    => 0,
                'nice'       => 0,
                'tries'      => 3,
                'timeout'    => 60,
            ],
        ],

        // cualquier otro entorno: 1 proceso
        '*' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue'      => ['default'],
                'balance'    => 'simple',
                'maxProcesses' => 1,
                'tries'      => 3,
            ],
        ],
    ],
];
