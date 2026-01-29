<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Schedule;
use Carbon\Carbon;

class BookingRules
{
    /**
     * Obtiene valores "globales" (settings DB o config) con fallback a config/booking.php
     */
    public static function global(): array
    {
        $cfgCutoff = config('booking.cutoff_hour', '18:00');
        $cfgLead   = (int) config('booking.lead_days', 1);

        // Usa el nuevo sistema de settings
        $cutoff = setting('booking.cutoff_hour', $cfgCutoff);
        $lead   = (int) setting('booking.lead_days', $cfgLead);

        return ['cutoff_hour' => $cutoff, 'lead_days' => $lead];
    }

    /**
     * Resuelve cutoff/lead con precedencia: pivot -> product -> global
     */
    public static function effectiveFor(?Product $product = null, ?Schedule $schedule = null): array
    {
        $global = self::global();

        // 1) Pivot
        if ($product && $schedule) {
            // Si ya viene cargada la relación, úsala. Si no, consulta.
            $pivot = $product->relationLoaded('schedules')
                ? $product->schedules->firstWhere('schedule_id', $schedule->getKey())?->pivot
                : $product->schedules()->where('schedules.schedule_id', $schedule->getKey())->first()?->pivot;

            $pCutoff = $pivot?->cutoff_hour;
            $pLead   = $pivot?->lead_days;

            if ($pCutoff || $pLead !== null) {
                return [
                    'cutoff_hour' => $pCutoff ?: ($product?->cutoff_hour ?: $global['cutoff_hour']),
                    'lead_days'   => $pLead   !== null ? (int)$pLead : ($product?->lead_days ?? $global['lead_days']),
                ];
            }
        }

        // 2) Tour
        if ($product && ($product->cutoff_hour || $product->lead_days !== null)) {
            return [
                'cutoff_hour' => $product->cutoff_hour ?: $global['cutoff_hour'],
                'lead_days'   => $product->lead_days   ?? $global['lead_days'],
            ];
        }

        // 3) Global
        return $global;
    }

    /**
     * Calcula la fecha mínima reservable según reglas (tz = config('app.timezone')).
     * lead_days se aplica siempre; si hoy ya pasó el cutoff, agrega 1 día adicional.
     */
    public static function earliestBookableDate(?Product $product = null, ?Schedule $schedule = null): Carbon
    {
        $tz = config('app.timezone', 'UTC');
        $now = Carbon::now($tz);

        $eff    = self::effectiveFor($product, $schedule);
        $lead   = (int) ($eff['lead_days'] ?? 1);
        $cutoff = (string) ($eff['cutoff_hour'] ?? '18:00'); // "HH:MM"

        $todayStart = $now->copy()->startOfDay();
        $earliest   = $todayStart->copy()->addDays($lead);

        // Si ahora ya pasó el cutoff, el primer día posible se corre un día más
        if ($now->format('H:i') >= $cutoff) {
            $earliest->addDay();
        }

        return $earliest;
    }
}
