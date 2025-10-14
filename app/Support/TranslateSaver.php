<?php

namespace App\Support;

class TranslateSaver
{
    /**
     * Guarda traducciones normalizadas para ES, EN, FR, PT, DE.
     *
     * Convenciones:
     * - La columna 'locale' SIEMPRE se guarda como corto: es | en | fr | pt | de.
     * - $fields: [ 'campo' => 'valorOriginal', ... ]
     * - $translations: [ 'campo' => ['es' => '...', 'pt' => '...', 'pt_br' => '...'], ... ]
     * - Hace upsert por [$fk, locale] para evitar duplicados.
     */
    public static function save(string $modelClass, string $fk, int $id, array $fields, array $translations): void
    {
        // Locales oficiales (corto)
        $targets = ['es', 'en', 'fr', 'pt', 'de'];

        foreach ($targets as $short) {
            $payload = [
                $fk     => $id,
                'locale'=> $short,
            ];

            foreach ($fields as $field => $original) {
                $val = self::pickTranslationValue($translations, $field, $short);

                // Fallback al original si viene vacío o no existe
                if (self::isEmptyStringish($val)) {
                    $val = $original;
                }

                // Normaliza strings (trim; vacío => null)
                if (is_string($val)) {
                    $val = trim($val);
                    if ($val === '') {
                        $val = null;
                    }
                }

                $payload[$field] = $val;
            }

            // Upsert por [$fk, locale]
            $modelClass::updateOrCreate(
                [$fk => $id, 'locale' => $short],
                $payload
            );
        }
    }

    /**
     * Devuelve el valor traducido para un campo y un locale corto,
     * aceptando alias tipo pt_br / pt-BR / en-us / en-gb.
     */
    private static function pickTranslationValue(array $translations, string $field, string $short)
    {
        if (!isset($translations[$field]) || !is_array($translations[$field])) {
            return null;
        }

        $pool = $translations[$field];

        // 1) Exacto (corto)
        if (array_key_exists($short, $pool)) {
            return $pool[$short];
        }

        // 2) Alias comunes por idioma
        $aliases = match ($short) {
            'pt' => ['pt_br', 'pt-BR', 'PT_BR', 'PT-BR', 'pt-pt', 'PT-PT'],
            'en' => ['en_us', 'en-Us', 'en-US', 'EN_US', 'EN-US', 'en_gb', 'en-GB', 'EN_GB', 'EN-GB'],
            'fr' => ['fr_fr', 'fr-FR', 'FR_FR', 'FR-FR'],
            'de' => ['de_de', 'de-DE', 'DE_DE', 'DE-DE'],
            'es' => ['es_es', 'es-ES', 'ES_ES', 'ES-ES'],
            default => [],
        };

        foreach ($aliases as $alias) {
            if (array_key_exists($alias, $pool)) {
                return $pool[$alias];
            }
        }

        // 3) Nada encontrado
        return null;
    }

    /**
     * Considera null / '' / '   ' como vacío.
     */
    private static function isEmptyStringish($v): bool
    {
        if ($v === null) return true;
        if (is_string($v) && trim($v) === '') return true;
        return false;
    }
}
