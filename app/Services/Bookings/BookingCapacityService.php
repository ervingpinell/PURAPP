<?php

namespace App\Services\Bookings;

use App\Models\{
    Schedule,
    Product,
    BookingDetail,
    ProductExcludedDate,
    ProductAvailability
};
use Illuminate\Support\Facades\DB;

/**
 * ÚNICA FUENTE DE LECTURA / REGLAS DE CAPACIDAD
 * Jerarquía:
 *   1) ProductAvailability específico (día+horario)
 *   2) ProductAvailability general (día sin horario)
 *   3) Pivot schedule_product.base_capacity
 *   4) Product.max_capacity
 *   5) Unbounded (PHP_INT_MAX)
 *
 * Además, contempla bloqueos (ProductExcludedDate y ProductAvailability.is_blocked).
 */
class BookingCapacityService
{
    private array $bookingCountStatuses;

    public function __construct()
    {
        // Configurable en config/booking.php => 'count_statuses'
        $this->bookingCountStatuses = config('booking.count_statuses', ['confirmed', 'pending']);
    }

    /**
     * Devuelve la capacidad MÁXIMA efectiva para la fecha dada.
     */
    public function resolveMaxCapacity(Product $product, ?Schedule $schedule, string $productDate): int
    {
        // 1) Override específico (día + horario)
        if ($schedule) {
            $specific = ProductAvailability::active()
                ->where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $productDate)
                ->first();

            if ($specific) {
                if ($specific->is_blocked) {
                    return 0;
                }
                if (!is_null($specific->max_capacity)) {
                    return (int) $specific->max_capacity;
                }
                // Si no trae max_capacity, pasamos a la siguiente capa
            }
        }

        // 2) Override general del día (sin horario)
        $general = ProductAvailability::active()
            ->where('product_id', $product->product_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $productDate)
            ->first();

        if ($general) {
            if ($general->is_blocked) {
                return 0;
            }
            if (!is_null($general->max_capacity)) {
                return (int) $general->max_capacity;
            }
        }

        // 3) Capacidad del pivote schedule_tour
        if ($schedule) {
            $pivot = DB::table('schedule_product')
                ->where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->first();

            if ($pivot && !is_null($pivot->base_capacity)) {
                return (int) $pivot->base_capacity;
            }
        }

        // 4) Capacidad global del tour
        if (!is_null($product->max_capacity)) {
            return (int) $product->max_capacity;
        }

