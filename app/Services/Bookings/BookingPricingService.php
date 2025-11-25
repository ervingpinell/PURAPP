<?php

namespace App\Services\Bookings;

use App\Models\{PromoCode, Tour};

class BookingPricingService
{
    /** Suma quantity * price en el snapshot */
    public function calculateSubtotal(array $categories): float
    {
        // Legacy support: if no tax_breakdown, use simple math
        return collect($categories)->sum(function ($cat) {
            if (isset($cat['tax_breakdown']['subtotal'])) {
                return $cat['tax_breakdown']['subtotal'];
            }
            return ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0));
        });
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
                // Calculate tax breakdown for this line item
                $breakdown = $price->calculateTaxBreakdown($quantity);

                $snapshot[] = [
                    'category_id'   => (int)$categoryId,
                    'category_name' => $price->category->name,
                    'category_slug' => $price->category->slug ?? strtolower($price->category->name),
                    'quantity'      => $quantity,
                    'price'         => (float)$price->price,
                    'tax_breakdown' => $breakdown, // Store full breakdown
                ];
            }
        }

        return $snapshot;
    }

    /**
     * Calcula totales agregados (subtotal, impuestos, total) desde el snapshot
     */
    public function calculateTotals(array $categories): array
    {
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $total = 0.0;
        $taxesBreakdown = [];

        foreach ($categories as $cat) {
            if (isset($cat['tax_breakdown'])) {
                $bd = $cat['tax_breakdown'];
                $subtotal += $bd['subtotal'];
                $taxTotal += $bd['tax_amount'];
                $total += $bd['total'];

                // Aggregate individual taxes
                foreach ($bd['taxes'] as $tax) {
                    $code = $tax['code'];
                    if (!isset($taxesBreakdown[$code])) {
                        $taxesBreakdown[$code] = [
                            'name' => $tax['name'],
                            'amount' => 0.0,
                            'included' => $tax['included']
                        ];
                    }
                    $taxesBreakdown[$code]['amount'] += $tax['amount'];
                }
            } else {
                // Fallback
                $lineTotal = ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0));
                $subtotal += $lineTotal;
                $total += $lineTotal;
            }
        }

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxTotal, 2),
            'total' => round($total, 2),
            'taxes_breakdown' => $taxesBreakdown
        ];
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
