<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Itinerary Model
 *
 * Represents a tour itinerary.
 */
class Itinerary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'itineraries';
    protected $primaryKey = 'itinerary_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'is_active',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'itinerary_id', 'itinerary_id');
    }

    public function items()
    {
        return $this->belongsToMany(
            ItineraryItem::class,
            'itinerary_item_itinerary',
            'itinerary_id',
            'itinerary_item_id'
        )
            ->withPivot('item_order', 'is_active')
            ->withTimestamps()
            ->wherePivot('is_active', true)
            ->where('itinerary_items.is_active', true)
            ->orderBy('itinerary_item_itinerary.item_order');
    }

    /**
     * All items without active constraints (for eager loading with translations)
     */
    public function allItems()
    {
        return $this->belongsToMany(
            ItineraryItem::class,
            'itinerary_item_itinerary',
            'itinerary_id',
            'itinerary_item_id'
        )
            ->withPivot('item_order', 'is_active')
            ->withTimestamps()
            ->orderBy('itinerary_item_itinerary.item_order');
    }

    public function translations()
    {
        return $this->hasMany(ItineraryTranslation::class, 'itinerary_id', 'itinerary_id');
    }

    /**
     * Obtiene la traducción para un locale específico con fallback robusto.
     *
     * Jerarquía de fallback:
     * 1. Locale solicitado (ej: 'en-US')
     * 2. Idioma base del locale (ej: 'en')
     * 3. Locale de fallback de la app (config)
     * 4. Idioma base del fallback
     * 5. Español ('es') como último recurso
     *
     * @param string|null $locale
     * @return ItineraryTranslation|null
     */
    public function translate(?string $locale = null): ?ItineraryTranslation
    {
        $locale = $locale ?? app()->getLocale();
        $locale = str_replace('_', '-', $locale);

        // Lista de candidatos en orden de prioridad
        $candidates = [$locale];

        // Si el locale tiene región (ej: en-US), agregar el idioma base (en)
        if (str_contains($locale, '-')) {
            $base = explode('-', $locale)[0];
            if (!in_array($base, $candidates, true)) {
                $candidates[] = $base;
            }
        }

        // Agregar fallback de la aplicación
        $appFallback = str_replace('_', '-', (string) config('app.fallback_locale', 'en'));
        if (!in_array($appFallback, $candidates, true)) {
            $candidates[] = $appFallback;
        }

        // Si el fallback tiene región, agregar su idioma base
        if (str_contains($appFallback, '-')) {
            $base = explode('-', $appFallback)[0];
            if (!in_array($base, $candidates, true)) {
                $candidates[] = $base;
            }
        }

        // Español como último recurso
        if (!in_array('es', $candidates, true)) {
            $candidates[] = 'es';
        }

        // Obtener todas las traducciones (usar relación cargada si existe)
        $translations = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        if ($translations->isEmpty()) {
            return null;
        }

        // Crear mapas para búsqueda eficiente
        $byExact = [];
        $byLang = [];

        foreach ($translations as $tr) {
            $loc = str_replace('_', '-', (string) $tr->locale);
            $byExact[$loc] = $tr;

            $lang = explode('-', $loc)[0];
            if (!isset($byLang[$lang])) {
                $byLang[$lang] = $tr;
            }
        }

        // Buscar en orden de prioridad
        foreach ($candidates as $cand) {
            // Primero intentar match exacto
            if (isset($byExact[$cand])) {
                return $byExact[$cand];
            }

            // Luego intentar por idioma base
            $lang = explode('-', $cand)[0];
            if (isset($byLang[$lang])) {
                return $byLang[$lang];
            }
        }

        return null;
    }

    /**
     * Obtiene una traducción específica por locale.
     *
     * @param string $locale
     * @return ItineraryTranslation|null
     */
    public function getTranslation(string $locale): ?ItineraryTranslation
    {
        return $this->translations()->where('locale', $locale)->first();
    }

    /* =====================
     * ACCESSORS MÁGICOS
     * Para compatibilidad con código existente que accede a $itinerary->name
     * ===================== */

    /**
     * Accessor mágico para 'name'.
     * Retorna el nombre traducido según el locale actual.
     */
    public function getNameAttribute(): string
    {
        return $this->translate()?->name ?? '';
    }

    /**
     * Accessor mágico para 'description'.
     * Retorna la descripción traducida según el locale actual.
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->translate()?->description;
    }

    /* =====================
     * ACCESSORS TRADUCIDOS (Mantener para compatibilidad)
     * ===================== */

    /**
     * Retorna el nombre traducido (usa el accessor mágico).
     */
    public function getNameTranslatedAttribute(): ?string
    {
        return $this->name;
    }

    /**
     * Retorna la descripción traducida (usa el accessor mágico).
     */
    public function getDescriptionTranslatedAttribute(): ?string
    {
        return $this->description;
    }

    /**
     * Scope para cargar traducciones de un locale específico.
     * Útil para optimizar queries.
     */
    public function scopeWithTranslation($query, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $query->with(['translations' => fn($q) => $q->where('locale', $locale)]);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }
}
