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

    // public $translatedAttributes = ['name', 'description']; // Removed

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

    public function translate(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
        }

        return $this->translations()
            ->where('locale', $locale)
            ->first()
            ?? $this->translations()
            ->where('locale', config('app.fallback_locale'))
            ->first();
    }

    public function getNameTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->name;
    }

    public function getDescriptionTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->description;
    }

    public function deletedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by', 'user_id');
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
     * Scope para cargar traducciones de un locale específico.
     * Útil para optimizar queries.
     */
    public function scopeWithTranslation($query, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $query->with(['translations' => fn($q) => $q->where('locale', $locale)]);
    }


}
