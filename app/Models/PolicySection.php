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
        'key',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    /* Relaciones */
    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id', 'policy_id');
    }

    public function translations()
    {
        return $this->hasMany(PolicySectionTranslation::class, 'section_id', 'section_id');
    }

    /* Helpers de traducción (igual estilo que Faq/Policy) */
    public function translation(?string $locale = null)
    {
        $locale   = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        $bag = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        return $bag->firstWhere('locale', $locale)
            ?? $bag->firstWhere('locale', $fallback)
            ?? $bag->first();
    }

    public function translate(?string $locale = null)
    {
        return $this->translation($locale);
    }
}
