<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use HasFactory;

    protected $table = 'policies';
    protected $primaryKey = 'policy_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'type',
        'name',
        'is_default',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'is_default'    => 'bool',
        'is_active'     => 'bool',
        'effective_from'=> 'date',
        'effective_to'  => 'date',
    ];

    // Para route model binding en /policies/{policy}
    public function getRouteKeyName()
    {
        return 'policy_id';
    }

    /* ---------- Scopes ---------- */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeType($q, string $type)
    {
        return $q->where('type', $type);
    }

    // Helper para obtener la activa por tipo, priorizando la default
    public static function byType(string $type): ?self
    {
        return static::active()
            ->type($type)
            ->orderByDesc('is_default')
            ->first();
    }

    /* ---------- Relaciones ---------- */

    public function translations()
    {
        // owner key explícito por PK no estándar
        return $this->hasMany(PolicyTranslation::class, 'policy_id', 'policy_id');
    }

    /* ---------- Helpers de traducción (con fallback) ---------- */

    // Igual que usas en blades: $policy->translation()
    public function translation(?string $locale = null)
    {
        $locale   = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        $bag = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        return $bag->firstWhere('locale', $locale)
            ?? $bag->firstWhere('locale', $fallback)
            ?? $bag->first(); // último recurso (por si solo hay una)
    }

    // Alias para parecerse al Faq: $policy->translate()
    public function translate(?string $locale = null)
    {
        return $this->translation($locale);
    }

    /* ---------- Accessors opcionales ---------- */

    public function getTitleTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->title;
    }

    public function getContentTranslatedAttribute(): ?string
    {
        return optional($this->translation())?->content;
    }
}
