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
        'tour_id','category_id','price','min_quantity','max_quantity','is_active',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_active'    => 'boolean',
    ];

    /** Relaciones */
    public function tour(){ return $this->belongsTo(Tour::class, 'tour_id', 'tour_id'); }
    public function category(){ return $this->belongsTo(CustomerCategory::class, 'category_id', 'category_id'); }

    /** Scopes */
    public function scopeActive($q){ return $q->where('is_active', true); }
    public function scopeForTour($q, int $tourId){ return $q->where('tour_id', $tourId); }
    public function scopeForCategory($q, int $categoryId){ return $q->where('category_id', $categoryId); }

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
}
