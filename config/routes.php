<?php

return [
    'locales' => [
        'es' => ['prefix' => 'es', 'name' => 'Español'],
        'en' => ['prefix' => 'en', 'name' => 'English'],
        'fr' => ['prefix' => 'fr', 'name' => 'Français'],
        'de' => ['prefix' => 'de', 'name' => 'Deutsch'],
        'pt' => ['prefix' => 'pt', 'name' => 'Português'],
    ],

    'default_locale' => 'es',

    // Rutas que NO necesitan prefijo de idioma
    'locale_independent' => [
        'login',
        'register',
        'password.*',
        'verification.*',
        'admin.*',
    ],
];
