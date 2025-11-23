<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'tax_id';

    protected $fillable = [
        'name',
        'code',
        'rate',
        'type',
        'apply_to',
        'is_inclusive',
        'is_active',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_inclusive' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all taxables for this tax
     */
    public function taxables()
    {
        return $this->hasMany(Taxable::class, 'tax_id', 'tax_id');
    }

    /**
     * Get all tours that have this tax
     */
    public function tours()
    {
        return $this->morphedByMany(Tour::class, 'taxable', 'taxables', 'tax_id');
    }

    /**
     * Calculate tax amount based on base amount and quantity
     *
     * @param float $base Base amount to calculate tax on
     * @param int $quantity Number of items/persons
     * @return float Calculated tax amount
     */
    public function calculateAmount(float $base, int $quantity = 1): float
    {
        if ($this->type === 'fixed') {
            return $this->apply_to === 'per_person'
                ? $this->rate * $quantity
                : $this->rate;
        }

        // Percentage calculation
        if ($this->apply_to === 'per_person') {
            // Apply rate to per-person amount, then multiply by quantity
            $perPersonAmount = $base / max($quantity, 1);
            return ($perPersonAmount * ($this->rate / 100)) * $quantity;
        }

        // Apply to subtotal or total
        return $base * ($this->rate / 100);
    }

    /**
     * Scope to get only active taxes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get formatted rate for display
     */
    public function getFormattedRateAttribute(): string
    {
        if ($this->type === 'percentage') {
            return number_format($this->rate, 2) . '%';
        }

        return '₡' . number_format($this->rate, 2);
    }

    /**
     * Get display name with rate
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->formatted_rate})";
    }
}
