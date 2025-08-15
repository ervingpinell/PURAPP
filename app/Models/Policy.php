<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class Policy extends Model
{
    use HasFactory;

    /** ───────────────── Configuración base ───────────────── */
    protected $table = 'policies';
    protected $primaryKey = 'policy_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Si tu esquema ACTUAL NO tiene 'type', es mejor no incluirlo en fillable.
    // Si lo tienes, puedes volver a añadirlo aquí sin problema.
    protected $fillable = [
        // 'type',
        'name',
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

    /** Route model binding usando la PK numérica */
    public function getRouteKeyName(): string
    {
        return 'policy_id';
    }

    /** ───────────────── Relaciones ───────────────── */

    public function translations()
    {
        return $this->hasMany(PolicyTranslation::class, 'policy_id', 'policy_id');
    }

    /**
     * Todas las secciones (admin).
     * Ordena por sort_order y luego por PK para estabilidad.
     */
    public function sections()
    {
        return $this->hasMany(PolicySection::class, 'policy_id', 'policy_id')
                    ->orderBy('sort_order')
                    ->orderBy('section_id');
    }

    /**
     * Solo secciones activas (público).
     */
    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }

    /** ───────────────── Helpers de traducción ───────────────── */

    /**
     * Devuelve la traducción en $locale o hace fallback a la configuración.
     */
    public function translation(?string $locale = null)
    {
        $loc      = $locale ?: app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'es');

        $bag = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        return $bag->firstWhere('locale', $loc)
            ?: $bag->firstWhere('locale', $fallback)
            ?: $bag->first();
    }

    /** Alias para parecerse a otros modelos (FAQ, etc.) */
    public function translate(?string $locale = null)
    {
        return $this->translation($locale);
    }

    /** Accessors convenientes */
    public function getTitleTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->title;
    }

    public function getContentTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->content;
    }

    /** ───────────────── Scopes ───────────────── */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /**
     * Políticas vigentes en una fecha (default: hoy).
     * Incluye rangos abiertos (null).
     */
    public function scopeEffectiveOn($q, ?Carbon $date = null)
    {
        $d = ($date ?: now())->toDateString();

        return $q->where(function ($qq) use ($d) {
            $qq->whereNull('effective_from')->orWhereDate('effective_from', '<=', $d);
        })->where(function ($qq) use ($d) {
            $qq->whereNull('effective_to')->orWhereDate('effective_to', '>=', $d);
        });
    }

    /**
     * Scope de compatibilidad con "type":
     * - Usa columna 'type' si existe.
     * - Si no existe, NO aplica filtro aquí (para evitar SQL error); deja el filtro al helper byType().
     */
    public function scopeType($q, string $type)
    {
        if (Schema::hasColumn($this->getTable(), 'type')) {
            return $q->where('type', $type);
        }
        // Sin columna 'type', devolvemos $q tal cual (no filtramos).
        return $q;
    }

    /** ───────────────── Helpers (compat) ───────────────── */

    /**
     * Compat con el código antiguo: devuelve la política activa por "tipo".
     * Si existe la columna 'type', la usa. Si NO existe:
     *   - mapea por 'name' (p. ej. "Política de Cancelación")
     *   - como último recurso, busca por traducciones (title LIKE).
     */
    public static function byType(string $type): ?self
    {
        $q = static::query()->active();

        // Caso 1: existe 'type'
        if (Schema::hasColumn((new static)->getTable(), 'type')) {
            return $q->where('type', $type)
                     ->orderByDesc('is_default')
                     ->orderByDesc('effective_from')
                     ->first();
        }

        // Caso 2: fallback por nombre "humano"
        $map = [
            'terminos'    => 'Términos y Condiciones',
            'cancelacion' => 'Política de Cancelación',
            'reembolso'   => 'Política de Reembolsos',
            'privacidad'  => 'Política de Privacidad',
        ];

        if (isset($map[$type])) {
            $base = $map[$type];

            $q->where(function ($qq) use ($base) {
                $qq->where('name', $base)
                   ->orWhere('name', 'like', $base.'%'); // cubre "(General)", etc.
            });

            return $q->orderByDesc('effective_from')->first();
        }

        // Último recurso: buscar por títulos traducidos que contengan el término
        return $q->whereHas('translations', function ($qq) use ($type) {
                // Si usas Postgres y quieres case-insensitive real, cambia a ILIKE:
                // $qq->whereRaw('title ILIKE ?', ['%'.$type.'%']);
                $qq->where('title', 'like', '%'.$type.'%');
            })
            ->orderByDesc('effective_from')
            ->first();
    }
}
