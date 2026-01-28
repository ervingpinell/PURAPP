<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPricingRule extends Model
{
    protected $fillable = [
        'strategy_id',
        'min_passengers',
        'max_passengers',
        'customer_category_id',
        'price',
        'price_type',
        'label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function strategy()
    {
        return $this->belongsTo(ProductPricingStrategy::class, 'strategy_id');
    }

    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id', 'category_id');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Verifica si esta regla aplica para X pasajeros
     */
    public function appliesToPassengerCount(int $passengers): bool
    {
        return $passengers >= $this->min_passengers 
            && $passengers <= $this->max_passengers;
    }

    /**
     * Calcula subtotal para esta regla
     */
    public function calculateSubtotal(int $quantity): float
    {
        if ($this->price_type === 'per_group') {
            return (float) $this->price;
        }

        return (float) $this->price * $quantity;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        $formatted = '$' . number_format($this->price, 2);
        
        if ($this->price_type === 'per_group') {
            return $formatted . ' total';
        }
        
        return $formatted . '/persona';
    }

    /**
     * Get passenger range label
     */
    public function getPassengerRangeAttribute(): string
    {
        if ($this->min_passengers === $this->max_passengers) {
            return "{$this->min_passengers} pax";
        }
        
        return "{$this->min_passengers}-{$this->max_passengers} pax";
    }
}
