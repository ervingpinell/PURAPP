<?php

use Laravel\Fortify\Features;

return [

    'guard' => 'web',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,
    'home' => '/admin',

    'prefix' => '',
    'domain' => null,

    'limiters' => [
        'login'      => null,
        'two-factor' => 'two-factor',
    ],

    'middleware' => ['web', \App\Http\Middleware\SetLocale::class],
    'views' => true,

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::twoFactorAuthentication([
            'confirm'          => true,
            'confirmPassword'  => true,
            'window'           => 0,
        ]),
    ],
];
