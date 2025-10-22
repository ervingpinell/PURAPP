<?php

use Laravel\Fortify\Features;

$allowRegistration = (bool) (config('gv.allow_public_registration') ?? env('GV_ALLOW_PUBLIC_REGISTRATION', false));

return [

    'guard' => 'web',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,

    // Al estar en modo revisión solemos llevar a /admin
    'home' => '/admin',

    'prefix' => '',
    'domain' => null,

    'limiters' => [
        'login'      => null,
        'two-factor' => 'two-factor',
    ],

    'middleware' => ['web', \App\Http\Middleware\SetLocale::class],
    'views' => true,

    /*
     | Condicionamos el registro vía .env / config('gv.*')
     | - Con GV_ALLOW_PUBLIC_REGISTRATION=false, Fortify NO registra rutas de registro.
     */
    'features' => array_values(array_filter([
        $allowRegistration ? Features::registration() : null,
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::twoFactorAuthentication([
            'confirm'          => true,
            'confirmPassword'  => true,
            'window'           => 0,
        ]),
    ])),
];
