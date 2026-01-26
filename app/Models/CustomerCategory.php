<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * CustomerCategory Model
 *
 * Represents a customer category (Adult, Child, Senior, etc.).
 */
class CustomerCategory extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'customer_categories';
    protected $primaryKey = 'category_id';

    public $translatable = ['name'];

    protected $fillable = [
        'slug',
        'name',
        'age_from',
        'age_to',
        'order',
        'is_active',
        'deleted_by',
    ];

    protected $casts = [
        'age_from'  => 'integer',
        'age_to'    => 'integer',
        'order'     => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['translated'];

    /* Scopes */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeOrdered($q)
    {
        return $q->orderBy('order')->orderBy('age_from');
    }

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
            if (
                $this->age_from >= $existing->age_from &&
                ($existing->age_to === null || $this->age_from <= $existing->age_to)
            ) {
                return false;
            }
            if ($this->age_to !== null) {
                if (
                    $this->age_to >= $existing->age_from &&
                    ($existing->age_to === null || $this->age_to <= $existing->age_to)
                ) {
                    return false;
                }
            }
            if (
                $existing->age_to !== null &&
                $this->age_from <= $existing->age_from &&
                ($this->age_to === null || $this->age_to >= $existing->age_to)
            ) {
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
        static::saved(function () {
            Cache::forget('customer_categories_active');
        });
        static::deleted(function () {
            Cache::forget('customer_categories_active');
        });
    }


    // public function tours()
    // {
    //     // Pivot logic might need update if Product keys changed, keeping as is for now if not using pivots directly via category often
    //      return $this->belongsToMany(Product::class, 'tour_prices', 'category_id', 'product_id')
    //         ->withPivot(['price', 'min_quantity', 'max_quantity', 'is_active'])
    //         ->withTimestamps();
    // }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }

    public function scopeOlderThan($query, $days)
    {
        return $query->where('deleted_at', '<=', now()->subDays($days));
    }
    
    // Legacy support via Spatie
    // public function translations() { ... } // Removed

    /** ===== LOCALIZACIÓN ===== */

    public static function candidateLocales(?string $preferred = null): array
    {
        $lc = $preferred ?: app()->getLocale() ?: 'es';
        $lc = str_replace('_', '-', $lc);
        $cands = [$lc];

        $base = explode('-', $lc)[0] ?? null;
        if ($base && !in_array($base, $cands, true)) $cands[] = $base;

        $fallback = str_replace('_', '-', (string) config('app.fallback_locale', 'en'));
        if (!in_array($fallback, $cands, true)) $cands[] = $fallback;

        $fbBase = explode('-', $fallback)[0] ?? null;
        if ($fbBase && !in_array($fbBase, $cands, true)) $cands[] = $fbBase;

        if (!in_array('es', $cands, true)) $cands[] = 'es';

        return array_values(array_unique($cands));
    }

    /**
     * Devuelve el nombre traducido usando Spatie.
     */
    public function getTranslatedName(?string $locale = null): string
    {
        // Spatie handles fallbacks automatically based on config/translatable.php
        // But if we want to support existing complex 'candidateLocales' logic, we can keep it
        // Or simply trust Spatie: return $this->getTranslation('name', $locale ?? app()->getLocale());
        
        // For compatibility with strict fallbacks defined in model:
        $locale = $locale ?: app()->getLocale();
        return $this->getTranslation('name', $locale);
    }

    public function getTranslatedAttribute(): string
    {
        return $this->name; // Spatie accessor automagically returns translated string based on app locale? 
        // No, $this->name returns the JSON if accessed directly on some versions or the string. 
        // Spatie trait: $this->name (attribute) uses the getter to return translation.
        // Actually Spatie trait intercepts attribute access. 
        // $this->name returns translation in current locale.
        return $this->name; 
    }
    
    // NOTE: Spatie `getTranslatedAttribute` override might conflict? 
    // Spatie uses `getAttribute` override.
    // If we define `getNameAttribute`, it overrides Spatie.
    
    /**
     * Carga un diccionario de nombres traducidos.
     */
    public static function preloadDictionaries(array $ids = [], array $slugs = [], ?string $locale = null): array
    {
        $ids   = array_values(array_unique(array_filter($ids)));
        $slugs = array_values(array_unique(array_filter($slugs)));

        if (empty($ids) && empty($slugs)) {
            return ['byId' => [], 'bySlug' => []];
        }

        $q = static::query(); // No need to load 'translations' relation anymore

        if (!empty($ids))   $q->whereIn('category_id', $ids);
        if (!empty($slugs)) {
            $q->when(
                !empty($ids),
                fn($qq) => $qq->orWhereIn('slug', $slugs),
                fn($qq) => $qq->whereIn('slug', $slugs)
            );
        }

        $rows = $q->get();

        $byId   = [];
        $bySlug = [];
        $locale = $locale ?: app()->getLocale();
        
        foreach ($rows as $row) {
            $name = $row->getTranslation('name', $locale);
            $byId[$row->category_id] = $name;
            if ($row->slug) $bySlug[$row->slug] = $name;
        }

        return ['byId' => $byId, 'bySlug' => $bySlug];
    }
}
