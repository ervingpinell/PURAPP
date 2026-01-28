<?php

namespace App\Mail\Concerns;

use App\Models\Booking;

trait BookingMailHelpers
{
    use RestrictsEmailLocale;

    /**
     * Intenta extraer un código de idioma ('es' | 'en') desde el modelo de ProductLanguage,
     * tolerando distintos nombres de campo.
     */
    protected function resolveLangCode(?object $lang): ?string
    {
        if (!$lang) return null;

        // Campos típicos posibles
        foreach (['code', 'locale', 'abbr', 'slug', 'short_code', 'iso', 'language_code'] as $f) {
            if (!empty($lang->{$f})) {
                return strtolower((string) $lang->{$f});
            }
        }

        // Heurística por nombre
        if (!empty($lang->name)) {
            $n = strtolower((string) $lang->name);
            if (str_contains($n, 'es') || str_contains($n, 'esp') || str_contains($n, 'ñol')) return 'es';
            if (str_contains($n, 'en') || str_contains($n, 'eng') || str_contains($n, 'english')) return 'en';
        }

        return null;
    }

    /**
     * Locale del correo según el usuario o fallback.
     * SIEMPRE devuelve 'es' o 'en'.
     */
    protected function mailLocaleFromBooking(Booking $booking): string
    {
        $booking->loadMissing(['user']);

        // Try to get locale from user, otherwise fallback to 'en'
        $code = $booking->user->locale ?? 'en';

        // Forzar a solo español o inglés
        return $this->restrictedLocale($code);
    }

    /**
     * Referencia de reserva (robusto a distintos nombres).
     */
    protected function bookingReference(Booking $booking): string
    {
        return (string) ($booking->booking_reference ?? $booking->reference ?? $booking->booking_code ?? $booking->booking_id);
    }

    /**
     * Etiqueta legible del idioma del tour, alineada con el locale del correo.
     */
    protected function humanTourLanguage(string $mailLocale, Booking $booking): string
    {
        return $mailLocale === 'es' ? 'Español' : 'English';
    }

    /**
     * Estado traducido usando el locale del correo.
     */
    protected function statusLabel(string $mailLocale, Booking $booking): string
    {
        $status = strtolower((string)($booking->status ?? 'confirmed'));

        $key = match ($status) {
            'pending'                 => 'state_pending',
            'confirmed'               => 'state_confirmed',
            'cancelled', 'canceled'   => 'state_cancelled',
            default                   => 'state_confirmed',
        };

        return __('adminlte::email.' . $key, [], $mailLocale);
    }
}
