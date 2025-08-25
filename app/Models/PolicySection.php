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
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'bool',
        'sort_order' => 'int',
    ];

    /* -------------------- BOOT & AUTO-TRADUCCIONES -------------------- */

    protected static function booted()
    {
        // Sembrar traducciones para todos los locales soportados al crear
        static::created(function (self $section) {
            $section->seedMissingTranslations();
        });

        // Si cambia el nombre base, actualizar traducciones vacías
        static::updated(function (self $section) {
            if ($section->wasChanged('name')) {
                $section->syncNameIntoTranslations();
            }
        });
    }

    /**
     * Crea traducciones faltantes para los locales soportados.
     * Rellena name con el base y content vacío.
     */
    public function seedMissingTranslations(?array $locales = null): void
    {
        $locales = $locales ?: config('app.supported_locales', ['es','en','fr','pt','de']);
        foreach ($locales as $loc) {
            $norm = Policy::canonicalLocale($loc);
            $this->translations()->firstOrCreate(
                ['locale' => $norm],
                ['name' => (string) $this->name, 'content' => '']
            );
        }
    }

    /**
     * Copia el nombre base a las traducciones cuyo "name" esté vacío.
     * No sobrescribe traducciones ya personalizadas.
     */
    public function syncNameIntoTranslations(): void
    {
        $current = (string) ($this->name ?? '');
        $this->translations()->get()->each(function (PolicySectionTranslation $tr) use ($current) {
            if (blank($tr->name)) {
                $tr->name = $current;
                $tr->save();
            }
        });
    }

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

    /* -------------------- HELPERS DE TRADUCCIÓN -------------------- */

    public function translation(?string $locale = null)
    {
        $locale   = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        $bag = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();

        $found = $bag->firstWhere('locale', $locale)
            ?: $bag->firstWhere('locale', substr($locale, 0, 2));

        if ($found) return $found;

        return $bag->firstWhere('locale', $fallback)
            ?: $bag->firstWhere('locale', substr($fallback, 0, 2))
            ?: $bag->first();
    }

    public function translate(?string $locale = null)
    {
        return $this->translation($locale);
    }
}
