<?php

use Illuminate\Support\Str;

if (!function_exists('supported_locales')) {
    function supported_locales(): array
    {
        $arr = config('i18n.supported_locales');

        if (is_array($arr) && !empty($arr)) {
            $arr = array_map(fn($l) => strtolower(trim((string) $l)), $arr);
            $arr = array_values(array_unique(array_filter($arr)));
            return $arr ?: [strtolower(config('app.locale', 'es'))];
        }

        return [strtolower(config('app.locale', 'es'))];
    }
}

if (!function_exists('localized_route')) {
    /**
     * Genera una ruta con prefijo de locale
     */
    function localized_route(string $name, $parameters = [], ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $locales = array_keys(config('routes.locales', []));

        if (!in_array($locale, $locales, true)) {
            $locale = config('routes.default_locale', 'es');
        }

        // Agregar prefijo de locale al nombre de la ruta
        return route("{$locale}.{$name}", $parameters);
    }
}

if (!function_exists('current_locale_prefix')) {
    /**
     * Obtiene el prefijo del locale actual
     */
    function current_locale_prefix(): string
    {
        $locale = app()->getLocale();
        $locales = config('routes.locales', []);

        return $locales[$locale]['prefix'] ?? $locale;
    }
}

if (!function_exists('viator_product_url')) {
    function viator_product_url(
        string $code,
        int $destId = 821,
        string $citySlug = 'La-Fortuna',
        ?string $productSlug = null,
        ?string $fallbackName = null
    ): string {
        $code     = trim($code);
        $citySlug = $citySlug ?: 'La-Fortuna';

        if (!$productSlug) {
            $name = $fallbackName ?: $code;
            $normalized  = str_ireplace(['&', '+'], ' and ', $name);
            $productSlug = Str::slug($normalized, '-');
        }

        return "https://www.viator.com/tours/{$citySlug}/{$productSlug}/d{$destId}-" . rawurlencode($code);
    }
}

if (!function_exists('cookies_accepted')) {
    /**
     * True si el usuario aceptó cookies. La fuente de la verdad es la cookie "gv_cookie_consent".
     */
    function cookies_accepted(): bool
    {
        $cookie = request()->cookie('gv_cookie_consent');
        return (string) $cookie === '1';
    }

}
