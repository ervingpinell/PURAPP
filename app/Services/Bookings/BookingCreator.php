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

public function create(array $payload, bool $validateCapacity = true): Booking
{
    return DB::transaction(function () use ($payload, $validateCapacity) {
        $tour     = Tour::findOrFail($payload['tour_id']);
        $schedule = Schedule::findOrFail($payload['schedule_id']);

        // precios unitarios del tour (snapshots)
        $adultPrice = (float) $tour->adult_price;
        $kidPrice   = (float) $tour->kid_price;

        $adults = (int) $payload['adults_quantity'];
        $kids   = (int) ($payload['kids_quantity'] ?? 0);

        // subtotal (sin promo) para el detalle
        $detailSubtotal = round($adultPrice * $adults + $kidPrice * $kids, 2);

        // Promo
        $promo = null;
        if (!empty($payload['promo_code'])) {
            $clean = PromoCode::normalize($payload['promo_code']);
            $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])->first();
            if ($promo && method_exists($promo, 'isValidToday') && !$promo->isValidToday())       { $promo = null; }
            if ($promo && method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses()){ $promo = null; }
        }

        // total del booking (con promo aplicada)
        $totalBooking = $this->pricing->applyPromo($detailSubtotal, $promo);

        // Capacidad si confirmas
        if ($validateCapacity && ($payload['status'] ?? 'pending') === 'confirmed') {
            $max       = $this->cap->resolveMaxCapacity($schedule, $tour);
            $confirmed = $this->cap->confirmedPaxFor($payload['tour_date'], (int)$payload['schedule_id'], null);
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

        // CABECERA
        $booking = Booking::create([
            'booking_reference' => 'BK'.strtoupper(uniqid()),
            'user_id'           => (int)$payload['user_id'],
            'tour_id'           => (int)$payload['tour_id'],
            'tour_language_id'  => (int)$payload['tour_language_id'],
            'booking_date'      => $payload['booking_date'] ?? now(),
            'status'            => $payload['status'] ?? 'pending',
            'promo_code_id'     => $promo?->promo_code_id,
            'total'             => $totalBooking,
            'notes'             => $payload['notes'] ?? null,
        ]);

        // DETALLE (incluye precios NOT NULL)
        BookingDetail::create([
            'booking_id'        => $booking->booking_id,
            'tour_id'           => (int)$payload['tour_id'],
            'schedule_id'       => (int)$payload['schedule_id'],
            'tour_date'         => $payload['tour_date'],
            'tour_language_id'  => (int)$payload['tour_language_id'],

            'adults_quantity'   => $adults,
            'kids_quantity'     => $kids,

            // ← campos NOT NULL en tu tabla
            'adult_price'       => $adultPrice,
            'kid_price'         => $kidPrice,
            'total'             => $detailSubtotal,

            // pickup/hotel snapshots
            'hotel_id'          => !empty($payload['is_other_hotel']) ? null : ($payload['hotel_id'] ?? null),
            'is_other_hotel'    => (bool)($payload['is_other_hotel'] ?? false),
            'other_hotel_name'  => $payload['other_hotel_name'] ?? null,
            'meeting_point_id'  => $payload['meeting_point_id'] ?? null,
        ]);

        return $booking;
    });
}

}
