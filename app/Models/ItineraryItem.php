<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ItineraryItem Model
 *
 * Represents a single item/stop in an itinerary.
 */
class ItineraryItem extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    // public $translatedAttributes = ['title', 'description']; // Removed


    protected $table = 'itinerary_items';
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function itineraries()
    {
        return $this->belongsToMany(
            Itinerary::class,
            'itinerary_item_itinerary',
            'itinerary_item_id',
            'itinerary_id'
        )
            ->withPivot('item_order', 'is_active')
            ->where('itineraries.is_active', true)
            ->wherePivot('is_active', true)
            ->withTimestamps()
            ->orderBy('itinerary_item_itinerary.item_order');
    }

    public function allItineraries()
    {
        return $this->belongsToMany(
            Itinerary::class,
            'itinerary_item_itinerary',
            'itinerary_item_id',
            'itinerary_id'
        )
            ->withPivot('item_order', 'is_active')
            ->withTimestamps()
            ->orderBy('itinerary_item_itinerary.item_order')
            ->withTrashed();
    }

    // Translations
    public function translations()
    {
        return $this->hasMany(ItineraryItemTranslation::class, 'item_id', 'item_id');
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

    public function getTitleTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->title;
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
     * @return ItineraryItemTranslation|null
     */
    public function getTranslation(string $locale): ?ItineraryItemTranslation
    {
        return $this->translations()->where('locale', $locale)->first();
    }

    /* =====================
     * ACCESSORS MÁGICOS
     * Para compatibilidad con código existente que accede a $item->title
     * ===================== */

    /**
     * Accessor mágico para 'title'.
     * Retorna el título traducido según el locale actual.
     */
    public function getTitleAttribute(): string
    {
        return $this->translate()?->title ?? '';
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
