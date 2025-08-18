<?php

use Illuminate\Support\Str;

if (!function_exists('viator_product_url')) {
    /**
     * Construye la URL pública de Viator para un producto.
     *
     * @param  string      $code        Código de producto (p.ej., 12732P10)
     * @param  int         $destId      ID de destino de Viator (p.ej., 821 = La Fortuna)
     * @param  string      $citySlug    Slug de ciudad (p.ej., 'La-Fortuna')
     * @param  string|null $productSlug Slug del producto (en inglés). Si viene null, se intenta generar.
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

        // Si no hay slug, lo generamos del nombre (mejor si es el nombre en inglés)
        if (!$productSlug) {
            $name = $fallbackName ?: $code; // último recurso
            // Reglas para acercarnos al slug de Viator:
            // - & y + -> "and"
            // - quitar símbolos, normalizar a ASCII, espacios -> guiones
            $normalized = str_ireplace(['&', '+'], ' and ', $name);
            $productSlug = Str::slug($normalized, '-'); // ej. "Arenal-Volcano-La-Fortuna-Waterfall-and-Lunch"
        }

        return "https://www.viator.com/tours/{$citySlug}/{$productSlug}/d{$destId}-" . rawurlencode($code);
    }
}
