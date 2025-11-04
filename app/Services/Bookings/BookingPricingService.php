<?php

namespace App\Services\Bookings;

use App\Models\{PromoCode, Tour};

class BookingPricingService
{
    /** Suma quantity * price en el snapshot */
    public function calculateSubtotal(array $categories): float
    {
        $total = collect($categories)->sum(function ($cat) {
            return ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0));
        });

        return round($total, 2);
    }

    /**
     * Construye snapshot de categorías activas para el tour a partir de quantities
     * $quantities = ['category_id' => quantity, ...]
     */
    public function buildCategoriesSnapshot(Tour $tour, array $quantities): array
    {
        $snapshot = [];

        foreach ($quantities as $categoryId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity <= 0) continue;

            $price = $tour->prices()
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->whereHas('category', fn($q) => $q->where('is_active', true))
                ->with('category')
                ->first();

            if ($price) {
                $snapshot[] = [
                    'category_id'   => (int)$categoryId,
                    'category_name' => $price->category->name,
                    'category_slug' => $price->category->slug ?? strtolower($price->category->name),
                    'quantity'      => $quantity,
                    'price'         => (float)$price->price,
                ];
            }
        }

        return $snapshot;
    }

    /** Aplica promo al subtotal */
    public function applyPromo(float $subtotal, ?PromoCode $promo): float
    {
        if (!$promo) return $subtotal;

        $discount = 0.0;
        if ($promo->discount_percent) {
            $discount = round($subtotal * ($promo->discount_percent / 100), 2);
        } elseif ($promo->discount_amount) {
            $discount = (float)$promo->discount_amount;
        }

        return $promo->operation === 'add'
            ? round($subtotal + $discount, 2)
            : max(0, round($subtotal - $discount, 2));
    }

    /** Total pax desde snapshot */
    public function getTotalPaxFromCategories(array $categories): int
    {
        return collect($categories)->sum('quantity');
    }
}
