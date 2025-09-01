<?php

namespace App\Support;

use Carbon\Carbon;

class BookingRules
{
    /**
     * Fecha más temprana reservable considerando lead_days y cutoff.
     * Regla: si ya pasó el cutoff de hoy, “mañana” queda bloqueada, por lo que
     * el mínimo pasa a “pasado mañana”.
     */
    public static function earliestBookableDate(?Carbon $now = null): Carbon
    {
        $tz  = config('app.timezone', 'UTC');
        $now = $now?->copy() ?? Carbon::now($tz);

        $leadDays = (int) config('booking.lead_days', 1); // por defecto 1 (mañana)
        $cutoff   = (string) config('booking.cutoff_hour', '18:00');

        // cutoff hoy a HH:MM
        [$h, $m] = array_pad(explode(':', $cutoff), 2, '0');
        $todayCutoff = $now->copy()->setTime((int) $h, (int) $m, 0);

        // candidato base = hoy + leadDays (p. ej. mañana)
        $candidate = $now->copy()->addDays($leadDays)->startOfDay();

        // si ya pasamos el cutoff, saltamos un día adicional
        if ($now->gte($todayCutoff)) {
            $candidate->addDay();
        }

        return $candidate;
    }

    /**
     * ¿Una fecha (Y-m-d) es reservable?
     */
    public static function isDateBookable(string $date): bool
    {
        $tz  = config('app.timezone', 'UTC');
        $dt  = Carbon::parse($date, $tz)->startOfDay();
        $min = self::earliestBookableDate();
        return $dt->gte($min);
    }
}
