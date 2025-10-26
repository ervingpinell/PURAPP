<?php

namespace App\Services\Bookings;

use App\Models\{Booking, BookingDetail, Tour, Schedule, PromoCode};
use Illuminate\Support\Facades\DB;

class BookingCreator
{
    public function __construct(
        private BookingCapacityService $cap,
        private BookingPricingService  $pricing,
    ) {}

    /**
     * Crea una reserva con snapshots de precios y (opcional) aplica cupón.
     *
     * @param  array  $payload
     *  - user_id, tour_id, schedule_id, tour_language_id, tour_date (Y-m-d)
     *  - booking_date (opcional), adults_quantity, kids_quantity (opcional)
     *  - status (pending/confirmed/cancelled)
     *  - promo_code (opcional, string)
     *  - meeting_point_id (opcional), hotel_id (opcional), is_other_hotel (bool), other_hotel_name (nullable)
     *  - notes (nullable)
     * @param  bool   $validateCapacity  Si true, valida cupos al crear confirmed.
     */
    public function create(array $payload, bool $validateCapacity = true): Booking
    {
        return DB::transaction(function () use ($payload, $validateCapacity) {
            // ===== Entities
            $tour     = Tour::findOrFail($payload['tour_id']);
            $schedule = Schedule::findOrFail($payload['schedule_id']);

            // ===== Precios unitarios (snapshots)
            $adultPrice = (float) $tour->adult_price;
            $kidPrice   = (float) $tour->kid_price;

            $adults = (int) $payload['adults_quantity'];
            $kids   = (int) ($payload['kids_quantity'] ?? 0);

            // ===== Subtotal (sin promo) para detalle
            $detailSubtotal = round($adultPrice * $adults + $kidPrice * $kids, 2);

            // ===== Promo (si viene)
            $promo = null;
            if (!empty($payload['promo_code'])) {
                $clean = PromoCode::normalize($payload['promo_code']);
                $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])->first();

                if ($promo && method_exists($promo,'isValidToday') && !$promo->isValidToday())         $promo = null;
                if ($promo && method_exists($promo,'hasRemainingUses') && !$promo->hasRemainingUses()) $promo = null;
            }

            // ===== Total final (aplicando promo si existe)
            $totalBooking = $this->pricing->applyPromo($detailSubtotal, $promo);

            // ===== Capacidad si confirmas
            if ($validateCapacity && ($payload['status'] ?? 'pending') === 'confirmed') {
                $max       = $this->cap->resolveMaxCapacity($schedule, $tour);
                $confirmed = $this->cap->confirmedPaxFor($payload['tour_date'], (int)$payload['schedule_id'], null, (int)$tour->tour_id);
                $available = $max - $confirmed;
                $requested = $adults + $kids;

                if ($requested > $available) {
                    throw new \RuntimeException(json_encode([
                        'type'      => 'capacity',
                        'available' => max(0, $available),
                        'max'       => $max,
                    ]));
                }
            }

            // ===== CABECERA
            $booking = Booking::create([
                'booking_reference' => 'BK'.strtoupper(uniqid()),
                'user_id'           => (int)$payload['user_id'],
                'tour_id'           => (int)$payload['tour_id'],
                'tour_language_id'  => (int)$payload['tour_language_id'],
                'booking_date'      => $payload['booking_date'] ?? now(),
                'status'            => $payload['status'] ?? 'pending',
                // Fuente de verdad del cupón es el pivot (no guardamos promo_code_id aquí)
                'total'             => $totalBooking,
                'notes'             => $payload['notes'] ?? null,
            ]);

            // ===== DETALLE (snapshot sin promo)
            BookingDetail::create([
                'booking_id'        => $booking->booking_id,
                'tour_id'           => (int)$payload['tour_id'],
                'schedule_id'       => (int)$payload['schedule_id'],
                'tour_date'         => $payload['tour_date'],
                'tour_language_id'  => (int)$payload['tour_language_id'],

                'adults_quantity'   => $adults,
                'kids_quantity'     => $kids,

                'adult_price'       => $adultPrice,
                'kid_price'         => $kidPrice,
                'total'             => $detailSubtotal,

                // snapshots de pickup/hotel
                'hotel_id'          => !empty($payload['is_other_hotel']) ? null : ($payload['hotel_id'] ?? null),
                'is_other_hotel'    => (bool)($payload['is_other_hotel'] ?? false),
                'other_hotel_name'  => $payload['other_hotel_name'] ?? null,
                'meeting_point_id'  => $payload['meeting_point_id'] ?? null,
            ]);

            // ===== REDENCIÓN (pivot con snapshots)
            if ($promo) {
                // Calcula monto aplicado contra el subtotal-snapshot
                $appliedAmount = 0.0;
                if ($promo->discount_percent)    $appliedAmount = round($detailSubtotal * ($promo->discount_percent/100), 2);
                elseif ($promo->discount_amount) $appliedAmount = (float)$promo->discount_amount;

                // 1) crea/incrementa redención/contador
                if (method_exists($promo, 'redeemForBooking')) {
                    $promo->redeemForBooking($booking->booking_id, $payload['user_id'] ?? null);

                    // 2) completa snapshots en el pivot recién creado
                    $booking->loadMissing('redemption');
                    $booking->redemption?->update([
                        'applied_amount'     => $appliedAmount,
                        'operation_snapshot' => $promo->operation ?? 'subtract',
                        'percent_snapshot'   => $promo->discount_percent,
                        'amount_snapshot'    => $promo->discount_amount,
                    ]);
                } else {
                    // Camino manual si no usas el método helper
                    $booking->redemption()->create([
                        'promo_code_id'      => (int)$promo->id,
                        'user_id'            => $payload['user_id'] ?? null,
                        'used_at'            => now(),

                        'applied_amount'     => $appliedAmount,
                        'operation_snapshot' => $promo->operation ?? 'subtract',
                        'percent_snapshot'   => $promo->discount_percent,
                        'amount_snapshot'    => $promo->discount_amount,
                    ]);
                }
            }

            return $booking;
        });
    }
}
