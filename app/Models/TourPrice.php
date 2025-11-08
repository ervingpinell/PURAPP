<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourPrice extends Model
{
    use HasFactory;

    protected $table = 'tour_prices';
    protected $primaryKey = 'tour_price_id';

    // Opcional: que el JSON traiga ya el nombre traducido de la categoría
    protected $appends = ['category_translated_name'];

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

    /**
     * Ordenar por nombre de categoría traducido para el locale actual (JOIN a translations)
     * Uso: TourPrice::query()->orderByCategoryTranslatedName()->get();
     */
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

    /* ==================== Helpers ==================== */

    /**
     * Nombre traducido de la categoría (accesor)
     * Devuelve '' si no hay categoría cargada.
     */
    public function getCategoryTranslatedNameAttribute(): string
    {
        // Si la relación viene precargada con translations, no hay N+1
        $cat = $this->relationLoaded('category') ? $this->category : $this->category()->with('translations')->first();
        return $cat ? $cat->getTranslatedName() : '';
    }

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
            return 0.0;
        }

        return (float) $this->price * $quantity;
    }

    /**
     * Rango legible (i18n opcional)
     * Ajusta las claves de idiomas a tu archivo de traducciones si quieres hacerlo multi-idioma.
     */
    public function getQuantityRangeAttribute(): string
    {
        if ($this->min_quantity === 0 && $this->max_quantity === 0) {
            return __('m_tours.prices.range.not_allowed'); // define esta clave
        }

        if ($this->min_quantity === $this->max_quantity) {
            // "Exactamente :n"
            return __('m_tours.prices.range.exactly', ['n' => $this->min_quantity]);
        }

        // ":min - :max"
        return __('m_tours.prices.range.between', [
            'min' => $this->min_quantity,
            'max' => $this->max_quantity,
        ]);
    }
}
