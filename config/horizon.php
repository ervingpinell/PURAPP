<?php

return [

    'domain' => null,

    'path' => 'horizon',

    'use' => 'default',

    'prefix' => env('HORIZON_PREFIX', 'horizon'),

    'middleware' => ['web'],

    // Esperas máximas para métricas
    'waits' => [
        'redis:default' => 10, // antes 60
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
        'queue' => ['default'],
        'job' => [],
    ],

    'fast_termination' => false,

    'memory_limit' => 128,

    'environments' => [

        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue'      => ['default'],
                'balance'    => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 3,
                'balanceCooldown' => 3,   // <–– nuevo (default era 60)
                'sleep'       => 3,       // <–– nuevo (default era 60)
                'maxTime'     => 3600,
                'maxJobs'     => 0,
                'nice'        => 0,
                'tries'       => 3,
                'timeout'     => 60,
            ],
        ],

        '*' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue'      => ['default'],
                'balance'    => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'sleep'       => 3,
                'tries'       => 3,
            ],
        ],
    ],
];
