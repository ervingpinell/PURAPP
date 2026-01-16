<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | Información básica de la compañía que se utiliza en todo el sitio.
    | Estos valores pueden ser sobrescritos mediante variables de entorno.
    |
    */

    'name' => env('COMPANY_NAME', 'Green Vacations Costa Rica'),
    'short_name' => env('COMPANY_SHORT_NAME', 'Green Vacations CR'),
    'brand_name' => env('COMPANY_BRAND_NAME', 'GV'),
    'logo_url'   => env('COMPANY_LOGO_URL', '/images/logoCompanyWhite.png'),

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */

    'phone' => env('COMPANY_PHONE', '+506 2479 1471'),
    'phone_raw' => env('COMPANY_PHONE_RAW', '50624791471'),
    'email' => env('COMPANY_EMAIL', 'info@greenvacationscr.com'),

    /*
    |--------------------------------------------------------------------------
    | Address Information
    |--------------------------------------------------------------------------
    */

    'address' => [
        'street' => env('COMPANY_ADDRESS_STREET', ''),
        'city' => env('COMPANY_ADDRESS_CITY', 'La Fortuna'),
        'state' => env('COMPANY_ADDRESS_STATE', 'San Carlos'),
        'country' => env('COMPANY_ADDRESS_COUNTRY', 'Costa Rica'),
        'country_code' => env('COMPANY_COUNTRY_CODE', 'CR'),
        'phone_code' => env('COMPANY_PHONE_CODE', '+506'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media
    |--------------------------------------------------------------------------
    */

    'social' => [
        'facebook' => env('COMPANY_FACEBOOK', 'https://www.facebook.com/greenvacationscr'),
        'instagram' => env('COMPANY_INSTAGRAM', 'https://www.instagram.com/greenvacationscr'),
        'twitter' => env('COMPANY_TWITTER', ''),
        'linkedin' => env('COMPANY_LINKEDIN', ''),
        'youtube' => env('COMPANY_YOUTUBE', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Hours
    |--------------------------------------------------------------------------
    */

    'business_hours' => [
        'timezone' => env('COMPANY_TIMEZONE', 'America/Costa_Rica'),
        'schedule' => env('COMPANY_SCHEDULE', '07:00-19:30'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO & Marketing
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'meta_description' => env('COMPANY_META_DESCRIPTION', 'Descubre los mejores tours sostenibles en La Fortuna y Arenal con Green Vacations Costa Rica. Reserva tu aventura con responsabilidad ecológica.'),
        'meta_keywords' => env('COMPANY_META_KEYWORDS', 'tours Costa Rica, turismo ecológico, La Fortuna, Arenal, viajes sostenibles, Green Vacations CR'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Map Configuration
    |--------------------------------------------------------------------------
    */

    'map' => [
        'latitude' => env('COMPANY_MAP_LAT', '10.4677'),
        'longitude' => env('COMPANY_MAP_LNG', '-84.6431'),
        'zoom' => env('COMPANY_MAP_ZOOM', '15'),
    ],

];
