<?php

namespace App\Services\Bookings;

use App\Models\{Booking, BookingDetail, Tour, Schedule, PromoCode};
use Illuminate\Support\Facades\DB;

class BookingCreator
{
    public function __construct(
        private BookingCapacityService $cap,
        private BookingPricingService $pricing,
    ) {}

    /**
     * Crea una reserva con categorías modulares
     *
     * $payload:
     *  - user_id, tour_id, schedule_id, tour_language_id, tour_date
     *  - categories: ['category_id' => quantity, ...]
     *  - status, promo_code, meeting_point_id, hotel_id, is_other_hotel, other_hotel_name, notes
     *  - exclude_cart_id (opcional)
     */
    public function create(array $payload, bool $validateCapacity = true, bool $countHolds = true): Booking
    {
        return DB::transaction(function () use ($payload, $validateCapacity, $countHolds) {
            $tour     = Tour::with('prices.category')->findOrFail($payload['tour_id']);
            $schedule = Schedule::findOrFail($payload['schedule_id']);

            // Cantidades por categoría
            $quantities = $payload['categories'] ?? [];
            if (empty($quantities)) {
                throw new \InvalidArgumentException('No categories provided');
            }

            // Snapshot (price + quantity + slug + name) - usar fecha del tour para precios temporales
            $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($tour, $quantities, $payload['tour_date'] ?? null);
            if (empty($categoriesSnapshot)) {
                throw new \RuntimeException('No valid active categories found for this tour');
            }

            // Subtotales y pax
            $totals = $this->pricing->calculateTotals($categoriesSnapshot);
            $detailSubtotal = $totals['subtotal'];
            $taxesTotal = $totals['tax_amount'];
            $taxesBreakdown = $totals['taxes_breakdown'];

            $totalPax = $this->pricing->getTotalPaxFromCategories($categoriesSnapshot);

            // Promo (opcional)
            $promo = null;
            if (!empty($payload['promo_code'])) {
                $clean = PromoCode::normalize($payload['promo_code']);
                $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])
                    ->lockForUpdate()
                    ->first();

                if ($promo && method_exists($promo, 'isValidToday') && !$promo->isValidToday()) $promo = null;
                if ($promo && method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses()) $promo = null;
            }

            // Capacidad
            if ($validateCapacity) {
                $date          = $payload['tour_date'];
                $excludeCartId = $payload['exclude_cart_id'] ?? null;

                $snap = $this->cap->capacitySnapshot(
                    $tour,
                    $schedule,
                    $date,
                    excludeBookingId: null,
                    countHolds: $countHolds,
                    excludeCartId: $excludeCartId
                );

                if ($snap['blocked'] || $totalPax > $snap['available']) {
                    throw new \RuntimeException(json_encode([
                        'type'       => 'capacity',
                        'blocked'    => (bool) $snap['blocked'],
                        'available'  => (int) $snap['available'],
                        'max'        => (int) $snap['max'],
                        'confirmed'  => (int) $snap['confirmed'],
                        'held'       => (int) $snap['held'],
                        'requested'  => (int) $totalPax,
                        'tour_id'    => (int) $tour->tour_id,
                        'schedule_id' => (int) $schedule->schedule_id,
                        'date'       => $date,
                    ]));
                }
            }

            // Total final (con promo aplicada al subtotal)
            $discountedSubtotal = $this->pricing->applyPromo($detailSubtotal, $promo);
            $totalBooking = $discountedSubtotal + $taxesTotal;

            // BOOKING
            $booking = Booking::create([
                'booking_reference' => 'BK' . strtoupper(uniqid()),
                'user_id'           => (int) $payload['user_id'],
                'tour_id'           => (int) $payload['tour_id'],
                'tour_language_id'  => (int) $payload['tour_language_id'],
                'booking_date'      => $payload['booking_date'] ?? now(),
                'status'            => $payload['status'] ?? 'pending',
                'total'             => $totalBooking,
                'notes'             => $payload['notes'] ?? null,
            ]);

            // BOOKING DETAIL (solo JSON categories + pickups)
            BookingDetail::create([
                'booking_id'        => $booking->booking_id,
                'tour_id'           => (int) $payload['tour_id'],
                'schedule_id'       => (int) $payload['schedule_id'],
                'tour_date'         => $payload['tour_date'],
                'tour_language_id'  => (int) $payload['tour_language_id'],
                'categories'        => $categoriesSnapshot,
                'total'             => $detailSubtotal, // Base price (Subtotal)
                'taxes_breakdown'   => $taxesBreakdown,
                'taxes_total'       => $taxesTotal,
                // Pickup
                'hotel_id'          => !empty($payload['is_other_hotel']) ? null : ($payload['hotel_id'] ?? null),
                'is_other_hotel'    => (bool) ($payload['is_other_hotel'] ?? false),
                'other_hotel_name'  => $payload['other_hotel_name'] ?? null,
                'meeting_point_id'  => $payload['meeting_point_id'] ?? null,
                'pickup_time'         => $payload['pickup_time'] ?? null,
            ]);

            // Redimir promo
            if ($promo) {
                $appliedAmount = 0.0;
                if ($promo->discount_percent) {
                    $appliedAmount = round($detailSubtotal * ($promo->discount_percent / 100), 2);
                } elseif ($promo->discount_amount) {
                    $appliedAmount = (float) $promo->discount_amount;
                }

                $exists = $booking->redemption()->where('promo_code_id', $promo->promo_code_id)->exists();
                if (!$exists) {
                    $booking->redemption()->create([
                        'promo_code_id'     => (int) $promo->promo_code_id,
                        'booking_id'        => (int) $booking->booking_id,
                        'user_id'           => (int) ($payload['user_id'] ?? null),
                        'applied_amount'    => $appliedAmount,
                        'operation_snapshot' => $promo->operation ?? 'subtract',
                        'percent_snapshot'  => $promo->discount_percent,
                        'amount_snapshot'   => $promo->discount_amount,
                        'used_at'           => now(),
                    ]);

                    $promo->usage_count = (int) $promo->usage_count + 1;
                    if (!is_null($promo->usage_limit) && $promo->usage_count >= $promo->usage_limit) {
                        $promo->is_used = true;
                        $promo->used_at = now();
                    }
                    $promo->save();
                }
            }

            return $booking;
        });
    }
}
