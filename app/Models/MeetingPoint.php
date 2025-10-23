<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;

class MeetingPoint extends Model
{
    protected $table = 'meeting_points';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'pickup_time',
        'description',
        'map_url',
        'sort_order',
        'is_active',
    ];

    // Scope útil
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // Relación con traducciones (type-hint correcto)
    public function translations(): EloquentHasMany
    {
        return $this->hasMany(MeetingPointTranslation::class);
    }

    // Helper para obtener un campo traducido con fallback al base
    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $short  = \App\Services\DeepLTranslator::normalizeLocaleCode($locale);

        // Si ya está eager-loaded, usa la colección; si no, consulta puntual
        $t = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', $short)
            : $this->translations()->where('locale', $short)->first();

        return $t?->{$field} ?? $this->{$field};
    }
}
