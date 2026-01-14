<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * MeetingPoint Model
 *
 * Represents a tour meeting point location.
 */
class MeetingPoint extends Model
{
    use SoftDeletes;

    protected $table = 'meeting_points';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pickup_time',
        'map_url',
        'sort_order',
        'is_active',
        'deleted_by',
    ];

    protected $casts = [
        'is_active'  => 'bool',
        'sort_order' => 'int',
    ];

    /* ----------------------------------------
     | Scopes
     |-----------------------------------------*/
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        // Primero los que tienen sort_order definido, luego por nombre
        return $q->orderByRaw('sort_order IS NULL, sort_order ASC');
    }

    /**
     * Scope para items eliminados hace más de X días
     */
    public function scopeOlderThan($q, int $days = 30)
    {
        return $q->onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days));
    }

    /* ----------------------------------------
     | Relaciones
     |-----------------------------------------*/
    public function translations(): EloquentHasMany
    {
        // FK por convención: meeting_point_id
        return $this->hasMany(MeetingPointTranslation::class, 'meeting_point_id');
    }

    /**
     * Usuario que eliminó este meeting point
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /* ----------------------------------------
     | Helpers de localización
     |-----------------------------------------*/

    /**
     * Devuelve el valor traducido de un campo ('name' o 'description')
     * para el locale dado (o el actual), con fallback al base.
     */
    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $short  = $this->shortLocale($locale);

        // Si ya viene eager-loaded, usamos la colección para evitar N+1.
        $t = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', $short)
            : $this->translations()->where('locale', $short)->first();

        if ($t) {
            return $t->{$field};
        }

        // Si no existe traducción, intentamos fallback al locale por defecto (es)
        $fallback = config('app.fallback_locale', 'es');
        if ($short !== $fallback) {
            $tFallback = $this->relationLoaded('translations')
                ? $this->translations->firstWhere('locale', $fallback)
                : $this->translations()->where('locale', $fallback)->first();

            if ($tFallback) {
                return $tFallback->{$field};
            }
        }

        return $this->{$field} ?? null;
    }

    /**
     * Accessor: $meetingPoint->name_localized
     */
    public function getNameLocalizedAttribute(): string
    {
        return (string) $this->getTranslated('name');
    }

    /**
     * Accessor: $meetingPoint->description_localized
     */
    public function getDescriptionLocalizedAttribute(): ?string
    {
        return $this->getTranslated('description');
    }

    /**
     * Accessor: $meetingPoint->instructions_localized
     */
    public function getInstructionsLocalizedAttribute(): ?string
    {
        return $this->getTranslated('instructions');
    }

    /* ----------------------------------------
     | Utilidades privadas
     |-----------------------------------------*/

    /**
     * Normaliza códigos de locale a la forma corta usada en translations.
     * Intenta usar tu DeepLTranslator si existe; si no, hace un fallback simple.
     */
    private function shortLocale(string $locale): string
    {
        // Si tu servicio existe, úsalo
        if (
            class_exists(\App\Services\DeepLTranslator::class)
            && method_exists(\App\Services\DeepLTranslator::class, 'normalizeLocaleCode')
        ) {
            return \App\Services\DeepLTranslator::normalizeLocaleCode($locale);
        }

        // Fallback básico: es-CR -> es, pt-BR -> pt, en-US -> en, etc.
        $locale = str_replace('_', '-', strtolower($locale));
        $short  = explode('-', $locale)[0] ?? $locale;

        // Aseguramos que sea uno de los que usas
        return in_array($short, ['es', 'en', 'fr', 'pt', 'de'], true) ? $short : config('app.fallback_locale', 'es');
    }
}
