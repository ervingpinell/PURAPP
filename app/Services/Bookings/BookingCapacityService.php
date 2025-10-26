<?php

namespace App\Services\Bookings;

use App\Models\{Schedule, Tour, BookingDetail, TourExcludedDate, TourAvailability};
use Illuminate\Support\Facades\DB;

class BookingCapacityService
{
    /** Estados de booking que cuentan para ocupar capacidad */
    private array $bookingCountStatuses;

    public function __construct()
    {
        // Ajusta si usas más estados (paid/completed, etc.)
        $this->bookingCountStatuses = config('bookings.count_statuses', ['confirmed']);
    }

    /** Capacidad base (prioriza Schedule, luego Tour, o “infinito”) */
    public function resolveMaxCapacity(?Schedule $schedule, ?Tour $tour): int
    {
        if ($schedule && !is_null($schedule->max_capacity)) return (int) $schedule->max_capacity;
        if ($tour && !is_null($tour->max_capacity))         return (int) $tour->max_capacity;
        return PHP_INT_MAX; // Sin límite explícito
    }

    /** ¿Fecha bloqueada por exclusiones (y opcionalmente por disponibilidad)? */
    public function isDateBlocked(Tour $tour, ?Schedule $schedule, string $tourDate): bool
    {
        // Bloqueos por rango (por tour o por horario)
        $blocked = TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where(function ($q) use ($schedule) {
                $q->whereNull('schedule_id');
                if ($schedule) $q->orWhere('schedule_id', $schedule->schedule_id);
            })
            ->where('start_date', '<=', $tourDate)
            ->where(function ($q) use ($tourDate) {
                $q->where('end_date', '>=', $tourDate)->orWhereNull('end_date');
            })
            ->exists();

        if ($blocked) return true;

        // Disponibilidad a nivel de tour para el día
        $availabilityBlocked = TourAvailability::where('tour_id', $tour->tour_id)
            ->whereDate('date', $tourDate)
            ->where('available', 0)
            ->where('is_active', 1)
            ->exists();

        // Si manejas disponibilidad por horario, agrega columna schedule_id y descomenta:
        // ->when($schedule, fn($q) => $q->where(function($qq) use ($schedule) {
        //     $qq->whereNull('schedule_id')->orWhere('schedule_id', $schedule->schedule_id);
        // }))

        return $availabilityBlocked;
    }

    /**
     * Pax confirmados para un tour+schedule+fecha
     * IMPORTANT: filtrar por tour_id para no mezclar horarios compartidos.
     */
    public function confirmedPaxFor(string $tourDate, int $scheduleId, ?int $excludeBookingId = null, ?int $tourId = null): int
    {
        $sum = BookingDetail::whereHas('booking', function ($q) use ($excludeBookingId) {
                $q->whereIn('status', $this->bookingCountStatuses);
                if ($excludeBookingId) $q->where('booking_id', '!=', $excludeBookingId);
            })
            ->whereNotNull('booking_id') // defensivo
            ->when($tourId, fn($q) => $q->where('tour_id', $tourId))
            ->whereDate('tour_date', $tourDate)
            ->where('schedule_id', $scheduleId)
            ->sum(DB::raw('COALESCE(adults_quantity,0) + COALESCE(kids_quantity,0)'));

        return (int) $sum;
    }

    /**
     * Pax retenidos en carritos activos (para evitar sobreventa).
     * Si no quieres considerar “holds”, puedes no usar esta resta.
     */
    public function heldPaxInActiveCarts(string $tourDate, int $scheduleId, int $tourId): int
    {
        $sum = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.cart_id')
            ->where('carts.is_active', true)
            ->whereNotNull('carts.expires_at')
            ->where('carts.expires_at', '>', now())
            ->where('cart_items.is_active', true)
            ->where('cart_items.tour_id', $tourId)
            ->whereDate('cart_items.tour_date', $tourDate)
            ->where('cart_items.schedule_id', $scheduleId)
            ->sum(DB::raw('COALESCE(cart_items.adults_quantity,0) + COALESCE(cart_items.kids_quantity,0)'));

        return (int) $sum;
    }

    /**
     * Snapshot completo de capacidad para Tour+Schedule+Fecha.
     * Devuelve: ['blocked', 'max', 'confirmed', 'held', 'available'].
     */
    public function capacitySnapshot(Tour $tour, Schedule $schedule, string $tourDate, ?int $excludeBookingId = null, bool $countHolds = true): array
    {
        $blocked   = $this->isDateBlocked($tour, $schedule, $tourDate);
        $max       = $this->resolveMaxCapacity($schedule, $tour);
        $confirmed = $this->confirmedPaxFor($tourDate, (int)$schedule->schedule_id, $excludeBookingId, (int)$tour->tour_id);
        $held      = $countHolds ? $this->heldPaxInActiveCarts($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id) : 0;

        // Si está bloqueado, disponibles = 0 (pero devolvemos max/confirmed por transparencia)
        $available = $blocked ? 0 : max(0, (int)$max - (int)$confirmed - (int)$held);

        return [
            'blocked'   => (bool) $blocked,
            'max'       => (int) $max,
            'confirmed' => (int) $confirmed,
            'held'      => (int) $held,
            'available' => (int) $available,
        ];
    }

    /**
     * Capacidad restante real para Tour+Schedule+Fecha (helper simple).
     */
    public function remainingCapacity(Tour $tour, Schedule $schedule, string $tourDate, ?int $excludeBookingId = null, bool $countHolds = true): int
    {
        $snap = $this->capacitySnapshot($tour, $schedule, $tourDate, $excludeBookingId, $countHolds);
        return (int) $snap['available'];
    }
}
