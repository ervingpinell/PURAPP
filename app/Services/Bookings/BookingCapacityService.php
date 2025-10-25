<?php

namespace App\Services\Bookings;

use App\Models\Schedule;
use App\Models\Tour;
use App\Models\BookingDetail;
use App\Models\TourExcludedDate;
use App\Models\TourAvailability;
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
        if ($schedule && !is_null($schedule->max_capacity)) return (int)$schedule->max_capacity;
        if ($tour && !is_null($tour->max_capacity))         return (int)$tour->max_capacity;
        return PHP_INT_MAX;
    }

    /** ¿Fecha bloqueada por exclusiones (y opcionalmente por disponibilidad)? */
    public function isDateBlocked(Tour $tour, ?Schedule $schedule, string $tourDate): bool
    {
        // Bloqueo por rango (por tour o por el horario específico)
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

        // Si usas availability como switch de día/slot
        $availabilityBlocked = TourAvailability::where('tour_id', $tour->tour_id)
            ->whereDate('date', $tourDate)
            ->where('available', 0)
            ->where('is_active', 1)
            ->exists();

        return $availabilityBlocked;
    }

    /**
     * Pax confirmados para un tour+schedule+fecha
     * (IMPORTANTE: filtrar por tour_id para no mezclar horarios compartidos).
     */
    public function confirmedPaxFor(string $tourDate, int $scheduleId, ?int $excludeBookingId = null, ?int $tourId = null): int
    {
        return (int) BookingDetail::whereHas('booking', function ($q) use ($excludeBookingId) {
                $q->whereIn('status', $this->bookingCountStatuses);
                if ($excludeBookingId) $q->where('booking_id', '!=', $excludeBookingId);
            })
            ->when($tourId, fn($q) => $q->where('tour_id', $tourId))
            ->whereDate('tour_date', $tourDate)
            ->where('schedule_id', $scheduleId)
            ->sum(DB::raw('COALESCE(adults_quantity,0) + COALESCE(kids_quantity,0)'));
    }

    /**
     * Pax retenidos en carritos activos (para evitar sobreventa).
     * Si no quieres considerar “holds”, puedes no usar esta resta.
     */
    public function heldPaxInActiveCarts(string $tourDate, int $scheduleId, int $tourId): int
    {
        return (int) DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.cart_id')
            ->where('carts.is_active', true)
            ->whereNotNull('carts.expires_at')
            ->where('carts.expires_at', '>', now())
            ->where('cart_items.is_active', true)
            ->where('cart_items.tour_id', $tourId)
            ->whereDate('cart_items.tour_date', $tourDate)
            ->where('cart_items.schedule_id', $scheduleId)
            ->sum(DB::raw('COALESCE(cart_items.adults_quantity,0) + COALESCE(cart_items.kids_quantity,0)'));
    }

    /**
     * Capacidad restante real para Tour+Schedule+Fecha
     * - chequea bloqueos
     * - resta confirmados
     * - (opcional) resta “holds” de carritos activos
     */
    public function remainingCapacity(Tour $tour, Schedule $schedule, string $tourDate, ?int $excludeBookingId = null, bool $countHolds = true): int
    {
        if ($this->isDateBlocked($tour, $schedule, $tourDate)) {
            return 0;
        }

        $max       = $this->resolveMaxCapacity($schedule, $tour);
        $confirmed = $this->confirmedPaxFor($tourDate, (int)$schedule->schedule_id, $excludeBookingId, (int)$tour->tour_id);
        $held      = $countHolds ? $this->heldPaxInActiveCarts($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id) : 0;

        return max(0, (int)$max - (int)$confirmed - (int)$held);
    }
}
