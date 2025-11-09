<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Policy extends Model
{
    use HasFactory;

    protected $table = 'policies';
    protected $primaryKey = 'policy_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'is_default',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'is_default'     => 'bool',
        'is_active'      => 'bool',
        'effective_from' => 'date',
        'effective_to'   => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ===================== BOOT & AUTO-TRADUCCIONES ===================== */

    protected static function booted()
    {
        // Generar slug automáticamente al crear
        static::creating(function (self $policy) {
            if (empty($policy->slug) && !empty($policy->name)) {
                $policy->slug = $policy->generateUniqueSlug($policy->name);
            }
        });

        // Sembrar traducciones inmediatamente después de crear
        static::created(function (self $policy) {
            $policy->seedMissingTranslations();
        });

        // Permitir actualización manual del slug asegurando unicidad
        static::updating(function (self $policy) {
            if (empty($policy->slug) && !empty($policy->name)) {
                $policy->slug = $policy->generateUniqueSlug($policy->name);
            }

            if ($policy->isDirty('slug') && !empty($policy->slug)) {
                $exists = static::where('slug', $policy->slug)
                    ->where('policy_id', '!=', $policy->policy_id)
                    ->exists();

                if ($exists) {
                    $policy->slug = $policy->generateUniqueSlug($policy->slug);
                }
            }
        });

        // Si cambió el name base, rellenar traducciones vacías con el nuevo valor
        static::updated(function (self $policy) {
            if ($policy->wasChanged('name')) {
                $policy->syncNameIntoTranslations();
            }
        });
    }

    /* ===================== SLUG ===================== */

    public function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->where('policy_id', '!=', $this->policy_id ?? 0)
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function regenerateSlug(?string $baseName = null): self
    {
        $this->slug = $this->generateUniqueSlug($baseName ?? (string)$this->name);
        $this->save();
        return $this;
    }

    /* ===================== TRADUCCIONES ===================== */

    /**
     * Normaliza cualquier variante a un código canónico corto.
     */
    public static function canonicalLocale(string $loc): string
    {
        $loc   = str_replace('-', '_', trim($loc));
        $short = strtolower(substr($loc, 0, 2));

        return match ($short) {
            'es' => 'es',
            'en' => 'en',
            'fr' => 'fr',
            'de' => 'de',
            'pt' => 'pt', // <- unificamos siempre a 'pt'
            default => $loc,
        };
    }

    public function seedMissingTranslations(?array $locales = null): void
    {
        $locales = $locales ?: (array) config('app.supported_locales', ['es','en','fr','pt','de']);
        foreach ($locales as $loc) {
            $norm = self::canonicalLocale($loc);
            $this->translations()->firstOrCreate(
                ['locale' => $norm],
                [
                    'name'    => (string) $this->name,
                    'content' => (string) ($this->content ?? ''),
                ]
            );
        }
    }

    public function syncNameIntoTranslations(): void
    {
        $current = (string) ($this->name ?? '');
        $this->translations()->get()->each(function (PolicyTranslation $tr) use ($current) {
            if (blank($tr->name)) {
                $tr->name = $current;
                $tr->save();
            }
        });
    }

    public function translation(?string $locale = null)
    {
        $requested = self::canonicalLocale($locale ?: app()->getLocale());
        $fallback  = self::canonicalLocale((string) config('app.fallback_locale', 'es'));

        $bag = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        $norm = fn ($v) => str_replace('-', '_', strtolower((string) $v));

        // 1) Coincidencia exacta (case-insensitive)
        if ($exact = $bag->first(fn ($t) => $norm($t->locale) === $norm($requested))) {
            return $exact;
        }

        // 2) Variantes típicas que mapeen a lo mismo (por robustez)
        //    Nota: aunque sólo guardamos 'pt', intentamos coincidir si
        //    hubiera legacy 'pt_BR' en la DB.
        $variants = array_unique([
            $requested,
            str_replace('_', '-', $requested),
            substr($requested, 0, 2), // 'pt'
            'pt_BR',                  // compat: por si quedara algún registro antiguo
            'pt-br',                  // compat
        ]);

        foreach ($variants as $v) {
            if ($found = $bag->first(fn ($t) => $norm($t->locale) === $norm($v))) {
                return $found;
            }
        }

        // 3) Fallback
        return $bag->first(fn ($t) => $norm($t->locale) === $norm($fallback))
            ?: $bag->first(fn ($t) => $norm($t->locale) === $norm(substr($fallback, 0, 2)))
            ?: $bag->first();
    }

    public function translate(?string $locale = null): ?PolicyTranslation
    {
        return $this->translation($locale);
    }

    /* ===================== ACCESSORS PARA BLADE ===================== */

    public function getDisplayNameAttribute(): string
    {
        return (string) (optional($this->translation())?->name ?? $this->name ?? '');
    }

    public function getDisplayContentAttribute(): string
    {
        return (string) (optional($this->translation())?->content ?? $this->content ?? '');
    }

    public function getNameTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->name ?? $this->name;
    }

    public function getTitleTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->name ?? $this->name;
    }

    public function getContentTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->content ?? $this->content;
    }

    /* ===================== RELACIONES ===================== */

    public function translations()
    {
        return $this->hasMany(PolicyTranslation::class, 'policy_id', 'policy_id');
    }

    public function sections()
    {
        return $this->hasMany(PolicySection::class, 'policy_id', 'policy_id')
            ->orderBy('sort_order')
            ->orderBy('section_id');
    }

    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }

    /* ===================== SCOPES ===================== */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeEffectiveOn($q, ?Carbon $date = null)
    {
        $d = ($date ?: now())->toDateString();

        return $q->where(function ($qq) use ($d) {
                $qq->whereNull('effective_from')->orWhereDate('effective_from', '<=', $d);
            })->where(function ($qq) use ($d) {
                $qq->whereNull('effective_to')->orWhereDate('effective_to', '>=', $d);
            });
    }

    public function scopeType($q, string $type)
    {
        if (Schema::hasColumn($this->getTable(), 'type')) {
            return $q->where('type', $type);
        }
        return $q;
    }

    /* ===================== HELPERS ===================== */

    public static function byType(string $type): ?self
    {
        $query = static::query()
            ->active()
            ->effectiveOn()
            ->with('translations');

        if (Schema::hasColumn((new static)->getTable(), 'type')) {
            return $query->where('type', $type)
                ->orderByDesc('is_default')
                ->orderByDesc('effective_from')
                ->first();
        }

        // Búsqueda por nombre base (compat)
        $map = [
            'terminos'    => 'Términos y Condiciones',
            'cancelacion' => 'Política de Cancelación',
            'reembolso'   => 'Política de Reembolsos',
            'privacidad'  => 'Política de Privacidad',
        ];

        if (isset($map[$type])) {
            $base = $map[$type];

            $query->where(function ($qq) use ($base) {
                $qq->where('name', $base)
                   ->orWhere('name', 'like', $base.'%');
            });

            return $query->orderByDesc('effective_from')->first();
        }

        // Búsqueda por traducciones
        return $query->whereHas('translations', function ($qq) use ($type) {
                $qq->where('name', 'like', '%'.$type.'%');
            })
            ->orderByDesc('effective_from')
            ->first();
    }
}
