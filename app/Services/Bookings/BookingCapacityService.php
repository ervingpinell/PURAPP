<?php

namespace App\Services\Bookings;

use App\Models\{Schedule, Tour, BookingDetail, TourExcludedDate, TourAvailability};
use Illuminate\Support\Facades\DB;

class BookingCapacityService
{
    private array $bookingCountStatuses;

    public function __construct()
    {
        $this->bookingCountStatuses = config('booking.count_statuses', ['confirmed', 'pending']);
    }

    /**
     * LÓGICA JERÁRQUICA DE CAPACIDAD
     */
    public function resolveMaxCapacity(Tour $tour, ?Schedule $schedule, string $tourDate): int
    {
        // 1) Override específico (día + horario)
        if ($schedule) {
            $specificOverride = TourAvailability::active()
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $tourDate)
                ->first();

            if ($specificOverride) {
                if ($specificOverride->is_blocked) return 0;
                if (!is_null($specificOverride->max_capacity)) {
                    return (int) $specificOverride->max_capacity;
                }
            }
        }

        // 2) Override general del día (sin horario)
        $generalDayOverride = TourAvailability::active()
            ->where('tour_id', $tour->tour_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $tourDate)
            ->first();

        if ($generalDayOverride) {
            if ($generalDayOverride->is_blocked) return 0;
            if (!is_null($generalDayOverride->max_capacity)) {
                return (int) $generalDayOverride->max_capacity;
            }
        }

        // 3) Capacidad del pivote schedule_tour
        if ($schedule) {
            $pivot = DB::table('schedule_tour')
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->first();

            if ($pivot && !is_null($pivot->base_capacity)) {
                return (int) $pivot->base_capacity;
            }
        }

        // 4) Capacidad del tour
        if (!is_null($tour->max_capacity)) {
            return (int) $tour->max_capacity;
        }

        return PHP_INT_MAX;
    }

    /**
     * Verificar si fecha está bloqueada
     */
    public function isDateBlocked(Tour $tour, ?Schedule $schedule, string $tourDate): bool
    {
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

        if ($excludedDateBlock) return true;

        if ($schedule) {
            $specificBlock = TourAvailability::active()
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $tourDate)
                ->where('is_blocked', true)
                ->exists();

            if ($specificBlock) return true;
        }

        $generalDayBlock = TourAvailability::active()
            ->where('tour_id', $tour->tour_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $tourDate)
            ->where('is_blocked', true)
            ->exists();

        return $generalDayBlock;
    }

    /**
     * Pax confirmados (suma de todas las categorías en JSON)
     */
    public function confirmedPaxFor(
        string $tourDate,
        int $scheduleId,
        int $tourId,
        ?int $excludeBookingId = null
    ): int {
        $details = BookingDetail::whereHas('booking', function ($q) use ($excludeBookingId) {
                $q->whereIn('status', $this->bookingCountStatuses);
                if ($excludeBookingId) {
                    $q->where('booking_id', '!=', $excludeBookingId);
                }
            })
            ->whereNotNull('booking_id')
            ->where('tour_id', $tourId)
            ->whereDate('tour_date', $tourDate)
            ->where('schedule_id', $scheduleId)
            ->get(['categories']); // solo necesitamos el JSON

        $total = 0;

        foreach ($details as $detail) {
            $cats = $detail->categories;

            // Por seguridad, si llegara como string, decodificamos
            if (is_string($cats)) {
                $cats = json_decode($cats, true);
            }

            if (is_array($cats)) {
                foreach ($cats as $cat) {
                    $total += (int)($cat['quantity'] ?? 0);
                }
            }
        }

        return $total;
    }

    /**
     * Pax retenidos en carritos activos (suma desde JSON categories)
     */
    public function heldPaxInActiveCarts(
        string $tourDate,
        int $scheduleId,
        int $tourId,
        ?int $excludeCartId = null
    ): int {
        $rows = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.cart_id')
            ->where('carts.is_active', true)
            ->whereNotNull('carts.expires_at')
            ->where('carts.expires_at', '>', now())
            ->where('cart_items.is_active', true)
            ->where('cart_items.tour_id', $tourId)
            ->whereDate('cart_items.tour_date', $tourDate)
            ->where('cart_items.schedule_id', $scheduleId)
            ->when($excludeCartId, fn($q) => $q->where('carts.cart_id', '!=', $excludeCartId))
            ->select('cart_items.categories')
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $cats = $row->categories;

            if (is_string($cats)) {
                $cats = json_decode($cats, true);
            }

            if (is_array($cats)) {
                foreach ($cats as $cat) {
                    $total += (int)($cat['quantity'] ?? 0);
                }
            }
        }

        return $total;
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
        $blocked   = $this->isDateBlocked($tour, $schedule, $tourDate);
        $max       = $this->resolveMaxCapacity($tour, $schedule, $tourDate);
        $confirmed = $this->confirmedPaxFor($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id, $excludeBookingId);
        $held      = $countHolds
            ? $this->heldPaxInActiveCarts($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id, $excludeCartId)
            : 0;

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
