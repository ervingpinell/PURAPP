<?php

use Illuminate\Support\Str;

if (!function_exists('supported_locales')) {
    /**
     * Devuelve los locales soportados desde config('i18n.supported_locales').
     * Siempre retorna un array NO vacío. Si la config no existe o está vacía,
     * cae a [config('app.locale', 'es')].
     *
     * @return array<string>
     */
    function supported_locales(): array
    {
        $arr = config('i18n.supported_locales');

        if (is_array($arr) && !empty($arr)) {
            // Normaliza: trim + lowercase + únicos
            $arr = array_map(fn($l) => strtolower(trim((string) $l)), $arr);
            $arr = array_values(array_unique(array_filter($arr)));
            return $arr ?: [strtolower(config('app.locale', 'es'))];
        }

        return [strtolower(config('app.locale', 'es'))];
    }
}

if (!function_exists('viator_product_url')) {
    /**
     * Construye la URL pública de Viator para un producto.
     *
     * @param  string      $code         Código de producto (p.ej., 12732P10)
     * @param  int         $destId       ID de destino de Viator (p.ej., 821 = La Fortuna)
     * @param  string      $citySlug     Slug de ciudad (p.ej., 'La-Fortuna')
     * @param  string|null $productSlug  Slug del producto (en inglés). Si viene null, se intenta generar.
     * @param  string|null $fallbackName Nombre a usar para generar slug si no se pasa $productSlug
     * @return string
     */
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
