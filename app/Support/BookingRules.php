<?php

namespace App\Support;

use App\Models\AppSetting;
use Carbon\Carbon;

class BookingRules
{
    public static function nowTz(): Carbon
    {
        return Carbon::now(config('app.timezone'));
    }

    public static function cutoffHour(): string
    {
        $h = AppSetting::get('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
        // Sanitiza
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $h) ? $h : '18:00';
    }

    public static function leadDays(): int
    {
        return (int) AppSetting::get('booking.lead_days', (int) config('booking.lead_days', 1));
    }

    public static function earliestBookableDate(): Carbon
    {
        $tz = config('app.timezone');
        [$hh, $mm] = array_pad(explode(':', static::cutoffHour()), 2, 0);

        $now = Carbon::now($tz);
        $cutoffToday = Carbon::createFromTime((int)$hh, (int)$mm, 0, $tz);

        $base = $now->lt($cutoffToday) ? static::leadDays() : static::leadDays() + 1;

        return $now->copy()->startOfDay()->addDays($base);
    }
}