        // 5) Sin límite explícito
        return PHP_INT_MAX;
    }

    /**
     * Indica si la fecha está bloqueada para ese producto/horario.
     */
    public function isDateBlocked(Product $product, ?Schedule $schedule, string $productDate): bool
    {
        // Bloqueo por rango (bitácora/administrativo)
        $byRange = ProductExcludedDate::where('product_id', $product->product_id)
            ->where(function ($q) use ($schedule) {
                $q->whereNull('schedule_id');
                if ($schedule) {
                    $q->orWhere('schedule_id', $schedule->schedule_id);
                }
            })
            ->where('start_date', '<=', $productDate)
            ->where(function ($q) use ($productDate) {
                $q->where('end_date', '>=', $productDate)
                    ->orWhereNull('end_date');
            })
            ->exists();

        if ($byRange) {
            return true;
        }

        // Bloqueo específico por día+horario
        if ($schedule) {
            $specific = ProductAvailability::active()
                ->where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $productDate)
                ->where('is_blocked', true)
                ->exists();

            if ($specific) {
                return true;
            }
        }

        // Bloqueo general por día (sin horario)
        $general = ProductAvailability::active()
            ->where('product_id', $product->product_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $productDate)
            ->where('is_blocked', true)
            ->exists();

        return $general;
    }

    /**
     * Pax confirmados + pending PAGADOS (suma de todas las categorías en JSON).
     * Solo cuenta bookings que están confirmados o pending pero PAID.
     */
    public function confirmedPaxFor(
        string $productDate,
        int $scheduleId,
        int $productId,
        ?int $excludeBookingId = null
    ): int {
        $details = BookingDetail::whereHas('booking', function ($q) use ($excludeBookingId) {
            // Confirmed OR (pending + paid)
            $q->where(function ($query) {
                $query->where('status', 'confirmed')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where('is_paid', true);
                    });
            });

            if ($excludeBookingId) {
                $q->where('booking_id', '!=', $excludeBookingId);
            }
        })
            ->whereNotNull('booking_id')
            ->where('product_id', $productId)
            ->whereDate('tour_date', $productDate)
            ->where('schedule_id', $scheduleId)
            ->get(['categories']);

        $total = 0;

        foreach ($details as $detail) {
            $cats = $detail->categories;

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
     * Pax en pending SIN PAGAR (lower priority bookings).
     */
    public function unpaidPendingPaxFor(
        string $productDate,
        int $scheduleId,
        int $productId,
        ?int $excludeBookingId = null
    ): int {
        $details = BookingDetail::whereHas('booking', function ($q) use ($excludeBookingId) {
            $q->where('status', 'pending')
                ->where('is_paid', false);

            if ($excludeBookingId) {
                $q->where('booking_id', '!=', $excludeBookingId);
            }
        })
            ->whereNotNull('booking_id')
            ->where('product_id', $productId)
            ->whereDate('tour_date', $productDate)
            ->where('schedule_id', $scheduleId)
            ->get(['categories']);

        $total = 0;

        foreach ($details as $detail) {
            $cats = $detail->categories;

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
     * Pax retenidos en carritos activos (suma desde JSON categories).
     */
    public function heldPaxInActiveCarts(
        string $productDate,
        int $scheduleId,
        int $productId,
        ?int $excludeCartId = null
    ): int {
        $rows = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.cart_id')
            ->where('carts.is_active', true)
            ->whereNotNull('carts.expires_at')
            ->where('carts.expires_at', '>', now())
            ->where('cart_items.is_active', true)
            ->where('cart_items.product_id', $productId)
            ->whereDate('cart_items.tour_date', $productDate)
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
     * Snapshot completo con desglose detallado de capacidad.
     * Incluye breakdown de paid/unpaid para mejor control.
     */
    public function capacitySnapshot(
        Product $product,
        Schedule $schedule,
        string $productDate,
        ?int $excludeBookingId = null,
        bool $countHolds = true,
        ?int $excludeCartId = null,
        bool $countActiveReservations = true  // 🆕 Count active cart reservations
    ): array {
        $blocked   = $this->isDateBlocked($product, $schedule, $productDate);
        $max       = $this->resolveMaxCapacity($product, $schedule, $productDate);

        // Paid bookings (confirmed + pending paid) - PRIORITY
        $confirmedAndPaid = $this->confirmedPaxFor($productDate, (int)$schedule->schedule_id, (int)$product->product_id, $excludeBookingId);

        // Unpaid pending bookings - LOWER PRIORITY
        $unpaidPending = $this->unpaidPendingPaxFor($productDate, (int)$schedule->schedule_id, (int)$product->product_id, $excludeBookingId);

        // Cart holds - LOWEST PRIORITY
        $held = $countHolds
            ? $this->heldPaxInActiveCarts($productDate, (int)$schedule->schedule_id, (int)$product->product_id, $excludeCartId)
            : 0;

        // 🆕 ACTIVE CART RESERVATIONS (items marked as reserved)
        $reserved = 0;
        if ($countActiveReservations) {
            $expirationMinutes = (int) setting('cart.expiration_minutes', 30);
            $cutoffTime = now()->subMinutes($expirationMinutes);

            $reserved = \App\Models\CartItem::where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->where('tour_date', $productDate)
                ->where('is_reserved', true)
                ->where('reserved_at', '>', $cutoffTime)
                ->when($excludeCartId, fn($q) => $q->whereNot('cart_id', $excludeCartId))
                ->get()
                ->sum('total_pax');
        }

        // Total used (for legacy 'confirmed' field)
        $confirmed = $confirmedAndPaid + $unpaidPending;

        // Available = max - (paid + unpaid + holds + reserved)
        $available = $blocked ? 0 : max(0, (int)$max - (int)$confirmed - (int)$held - (int)$reserved);

        return [
            'blocked'   => (bool) $blocked,
            'max'       => (int) $max,
            'confirmed' => (int) $confirmed, // Total confirmed + all pending (legacy)
            'held'      => (int) $held,
            'reserved'  => (int) $reserved,  // 🆕 Active cart reservations
            'available' => (int) $available,

            // Detailed breakdown (NEW)
            'confirmed_and_paid' => (int) $confirmedAndPaid,  // Priority bookings
            'unpaid_pending'     => (int) $unpaidPending,     // Lower priority
            'cart_holds'         => (int) $held,              // Lowest priority
            'cart_reserved'      => (int) $reserved,          // 🆕 Reserved items
        ];
    }

    /**
     * Etiqueta de nivel que explica de dónde salió la capacidad aplicada.
     */
    public function capacityLevel(Product $product, ?Schedule $schedule, string $productDate): string
    {
        if ($schedule) {
            $spec = ProductAvailability::active()
                ->where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->whereDate('date', $productDate)
                ->first();
            if ($spec) {
                return $spec->is_blocked
                    ? 'blocked-specific'
                    : (!is_null($spec->max_capacity) ? 'override-specific' : 'pass-through-specific');
            }
        }

        $gen = ProductAvailability::active()
            ->where('product_id', $product->product_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $productDate)
            ->first();
        if ($gen) {
            return $gen->is_blocked
                ? 'blocked-general'
                : (!is_null($gen->max_capacity) ? 'override-general' : 'pass-through-general');
        }

        if ($schedule) {
            $pivot = DB::table('schedule_product')
                ->where('product_id', $product->product_id)
                ->where('schedule_id', $schedule->schedule_id)
                ->first();
            if ($pivot && !is_null($pivot->base_capacity)) {
                return 'pivot';
            }
        }

        if (!is_null($product->max_capacity)) {
            return 'product';
        }

        return 'unbounded';
    }

    /**
     * Helper simple para capacidad restante.
     */
    public function remainingCapacity(
        Product $product,
        Schedule $schedule,
        string $productDate,
        ?int $excludeBookingId = null,
        bool $countHolds = true,
        ?int $excludeCartId = null
    ): int {
        $snap = $this->capacitySnapshot($product, $schedule, $productDate, $excludeBookingId, $countHolds, $excludeCartId);
        return (int) $snap['available'];
    }
}
