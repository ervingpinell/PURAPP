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
        'content',
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
        static::created(function (self $section) {
            $section->seedMissingTranslations();
        });

        static::updated(function (self $section) {
            if ($section->wasChanged('name')) {
                $section->syncNameIntoTranslations();
            }
        });
    }

    public function seedMissingTranslations(?array $locales = null): void
    {
        $locales = $locales ?: (array) config('app.supported_locales', ['es','en','fr','pt','de']);
        foreach ($locales as $loc) {
            $norm = Policy::canonicalLocale($loc);
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
        $requested = \App\Models\Policy::canonicalLocale($locale ?: app()->getLocale());
        $fallback  = config('app.fallback_locale', 'es');

        $bag = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        $norm = fn ($v) => str_replace('-', '_', strtolower((string) $v));

        if ($exact = $bag->first(fn ($t) => $norm($t->locale) === $norm($requested))) {
            return $exact;
        }

        foreach ([str_replace('_','-',$requested), substr($requested, 0, 2)] as $v) {
            if ($found = $bag->first(fn ($t) => $norm($t->locale) === $norm($v))) {
                return $found;
            }
        }

        return $bag->first(fn ($t) => $norm($t->locale) === $norm($fallback))
            ?: $bag->first(fn ($t) => $norm($t->locale) === $norm(substr($fallback, 0, 2)))
            ?: $bag->first();
    }


    public function translate(?string $locale = null): ?PolicySectionTranslation
    {
        return $this->translation($locale);
    }
}
