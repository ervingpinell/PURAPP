<?php

use Laravel\Fortify\Features;

return [

    'guard' => 'web',
    'passwords' => 'users',

    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,

    // Este valor realmente lo ignora tu LoginResponse personalizado,
    // pero está bien dejarlo apuntando al admin.
    'home' => '/admin',

    'prefix' => '',
    'domain' => null,

    // Mantén SetLocale aquí para que el switcher funcione en todas las pantallas de Fortify.
    'middleware' => ['web', \App\Http\Middleware\SetLocale::class],

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::twoFactorAuthentication([
            'confirm' => true,           // Obliga a CONFIRMAR tras escanear el QR
            'confirmPassword' => true,   // Pide confirmar password para activar/desactivar 2FA
            'window' => 1,               // +/- 30s de margen (opcional pero recomendado)
        ]),
    ],

];
