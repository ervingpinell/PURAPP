<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CustomerCategory extends Model
{
    use HasFactory;

    protected $table = 'customer_categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'slug',
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

    // === Appends útiles si serializas categorías directo a JSON ===
    protected $appends = ['translated'];

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

    public function translations()
    {
        return $this->hasMany(CustomerCategoryTranslation::class, 'category_id', 'category_id');
    }

    /** ===== LOCALIZACIÓN ===== */

    /**
     * Lista ordenada de locales candidatos a evaluar.
     */
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
     * Devuelve el nombre traducido (igual que antes).
     */
    public function getTranslatedName(?string $locale = null): string
    {
        $cands = self::candidateLocales($locale);
        $trs = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        if ($trs->isNotEmpty()) {
            // índices por exacto y por base
            $byExact = [];
            $byBase  = [];
            foreach ($trs as $t) {
                $loc = str_replace('_', '-', (string) $t->locale);
                $byExact[$loc] = $t;
                $base = explode('-', $loc)[0];
                if (!isset($byBase[$base])) $byBase[$base] = $t;
            }

            foreach ($cands as $cand) {
                if (isset($byExact[$cand]) && $byExact[$cand]->name) {
                    return (string) $byExact[$cand]->name;
                }
                $lang = explode('-', $cand)[0];
                if (isset($byBase[$lang]) && $byBase[$lang]->name) {
                    return (string) $byBase[$lang]->name;
                }
            }

            // cualquier traducción disponible
            $any = $trs->firstWhere('name', '!=', null) ?? $trs->first();
            if ($any && $any->name) return (string) $any->name;
        }

        return $this->slug
            ? (string) Str::of($this->slug)->replace(['_','-'],' ')->title()
            : '';
    }

    /**
     * Accessor uniforme: $category->translated
     * (clave estándar que pides para “el nombre traducido”)
     */
    public function getTranslatedAttribute(): string
    {
        return $this->getTranslatedName();
    }

    /**
     * Carga un diccionario de nombres traducidos por ID y por SLUG para un set.
     * Devuelve: ['byId'=>[id=>name], 'bySlug'=>[slug=>name]]
     */
    public static function preloadDictionaries(array $ids = [], array $slugs = [], ?string $locale = null): array
    {
        $ids   = array_values(array_unique(array_filter($ids)));
        $slugs = array_values(array_unique(array_filter($slugs)));

        if (empty($ids) && empty($slugs)) {
            return ['byId'=>[], 'bySlug'=>[]];
        }

        $q = static::query()->with('translations');

        if (!empty($ids))   $q->whereIn('category_id', $ids);
        if (!empty($slugs)) {
            $q->when(!empty($ids),
                fn($qq) => $qq->orWhereIn('slug', $slugs),
                fn($qq) => $qq->whereIn('slug', $slugs)
            );
        }

        $rows = $q->get();

        $byId   = [];
        $bySlug = [];
        foreach ($rows as $row) {
            $name = $row->getTranslatedName($locale);
            $byId[$row->category_id] = $name;
            if ($row->slug) $bySlug[$row->slug] = $name;
        }

        return ['byId' => $byId, 'bySlug' => $bySlug];
    }
}
