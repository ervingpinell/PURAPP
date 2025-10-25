<?php

// app/Services/Bookings/BookingPricingService.php
namespace App\Services\Bookings;

use App\Models\PromoCode;

class BookingPricingService
{
    public function subtotal(float $adultPrice, int $adults, float $kidPrice, int $kids): float
    {
        return ($adultPrice * $adults) + ($kidPrice * $kids);
    }

    public function applyPromo(float $subtotal, ?PromoCode $promo): float
    {
        if (!$promo) return $subtotal;

        $discount = 0.0;
        if ($promo->discount_percent) $discount = round($subtotal * ($promo->discount_percent / 100), 2);
        elseif ($promo->discount_amount) $discount = (float)$promo->discount_amount;

        return $promo->operation === 'add'
            ? round($subtotal + $discount, 2)
            : max(0, round($subtotal - $discount, 2));
    }
}
