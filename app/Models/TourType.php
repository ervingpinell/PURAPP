<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Tour;
use App\Models\TourTypeTranslation;

class TourType extends Model
{
    use HasFactory;

    protected $table = 'tour_types';
    protected $primaryKey = 'tour_type_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;


    protected $fillable = [
        'is_active',
        'cover_path',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    // Para exponer cover_url ya listo en el modelo
    protected $appends = ['cover_url'];

    public function getCoverUrlAttribute(): string
    {
        return $this->cover_path
            ? asset('storage/' . ltrim($this->cover_path, '/'))
            : asset('images/volcano.png');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /* =====================
     * RELACIONES
     * ===================== */

    public function tours()
    {
        return $this->hasMany(Tour::class, 'tour_type_id', 'tour_type_id');
    }

    public function translations()
    {
        return $this->hasMany(TourTypeTranslation::class, 'tour_type_id', 'tour_type_id');
    }

    public function orderedTours()
    {
        return $this->belongsToMany(
            Tour::class,
            'tour_type_tour_order',
            'tour_type_id',
            'tour_id'
        )->withPivot('position')
            ->orderBy('tour_type_tour_order.position', 'asc');
    }

    /* =====================
     * MÉTODOS DE TRADUCCIÓN
     * ===================== */

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
     * @return TourTypeTranslation|null
     */
    public function translate(?string $locale = null): ?TourTypeTranslation
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
     * @return TourTypeTranslation|null
     */
    public function getTranslation(string $locale): ?TourTypeTranslation
    {
        return $this->translations()->where('locale', $locale)->first();
    }

    /* =====================
     * ACCESSORS MÁGICOS
     * Para compatibilidad con código existente que accede a $tourType->name
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

    /**
     * Accessor mágico para 'duration'.
     * Retorna la duración traducida según el locale actual.
     */
    public function getDurationAttribute(): ?string
    {
        return $this->translate()?->duration;
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
     * Retorna la duración traducida (usa el accessor mágico).
     */
    public function getDurationTranslatedAttribute(): ?string
    {
        return $this->duration;
    }

    /* =====================
     * OTROS MÉTODOS
     * ===================== */

    public function getRouteKeyName()
    {
        return 'tour_type_id';
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
}
