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
        // ✅ Generar slug automáticamente al crear
        static::creating(function (self $policy) {
            if (empty($policy->slug) && !empty($policy->name)) {
                $policy->slug = $policy->generateUniqueSlug($policy->name);
            }
        });

        static::created(function (self $policy) {
            $policy->seedMissingTranslations();
        });

        // ✅ CORREGIDO: Permitir actualización manual del slug
        static::updating(function (self $policy) {
            // Si el slug está vacío pero hay nombre, regenerarlo
            if (empty($policy->slug) && !empty($policy->name)) {
                $policy->slug = $policy->generateUniqueSlug($policy->name);
            }

            // Si el slug cambió manualmente, validar que sea único
            if ($policy->isDirty('slug') && !empty($policy->slug)) {
                $exists = static::where('slug', $policy->slug)
                    ->where('policy_id', '!=', $policy->policy_id)
                    ->exists();

                if ($exists) {
                    // Agregar sufijo para hacerlo único
                    $policy->slug = $policy->generateUniqueSlug($policy->slug);
                }
            }
        });

        static::updated(function (self $policy) {
            if ($policy->wasChanged('name')) {
                $policy->syncNameIntoTranslations();
            }
        });
    }

    /* ===================== SLUG GENERATION ===================== */

    /**
     * Genera un slug único basado en el nombre
     */
    public function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Verificar que sea único
        while (static::where('slug', $slug)
            ->where('policy_id', '!=', $this->policy_id ?? 0)
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Regenerar slug manualmente (útil para admin)
     */
    public function regenerateSlug(?string $baseName = null): self
    {
        $this->slug = $this->generateUniqueSlug($baseName ?? $this->name);
        $this->save();
        return $this;
    }

    /**
     * Sembrar traducciones faltantes para locales soportados.
     */
    public function seedMissingTranslations(?array $locales = null): void
    {
        $locales = $locales ?: config('app.supported_locales', ['es','en','fr','pt','de']);
        foreach ($locales as $loc) {
            $norm = self::canonicalLocale($loc);
            $this->translations()->firstOrCreate(
                ['locale' => $norm],
                ['name' => (string) $this->name, 'content' => '']
            );
        }
    }

    /**
     * Rellenar name traducido si está vacío cuando cambia el base.
     */
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

    /* ===================== LOCALIZACIÓN ===================== */

    public static function canonicalLocale(string $loc): string
    {
        $loc   = str_replace('-', '_', trim($loc));
        $short = strtolower(substr($loc, 0, 2));

        return match ($short) {
            'es' => 'es',
            'en' => 'en',
            'fr' => 'fr',
            'de' => 'de',
            'pt' => 'pt_BR',
            default => $loc,
        };
    }

    public function translation(?string $locale = null)
    {
        $requested = self::canonicalLocale($locale ?: app()->getLocale());
        $fallback  = self::canonicalLocale((string) config('app.fallback_locale', 'es'));

        $bag = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        $candidates = array_values(array_unique([
            $requested,
            strtolower($requested),
            strtoupper($requested),
            str_replace('_', '-', $requested),
            str_replace('-', '_', $requested),
            substr($requested, 0, 2),
        ]));

        $found = $bag->first(function ($t) use ($candidates) {
            $v = (string) ($t->locale ?? '');
            $norms = [
                $v,
                strtolower($v),
                strtoupper($v),
                str_replace('-', '_', $v),
                str_replace('_', '-', $v),
                substr($v, 0, 2),
            ];
            return count(array_intersect($candidates, $norms)) > 0;
        });

        if ($found) return $found;

        return $bag->firstWhere('locale', $fallback)
            ?: $bag->firstWhere('locale', substr($fallback, 0, 2))
            ?: $bag->first();
    }

    public function translate(?string $locale = null)
    {
        return $this->translation($locale);
    }

    /* ===================== ACCESSORS ===================== */

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
        return optional($this->translation())?->content;
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

        return $query->whereHas('translations', function ($qq) use ($type) {
                $qq->where('name', 'like', '%'.$type.'%');
            })
            ->orderByDesc('effective_from')
            ->first();
    }
}
