<?php

namespace App\Mail\Concerns;

trait RestrictsEmailLocale
{
    /**
     * Restringe el locale del correo a solo español o inglés.
     * Si el locale actual no es 'es' o 'en', usa 'en' por defecto.
     */
    protected function restrictedLocale(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        // Normalizar: si empieza con 'es', usar 'es'; sino, usar 'en'
        if (str_starts_with(strtolower($locale), 'es')) {
            return 'es';
        }

        return 'en';
    }
}
