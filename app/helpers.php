<?php

use Illuminate\Support\Str;

if (!function_exists('supported_locales')) {
    /**
     * Locales soportados (para UI / selectores).
     * Lee de config('i18n.supported_locales') y normaliza a minúsculas.
     */
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

/* =========================
   Mapeos de locale/prefijo
   ========================= */

if (!function_exists('locale_to_prefix')) {
    /**
     * Convierte un locale interno (ej. 'pt' o 'pt_BR') al prefijo de rutas (2 letras).
     * E.g. 'pt_BR' -> 'pt'
     */
    function locale_to_prefix(string $locale): string
    {
        $s = strtolower(str_replace('-', '_', trim($locale)));
        return substr($s, 0, 2);
    }
}

if (!function_exists('prefix_to_locale')) {
    /**
     * Convierte el prefijo de rutas a un locale interno de Laravel.
     * Para simplificar, devolvemos el mismo prefijo (ej. 'pt' -> 'pt').
     */
    function prefix_to_locale(string $prefix): string
    {
        return strtolower(trim($prefix));
    }
}

if (!function_exists('localized_route')) {
    /**
     * Genera una ruta con prefijo de locale.
     * - Usa el prefijo (2 letras) incluso si el locale interno fuera 'pt_BR'.
     */
    function localized_route(string $name, $parameters = [], ?string $locale = null): string
    {
        $internal = $locale ?? app()->getLocale();   // p.ej. 'pt' ó 'pt_BR'
        $prefix   = locale_to_prefix($internal);     // 'pt'
        $locales  = array_keys(config('routes.locales', [])); // ['es','en','fr','de','pt']

        if (!in_array($prefix, $locales, true)) {
            $prefix = config('routes.default_locale', 'es');
        }

        return route("{$prefix}.{$name}", $parameters);
    }
}

if (!function_exists('current_locale_prefix')) {
    /**
     * Obtiene el prefijo del locale actual (2 letras), usando config('routes.locales').
     */
    function current_locale_prefix(): string
    {
        $internal      = app()->getLocale();            // 'pt' o 'pt_BR'
        $prefix        = locale_to_prefix($internal);   // 'pt'
        $routesLocales = (array) config('routes.locales', []);

        return $routesLocales[$prefix]['prefix'] ?? $prefix;
    }
}

/* =========================
   Fallback de traducciones DB
   ========================= */

if (!function_exists('pick_db_translation')) {
    /**
     * Busca una traducción en una relación/colección Eloquent con fallback:
     * 1) exacto por locale (ej. 'pt' o 'pt_BR')
     * 2) si el locale es 'pt', prueba alias 'pt_BR' si no existía exacto
     * 3) fallback global
     *
     * @param \Illuminate\Support\Collection|array|null $translations
     * @param string $locale   Locale actual (p.ej. 'pt')
     * @param string $fallback Locale fallback (p.ej. 'es')
     * @return mixed|null      Modelo/array de traducción o null
     */
    function pick_db_translation($translations, string $locale, string $fallback)
    {
        $col = $translations ?? collect();
        if (is_array($col)) $col = collect($col);

        $norm = strtolower(str_replace('-', '_', $locale));
        $short = substr($norm, 0, 2);

        // 1) exacto (pt o pt_BR)
        $hit = $col->firstWhere('locale', $norm)
            ?: $col->firstWhere('locale', $short);

        // 2) alias pt_BR si el short es 'pt'
        if (!$hit && $short === 'pt') {
            $hit = $col->firstWhere('locale', 'pt_BR')
                ?: $col->firstWhere('locale', 'pt-br');
        }

        // 3) fallback estándar
        if (!$hit) {
            $hit = $col->firstWhere('locale', strtolower(str_replace('-', '_', $fallback)))
                ?: $col->firstWhere('locale', substr(strtolower($fallback), 0, 2));
        }

        return $hit;
    }
}

/* =========================
   Otros helpers ya existentes
   ========================= */

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

/* =========================
   Settings Helpers
   ========================= */

if (!function_exists('setting')) {
    /**
     * Get a setting value from database with caching
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return \Illuminate\Support\Facades\Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = \App\Models\Setting::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }
}

if (!function_exists('setting_update')) {
    /**
     * Update a setting value and clear cache
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    function setting_update(string $key, $value): bool
    {
        $updated = \App\Models\Setting::where('key', $key)->update([
            'value' => $value,
            'updated_by' => auth()->id(),
        ]);

        \Illuminate\Support\Facades\Cache::forget("setting.{$key}");

        return (bool) $updated;
    }
}

if (!function_exists('settings_by_category')) {
    /**
     * Get all settings grouped by category
     *
     * @param string|null $category
     * @return \Illuminate\Support\Collection
     */
    function settings_by_category(?string $category = null)
    {
        $query = \App\Models\Setting::orderBy('category')->orderBy('sort_order');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get()->groupBy('category');
    }
}
