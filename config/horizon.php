<?php

return [

    'domain' => null,

    'path' => 'horizon',

    'use' => 'default',

    'prefix' => env('HORIZON_PREFIX', 'horizon'),

    'middleware' => ['web'],

    // Esperas máximas para métricas (añadimos maintenance)
    'waits' => [
        'redis:default'      => 10,
        'redis:maintenance'  => 10,
    ],

    'trim' => [
        'recent'        => 60,
        'pending'       => 60,
        'completed'     => 60,
        'recent_failed' => 10080,
        'failed'        => 10080,
        'monitored'     => 120,
    ],

    // Métricas por cola (incluye maintenance)
    'metrics' => [
        'queue' => ['default', 'maintenance'],
        'job'   => [],
    ],

    'fast_termination' => false,

    'memory_limit' => 128,

    'environments' => [

        // Producción: procesa default + maintenance
        'production' => [
            'supervisor-1' => [
                'connection'     => 'redis',
                'queue'          => ['default', 'maintenance'],
                'balance'        => 'auto',
                'minProcesses'   => 1,
                'maxProcesses'   => 3,
                'balanceCooldown'=> 3,
                'sleep'          => 3,
                'maxTime'        => 3600,
                'maxJobs'        => 0,
                'nice'           => 0,
                'tries'          => 3,
                'timeout'        => 60,
            ],
        ],

        // Otros entornos: también escucha ambas colas
        '*' => [
            'supervisor-1' => [
                'connection'   => 'redis',
                'queue'        => ['default', 'maintenance'],
                'balance'      => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'sleep'        => 3,
                'tries'        => 3,
            ],
        ],
    ],
];
