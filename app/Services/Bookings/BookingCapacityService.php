<?php

namespace App\Services\Bookings;

use App\Models\{Schedule, Tour, BookingDetail, TourExcludedDate, TourAvailability};
use Illuminate\Support\Facades\DB;

class BookingCapacityService
{
    private array $bookingCountStatuses;

    public function __construct()
    {
        $this->bookingCountStatuses = config('bookings.count_statuses', ['confirmed']);
    }

    /**
     * NUEVA LÓGICA JERÁRQUICA DE CAPACIDAD
     * Prioridad:
     * 1. TourAvailability (día + horario específico)
     * 2. TourAvailability (día general, todos los horarios)
     * 3. schedule_tour.base_capacity (pivote)
     * 4. Tour.max_capacity
     * 5. PHP_INT_MAX (sin límite)
     */
    public function resolveMaxCapacity(Tour $tour, ?Schedule $schedule, string $tourDate): int
    {
        // 1. Buscar override específico para día + horario
        if ($schedule) {
            $specificOverride = TourAvailability::active()
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $tourDate)
                ->first();

            if ($specificOverride) {
                if ($specificOverride->is_blocked) {
                    return 0;
                }
                if (!is_null($specificOverride->max_capacity)) {
                    return (int) $specificOverride->max_capacity;
                }
            }
        }

        // 2. Buscar override general del día (aplica a todos los horarios)
        $generalDayOverride = TourAvailability::active()
            ->where('tour_id', $tour->tour_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $tourDate)
            ->first();

        if ($generalDayOverride) {
            if ($generalDayOverride->is_blocked) {
                return 0;
            }
            if (!is_null($generalDayOverride->max_capacity)) {
                return (int) $generalDayOverride->max_capacity;
            }
        }

        // 3. Capacidad del pivote schedule_tour.base_capacity
        if ($schedule) {
            $pivot = DB::table('schedule_tour')
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->first();

            if ($pivot && !is_null($pivot->base_capacity)) {
                return (int) $pivot->base_capacity;
            }
        }

        // 4. Capacidad general del tour
        if (!is_null($tour->max_capacity)) {
            return (int) $tour->max_capacity;
        }

        // 5. Sin límite
        return PHP_INT_MAX;
    }

    /**
     * Verificar si fecha está bloqueada
     */
    public function isDateBlocked(Tour $tour, ?Schedule $schedule, string $tourDate): bool
    {
        // 1. Bloqueos por TourExcludedDate
        $excludedDateBlock = TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where(function ($q) use ($schedule) {
                $q->whereNull('schedule_id');
                if ($schedule) {
                    $q->orWhere('schedule_id', $schedule->schedule_id);
                }
            })
            ->where('start_date', '<=', $tourDate)
            ->where(function ($q) use ($tourDate) {
                $q->where('end_date', '>=', $tourDate)
                    ->orWhereNull('end_date');
            })
            ->exists();

        if ($excludedDateBlock) {
            return true;
        }

        // 2. Bloqueos por TourAvailability específico
        if ($schedule) {
            $specificBlock = TourAvailability::active()
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $tourDate)
                ->where('is_blocked', true)
                ->exists();

            if ($specificBlock) {
                return true;
            }
        }

        // 3. Bloqueo general del día
        $generalDayBlock = TourAvailability::active()
            ->where('tour_id', $tour->tour_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $tourDate)
            ->where('is_blocked', true)
            ->exists();

        return $generalDayBlock;
    }

    /**
     * Pax confirmados para tour+schedule+fecha
     */
    public function confirmedPaxFor(
        string $tourDate,
        int $scheduleId,
        int $tourId,
        ?int $excludeBookingId = null
    ): int {
        $sum = BookingDetail::whereHas('booking', function ($q) use ($excludeBookingId) {
            $q->whereIn('status', $this->bookingCountStatuses);
            if ($excludeBookingId) {
                $q->where('booking_id', '!=', $excludeBookingId);
            }
        })
            ->whereNotNull('booking_id')
            ->where('tour_id', $tourId)
            ->whereDate('tour_date', $tourDate)
            ->where('schedule_id', $scheduleId)
            ->sum(DB::raw('COALESCE(adults_quantity,0) + COALESCE(kids_quantity,0)'));

        return (int) $sum;
    }

    /**
     * Pax retenidos en carritos activos
     */
    public function heldPaxInActiveCarts(
        string $tourDate,
        int $scheduleId,
        int $tourId,
        ?int $excludeCartId = null
    ): int {
        $q = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.cart_id')
            ->where('carts.is_active', true)
            ->whereNotNull('carts.expires_at')
            ->where('carts.expires_at', '>', now())
            ->where('cart_items.is_active', true)
            ->where('cart_items.tour_id', $tourId)
            ->whereDate('cart_items.tour_date', $tourDate)
            ->where('cart_items.schedule_id', $scheduleId);

        if ($excludeCartId) {
            $q->where('carts.cart_id', '!=', $excludeCartId);
        }

        return (int) $q->sum(DB::raw('COALESCE(cart_items.adults_quantity,0) + COALESCE(cart_items.kids_quantity,0)'));
    }

    /**
     * Snapshot completo de capacidad
     */
    public function capacitySnapshot(
        Tour $tour,
        Schedule $schedule,
        string $tourDate,
        ?int $excludeBookingId = null,
        bool $countHolds = true,
        ?int $excludeCartId = null
    ): array {
        $blocked = $this->isDateBlocked($tour, $schedule, $tourDate);
        $max = $this->resolveMaxCapacity($tour, $schedule, $tourDate);
        $confirmed = $this->confirmedPaxFor($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id, $excludeBookingId);
        $held = $countHolds
            ? $this->heldPaxInActiveCarts($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id, $excludeCartId)
            : 0;

        $available = $blocked ? 0 : max(0, (int)$max - (int)$confirmed - (int)$held);

        return [
            'blocked' => (bool) $blocked,
            'max' => (int) $max,
            'confirmed' => (int) $confirmed,
            'held' => (int) $held,
            'available' => (int) $available,
        ];
    }

    /**
     * Helper simple para capacidad restante
     */
    public function remainingCapacity(
        Tour $tour,
        Schedule $schedule,
        string $tourDate,
        ?int $excludeBookingId = null,
        bool $countHolds = true,
        ?int $excludeCartId = null
    ): int {
        $snap = $this->capacitySnapshot($tour, $schedule, $tourDate, $excludeBookingId, $countHolds, $excludeCartId);
        return (int) $snap['available'];
    }
}
