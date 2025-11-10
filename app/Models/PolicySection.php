<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicySection extends Model
{
    use HasFactory;

    protected $table = 'policy_sections';
    protected $primaryKey = 'section_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'policy_id',
        // OJO: sin 'name' ni 'content' en la base
        'sort_order',
        'is_active',
        // si tienes otros (p.ej. 'type'), agrégalos aquí
    ];

    protected $casts = [
        'is_active'  => 'bool',
        'sort_order' => 'int',
    ];

    /* -------------------- RELACIONES -------------------- */

    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id', 'policy_id');
    }

    public function translations()
    {
        return $this->hasMany(PolicySectionTranslation::class, 'section_id', 'section_id');
    }

    /* -------------------- SCOPES -------------------- */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /* -------------------- TRADUCCIÓN -------------------- */

    public function translation(?string $locale = null): ?PolicySectionTranslation
    {
        $requested = Policy::canonicalLocale($locale ?: app()->getLocale());
        $fallback  = Policy::canonicalLocale((string) config('app.fallback_locale', 'es'));

        $bag = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        $norm = fn ($v) => str_replace('-', '_', strtolower((string) $v));

        // Coincidencia exacta
        if ($exact = $bag->first(fn ($t) => $norm($t->locale) === $norm($requested))) {
            return $exact;
        }

        // Variantes habituales (compat)
        foreach ([$requested, str_replace('_','-',$requested), substr($requested, 0, 2), 'pt_BR', 'pt-br'] as $v) {
            if ($found = $bag->first(fn ($t) => $norm($t->locale) === $norm($v))) {
                return $found;
            }
        }

        // Fallback → 'es' o primer registro
        return $bag->first(fn ($t) => $norm($t->locale) === $norm($fallback))
            ?: $bag->first(fn ($t) => $norm($t->locale) === $norm(substr($fallback, 0, 2)))
            ?: $bag->first();
    }

    public function translate(?string $locale = null): ?PolicySectionTranslation
    {
        return $this->translation($locale);
    }

    /* -------------------- ACCESSORS DE COMODIDAD -------------------- */

    public function getDisplayNameAttribute(): string
    {
        return (string) (optional($this->translation())?->name ?? '');
    }

    public function getDisplayContentAttribute(): string
    {
        return (string) (optional($this->translation())?->content ?? '');
    }
}
