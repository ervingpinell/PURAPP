<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CustomerCategory;

class TourPrice extends Model
{
    use HasFactory;

    protected $table = 'tour_prices';
    protected $primaryKey = 'tour_price_id';

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

    /* ==================== Relaciones ==================== */

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'category_id', 'category_id');
    }

    /* ==================== Scopes ==================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTour($query, int $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /* ==================== Helpers ==================== */

    /**
     * Verifica si la cantidad está dentro del rango permitido
     */
    public function isValidQuantity(int $quantity): bool
    {
        return $quantity >= $this->min_quantity && $quantity <= $this->max_quantity;
    }

    /**
     * Calcula el subtotal para una cantidad dada
     */
    public function calculateSubtotal(int $quantity): float
    {
        if (!$this->isValidQuantity($quantity)) {
            return 0;
        }

        return (float) $this->price * $quantity;
    }

    /**
     * Obtiene el rango de cantidad permitida en formato legible
     */
    public function getQuantityRangeAttribute(): string
    {
        if ($this->min_quantity === 0 && $this->max_quantity === 0) {
            return 'No permitido';
        }

        if ($this->min_quantity === $this->max_quantity) {
            return "Exactamente {$this->min_quantity}";
        }

        return "{$this->min_quantity} - {$this->max_quantity}";
    }
}
