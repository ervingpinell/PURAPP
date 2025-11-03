<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class CustomerCategory extends Model
{
    use HasFactory;

    protected $table = 'customer_categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'slug',
        'name',
        'age_from',
        'age_to',
        'order',
        'is_active',
    ];

    protected $casts = [
        'age_from'  => 'integer',
        'age_to'    => 'integer',
        'order'     => 'integer',
        'is_active' => 'boolean',
    ];

    /* ==================== Scopes ==================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('age_from');
    }

    /* ==================== Helpers ==================== */

    /**
     * Obtiene el rango de edad en formato legible
     * Ejemplos: "0-2", "3-12", "13-17", "18+"
     */
    public function getAgeRangeAttribute(): string
    {
        if ($this->age_to === null) {
            return "{$this->age_from}+";
        }
        return "{$this->age_from}-{$this->age_to}";
    }

    /**
     * Verifica si una edad específica pertenece a esta categoría
     */
    public function includesAge(int $age): bool
    {
        if ($age < $this->age_from) {
            return false;
        }

        if ($this->age_to === null) {
            return true; // Sin límite superior
        }

        return $age <= $this->age_to;
    }

    /**
     * Valida que los rangos de edad no se solapen con otras categorías
     */
    public function validateNoOverlap(): bool
    {
        $query = static::where('category_id', '!=', $this->category_id ?? 0)
            ->where('is_active', true);

        foreach ($query->get() as $existing) {
            // Si esta categoría empieza dentro del rango de otra
            if ($this->age_from >= $existing->age_from &&
                ($existing->age_to === null || $this->age_from <= $existing->age_to)) {
                return false;
            }

            // Si esta categoría termina dentro del rango de otra
            if ($this->age_to !== null) {
                if ($this->age_to >= $existing->age_from &&
                    ($existing->age_to === null || $this->age_to <= $existing->age_to)) {
                    return false;
                }
            }

            // Si esta categoría envuelve a otra
            if ($existing->age_to !== null &&
                $this->age_from <= $existing->age_from &&
                ($this->age_to === null || $this->age_to >= $existing->age_to)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtiene todas las categorías activas (cached)
     */
    public static function getActiveCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('customer_categories_active', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Limpia el cache cuando se modifica
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('customer_categories_active');
        });

        static::deleted(function () {
            Cache::forget('customer_categories_active');
        });
    }

    /* ==================== Traducciones (Opcional) ==================== */

    public function translations()
    {
        return $this->hasMany(CustomerCategoryTranslation::class, 'category_id');
    }

    public function getTranslatedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        $translation = $this->translations()
            ->where('locale', $locale)
            ->first();

        return $translation?->name ?? $this->name;
    }

    /**
 * Relación con precios de tours
 */
public function tourPrices()
{
    return $this->hasMany(TourPrice::class, 'category_id', 'category_id');
}

/**
 * Tours que usan esta categoría
 */
public function tours()
{
    return $this->belongsToMany(Tour::class, 'tour_prices', 'category_id', 'tour_id')
        ->withPivot(['price', 'min_quantity', 'max_quantity', 'is_active'])
        ->withTimestamps();
}

}
