<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Policy Model
 *
 * Represents a policy (cancellation, terms, etc.).
 */
class Policy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'policies';
    protected $primaryKey = 'policy_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Policy types (canonical identifiers)
    public const TYPE_TERMS = 'terms';
    public const TYPE_PRIVACY = 'privacy';
    public const TYPE_CANCELLATION = 'cancellation';
    public const TYPE_REFUND = 'refund';
    public const TYPE_WARRANTY = 'warranty';

    public const TYPES = [
        self::TYPE_TERMS => 'Terms and Conditions',
        self::TYPE_PRIVACY => 'Privacy Policy',
        self::TYPE_CANCELLATION => 'Cancellation Policy',
        self::TYPE_REFUND => 'Refund Policy',
        self::TYPE_WARRANTY => 'Warranty Policy',
    ];

    protected $fillable = [
        // OJO: sin name/content (ya no existen en policies)
        'slug',
        'type',
        'is_default',
        'is_active',
        'effective_from',
        'effective_to',
        'deleted_by', // Allow mass assignment for soft delete tracking
        // si tienes 'type' u otros, agréguelos aquí
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

    /* ===================== BOOT ===================== */

    protected static function booted()
    {
        // Asegurar unicidad del slug si se modifica/proporciona
        static::creating(function (self $policy) {
            if (empty($policy->slug)) {
                // Si quieres auto-generar slug aquí, toma la decisión desde el controlador.
                // Aquí solo garantizamos unicidad si existe.
                $policy->slug = $policy->slug ?: null;
            } else {
                $policy->slug = $policy->generateUniqueSlug($policy->slug);
            }
        });

        static::updating(function (self $policy) {
            if ($policy->isDirty('slug') && !empty($policy->slug)) {
                $policy->slug = $policy->generateUniqueSlug($policy->slug);
            }
        });
    }

    /* ===================== SLUG ===================== */

    public function generateUniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->where('policy_id', '!=', $this->policy_id ?? 0)
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function regenerateSlug(string $from): self
    {
        $this->slug = $this->generateUniqueSlug($from);
        $this->save();
        return $this;
    }

    /* ===================== TRADUCCIONES ===================== */

    /**
     * Normaliza cualquier variante a un código canónico corto.
     * Guardamos 'pt' como convención (no 'pt_BR').
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
            'pt' => 'pt',
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

        $norm = fn($v) => str_replace('-', '_', strtolower((string) $v));

        // Exacta
        if ($exact = $bag->first(fn($t) => $norm($t->locale) === $norm($requested))) {
            return $exact;
        }

        // Variantes típicas (compatibilidad)
        $variants = array_unique([
            $requested,
            str_replace('_', '-', $requested),
            substr($requested, 0, 2), // ej. 'pt'
            'pt_BR',
            'pt-br',         // compat si quedó legacy
        ]);

        foreach ($variants as $v) {
            if ($found = $bag->first(fn($t) => $norm($t->locale) === $norm($v))) {
                return $found;
            }
        }

        // Fallback
        return $bag->first(fn($t) => $norm($t->locale) === $norm($fallback))
            ?: $bag->first(fn($t) => $norm($t->locale) === $norm(substr($fallback, 0, 2)))
            ?: $bag->first();
    }

    public function translate(?string $locale = null): ?PolicyTranslation
    {
        return $this->translation($locale);
    }

    /* ===================== ACCESSORS PARA BLADE ===================== */
    // Solo desde translations (sin fallback a columnas base)

    public function getDisplayNameAttribute(): string
    {
        return (string) (optional($this->translation())?->name ?? '');
    }

    public function getDisplayContentAttribute(): string
    {
        return (string) (optional($this->translation())?->content ?? '');
    }

    public function getNameTranslatedAttribute(): ?string
    {
        return $this->translation()?->name;
    }

    public function getTitleTranslatedAttribute(): ?string
    {
        return $this->translation()?->name;
    }

    public function getContentTranslatedAttribute(): ?string
    {
        return $this->translation()?->content;
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

    public function deletedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
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
        // Todo por traducciones; ya no usamos name de policies
        $query = static::query()
            ->active()
            ->effectiveOn()
            ->with('translations');

        // Si tienes columna 'type' en policies:
        if (Schema::hasColumn((new static)->getTable(), 'type')) {
            return $query->where('type', $type)
                ->orderByDesc('effective_from')
                ->first();
        }

        // Búsqueda por traducciones (por nombre)
        return $query->whereHas('translations', function ($qq) use ($type) {
            $qq->where('name', 'ilike', '%' . $type . '%'); // ILIKE si estás en Postgres
        })
            ->orderByDesc('effective_from')
            ->first();
    }
}
