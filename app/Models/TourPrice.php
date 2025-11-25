<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourPrice extends Model
{
    use HasFactory;

    protected $table = 'tour_prices';
    protected $primaryKey = 'tour_price_id';

    protected $appends = ['category_translated', 'quantity_range'];

    protected $fillable = [
        'tour_id',
        'category_id',
        'price',
        'min_quantity',
        'max_quantity',
        'is_active',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_active'    => 'boolean',
    ];

    /** Relaciones */
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'category_id', 'category_id');
    }

    /** Scopes */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeForTour($q, int $tourId)
    {
        return $q->where('tour_id', $tourId);
    }
    public function scopeForCategory($q, int $categoryId)
    {
        return $q->where('category_id', $categoryId);
    }

    public function scopeOrderByCategoryTranslatedName($q, ?string $locale = null)
    {
        $locale = $locale ? substr($locale, 0, 2) : substr(app()->getLocale() ?? 'es', 0, 2);
        return $q->leftJoin('customer_category_translations as cct', function ($j) use ($locale) {
            $j->on('cct.category_id', '=', 'tour_prices.category_id')
                ->where('cct.locale', '=', $locale);
        })
            ->orderBy('cct.name')
            ->select('tour_prices.*');
    }

    /** Helpers */
    public function getCategoryTranslatedAttribute(): string
    {
        $cat = $this->relationLoaded('category') ? $this->category : $this->category()->with('translations')->first();
        return $cat ? $cat->translated : '';
    }

    // (opcional) mantener compatibilidad con "category_translated_name"
    public function getCategoryTranslatedNameAttribute(): string
    {
        return $this->category_translated;
    }

    public function isValidQuantity(int $quantity): bool
    {
        return $quantity >= $this->min_quantity && $quantity <= $this->max_quantity;
    }

    public function calculateSubtotal(int $quantity): float
    {
        return $this->isValidQuantity($quantity) ? (float)$this->price * $quantity : 0.0;
    }

    public function getQuantityRangeAttribute(): string
    {
        if ($this->min_quantity === 0 && $this->max_quantity === 0) {
            return __('m_tours.prices.range.not_allowed');
        }
        if ($this->min_quantity === $this->max_quantity) {
            return __('m_tours.prices.range.exactly', ['n' => $this->min_quantity]);
        }
        return __('m_tours.prices.range.between', [
            'min' => $this->min_quantity,
            'max' => $this->max_quantity,
        ]);
    }

    /**
     * Obtiene el precio con impuestos incluidos
     * Alias de final_price para mantener compatibilidad
     */
    public function getPriceWithTaxAttribute(): float
    {
        return $this->final_price;
    }

    /**
     * Calculate tax breakdown for this price
     * Returns array with subtotal, tax_amount, and total
     * 
     * @param int $quantity Number of persons/items
     * @param bool|null $taxIncluded Deprecated, ignored in favor of tax->is_inclusive
     * @return array ['subtotal' => float, 'tax_amount' => float, 'total' => float, 'taxes' => array]
     */
    public function calculateTaxBreakdown(int $quantity = 1, ?bool $taxIncluded = null): array
    {
        $basePrice = (float) $this->price;
        $tour = $this->tour;

        // Note: $taxIncluded parameter is ignored. We use $tax->is_inclusive for each tax.

        if (!$tour) {
            return [
                'subtotal' => round($basePrice * $quantity, 2),
                'tax_amount' => 0,
                'total' => round($basePrice * $quantity, 2),
                'taxes' => [],
            ];
        }

        $taxes = $tour->taxes;
        $taxDetails = [];

        // Separate taxes into inclusive and exclusive
        $inclusiveTaxes = $taxes->filter(fn($t) => $t->is_inclusive);
        $exclusiveTaxes = $taxes->filter(fn($t) => !$t->is_inclusive);

        // 1. Calculate Base Price
        // If there are inclusive taxes, the stored price includes them.
        // Base = Price / (1 + sum(rates))
        $basePriceTotal = $basePrice * $quantity;

        if ($inclusiveTaxes->isNotEmpty()) {
            $totalRate = 0;
            $fixedDeduction = 0;

            foreach ($inclusiveTaxes as $tax) {
                if ($tax->type === 'percentage') {
                    $totalRate += $tax->rate;
                } elseif ($tax->type === 'fixed') {
                    $fixedDeduction += ($tax->apply_to === 'per_person' ? $tax->rate * $quantity : $tax->rate);
                }
            }

            // Remove fixed taxes first (usually)
            $tempTotal = $basePriceTotal - $fixedDeduction;
            // Then remove percentage taxes
            $basePriceTotal = $tempTotal / (1 + ($totalRate / 100));
        }

        $taxDetails = [];
        $totalTaxAmount = 0;

        // 2. Calculate Inclusive Tax Amounts
        foreach ($inclusiveTaxes as $tax) {
            // Re-calculate based on the derived base price
            // For inclusive, the amount is the difference between what it would be and the base
            // Or simply: Base * Rate
            $amount = 0;
            if ($tax->type === 'percentage') {
                $amount = $basePriceTotal * ($tax->rate / 100);
            } elseif ($tax->type === 'fixed') {
                $amount = ($tax->apply_to === 'per_person' ? $tax->rate * $quantity : $tax->rate);
            }

            $taxDetails[] = [
                'name' => $tax->name,
                'code' => $tax->code,
                'rate' => $tax->formatted_rate,
                'amount' => round($amount, 2),
                'included' => true
            ];
            $totalTaxAmount += $amount;
        }

        // 3. Calculate Exclusive Tax Amounts
        foreach ($exclusiveTaxes as $tax) {
            $amount = 0;
            if ($tax->type === 'percentage') {
                $amount = $basePriceTotal * ($tax->rate / 100);
            } elseif ($tax->type === 'fixed') {
                $amount = ($tax->apply_to === 'per_person' ? $tax->rate * $quantity : $tax->rate);
            }

            $taxDetails[] = [
                'name' => $tax->name,
                'code' => $tax->code,
                'rate' => $tax->formatted_rate,
                'amount' => round($amount, 2),
                'included' => false
            ];
            $totalTaxAmount += $amount;
        }

        // Final totals
        // Subtotal should be the Base Price
        // Total should be Base + All Taxes

        return [
            'subtotal' => round($basePriceTotal, 2),
            'tax_amount' => round($totalTaxAmount, 2),
            'total' => round($basePriceTotal + $totalTaxAmount, 2),
            'taxes' => $taxDetails,
        ];
    }

    /**
     * Get the final price to display (respects tax_included setting)
     * This is what should be shown to customers
     */
    public function getFinalPriceAttribute(): float
    {
        $breakdown = $this->calculateTaxBreakdown(1);
        return $breakdown['total'];
    }

    /**
     * Get price without tax (subtotal)
     */
    public function getPriceWithoutTaxAttribute(): float
    {
        $breakdown = $this->calculateTaxBreakdown(1);
        return $breakdown['subtotal'];
    }
}
