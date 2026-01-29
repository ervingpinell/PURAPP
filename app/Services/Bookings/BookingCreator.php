<?php

namespace App\Services\Bookings;

use App\Models\{Booking, BookingDetail, Product, Schedule, PromoCode};
use Illuminate\Support\Facades\DB;

/**
 * BookingCreator
 *
 * Creates and manages booking records.
 */
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
     *  - user_id, product_id, schedule_id, tour_language_id, tour_date
     *  - categories: ['category_id' => quantity, ...]
     *  - status, promo_code, meeting_point_id, hotel_id, is_other_hotel, other_hotel_name, notes
     *  - exclude_cart_id (opcional)
     */
    public function create(array $payload, bool $validateCapacity = true, bool $countHolds = true): Booking
    {
        return DB::transaction(function () use ($payload, $validateCapacity, $countHolds) {
            // 🔒 PESSIMISTIC LOCK: Prevent concurrent bookings for same product/schedule/date
            // Lock product and schedule records to prevent race conditions on capacity
            $product     = Product::with('prices.category')
                ->lockForUpdate()
                ->findOrFail($payload['product_id']);
            $schedule = Schedule::lockForUpdate()
                ->findOrFail($payload['schedule_id']);

            // Cantidades por categoría
            $quantities = $payload['categories'] ?? [];
            if (empty($quantities)) {
                throw new \InvalidArgumentException('No categories provided');
            }

            // Snapshot (price + quantity + slug + name) - usar fecha del producto para precios temporales
            $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($product, $quantities, $payload['tour_date'] ?? null);
            if (empty($categoriesSnapshot)) {
                throw new \RuntimeException('No valid active categories found for this product');
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
                $promo = PromoCode::whereRaw("TRIM(REPLACE(code,' ','')) = ?", [$clean])
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
                    $product,
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
                        'product_id'    => (int) $product->product_id,
                        'schedule_id' => (int) $schedule->schedule_id,
                        'date'       => $date,
                    ]));
                }
            }

            // Total final: PRIMERO sumar impuestos, LUEGO aplicar promo
            $totalWithTaxes = $detailSubtotal + $taxesTotal;
            $totalBooking = $this->pricing->applyPromo($totalWithTaxes, $promo);

            // ============================================
            // PAY-LATER LOGIC
            // ============================================
            $isPayLater = (bool) ($payload['is_pay_later'] ?? false);
            $isPaid = !$isPayLater; // If not pay-later, assume paid immediately

            $paymentToken = \Illuminate\Support\Str::random(64);
            $paymentLinkExpiresAt = null;
            $autoChargeAt = null;
            $pendingExpiresAt = null;

            if ($isPayLater) {
                // Pay-later booking
                $productDate = \Carbon\Carbon::parse($payload['tour_date']);

                // Get settings
                $daysBeforeCharge = (int) setting('booking.pay_later.days_before_charge', 2);
                $linkExpiresHours = (int) setting('booking.pay_later.link_expires_hours', 72);

                // Calculate auto-charge date
                $autoChargeAt = $productDate->copy()->subDays($daysBeforeCharge)->startOfDay();

                // Payment link expires in X hours
                $paymentLinkExpiresAt = now()->addHours($linkExpiresHours);

                // Unpaid booking expires: earlier of (auto-charge date OR link expiration)
                $pendingExpiresAt = $autoChargeAt->copy()->min($paymentLinkExpiresAt);

                $isPaid = false;
            } elseif (($payload['status'] ?? 'pending') === 'pending') {
                // Standard pending booking (not pay-later but not confirmed)
                // Hold for configured time (default 12h)
                $holdMinutes = config('booking.hold_times.unpaid_booking', 720);
                $pendingExpiresAt = now()->addMinutes($holdMinutes);
                $isPaid = false; // Will be set to true when payment succeeds
            }

            // Get user for snapshot
            $user = \App\Models\User::find($payload['user_id']);

            // BOOKING
            $booking = Booking::create([
                'booking_reference' => 'BK' . strtoupper(uniqid()),
                'user_id'           => (int) $payload['user_id'],

                // User snapshot for audit trail
                'user_email'        => $user?->email,
                'user_full_name'    => $user?->full_name,
                'user_phone'        => $user?->phone,
                'user_was_guest'    => (bool) ($user?->is_guest ?? false),

                'product_id'           => (int) $payload['product_id'],
                'tour_language_id'  => (int) $payload['tour_language_id'],
                'booking_date'      => $payload['booking_date'] ?? now(),
                'status'            => $payload['status'] ?? 'pending',
                'total'             => $totalBooking,
                'notes'             => $payload['notes'] ?? null,

                // Payment tracking
                'is_paid'           => $isPaid,
                'paid_amount'       => $isPaid ? $totalBooking : null,
                'paid_at'           => $isPaid ? now() : null,

                // Pay-later
                'is_pay_later'      => $isPayLater,
                'auto_charge_at'    => $autoChargeAt,
                'payment_link_expires_at' => $paymentLinkExpiresAt,
                'pending_expires_at' => $pendingExpiresAt,

                // Payment token already exists from previous migration, just update if needed
                // Token will be set by generateCheckoutToken() below
            ]);

            // BOOKING DETAIL (solo JSON categories + pickups)
            BookingDetail::create([
                'booking_id'        => $booking->booking_id,
                'product_id'           => (int) $payload['product_id'],
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

            // Generar token de checkout para pagos públicos
            $booking->generateCheckoutToken();

            return $booking;
        });
    }
}
