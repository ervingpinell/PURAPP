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
        // 'name',  // <-- eliminado
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

    /* Scopes */
    public function scopeActive($q){ return $q->where('is_active', true); }
    public function scopeOrdered($q){ return $q->orderBy('order')->orderBy('age_from'); }

    /* Helpers */
    public function getAgeRangeAttribute(): string
    {
        return $this->age_to === null
            ? "{$this->age_from}+"
            : "{$this->age_from}-{$this->age_to}";
    }

    public function includesAge(int $age): bool
    {
        if ($age < $this->age_from) return false;
        if ($this->age_to === null)  return true;
        return $age <= $this->age_to;
    }

    public function validateNoOverlap(): bool
    {
        $query = static::where('category_id', '!=', $this->category_id ?? 0)
            ->where('is_active', true);

        foreach ($query->get() as $existing) {
            if ($this->age_from >= $existing->age_from &&
                ($existing->age_to === null || $this->age_from <= $existing->age_to)) {
                return false;
            }
            if ($this->age_to !== null) {
                if ($this->age_to >= $existing->age_from &&
                    ($existing->age_to === null || $this->age_to <= $existing->age_to)) {
                    return false;
                }
            }
            if ($existing->age_to !== null &&
                $this->age_from <= $existing->age_from &&
                ($this->age_to === null || $this->age_to >= $existing->age_to)) {
                return false;
            }
        }
        return true;
    }

    public static function getActiveCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('customer_categories_active', 3600, function () {
            return static::active()->ordered()->get();
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function () { Cache::forget('customer_categories_active'); });
        static::deleted(function () { Cache::forget('customer_categories_active'); });
    }

    public function tourPrices()
    {
        return $this->hasMany(TourPrice::class, 'category_id', 'category_id');
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_prices', 'category_id', 'tour_id')
            ->withPivot(['price', 'min_quantity', 'max_quantity', 'is_active'])
            ->withTimestamps();
    }

    public function getTranslatedName(?string $locale = null): string
{
    // 1) Locale actual (ej. 'pt-BR' → intenta 'pt-BR' y luego 'pt')
    $locale = $locale ?: app()->getLocale();
    $cands  = array_unique([
        $locale,                        // 'pt-BR'
        substr($locale, 0, 2),          // 'pt'
        config('app.fallback_locale'),  // típico: 'en' o 'es'
        'es',                           // forzamos español como fallback conocido
    ]);

    foreach ($cands as $lc) {
        $t = $this->translations->firstWhere('locale', $lc);
        if ($t && $t->name) return $t->name;
    }

    // Último recurso: cualquier traducción existente o slug “bonito”
    if ($this->translations->isNotEmpty()) {
        return (string) optional($this->translations->first())->name;
    }

    return $this->slug ? \Illuminate\Support\Str::of($this->slug)->replace(['_','-'],' ')->title() : '';
}

// Accessor para usar $category->display_name en las vistas
public function getDisplayNameAttribute(): string
{
    return $this->getTranslatedName();
}

    public function translations()
{
    return $this->hasMany(CustomerCategoryTranslation::class, 'category_id', 'category_id');
}
}
