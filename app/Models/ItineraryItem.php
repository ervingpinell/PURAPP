<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'itinerary_id',
        'title',
        'description',
        'is_active',
    ];

    public function itineraries()
    {
        return $this->belongsToMany(Itinerary::class, 'itinerary_item_itinerary', 'itinerary_item_id', 'itinerary_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    // 🔁 Traducciones
public function translations()
{
    return $this->hasMany(\App\Models\ItineraryItemTranslation::class, 'item_id');
}

public function translate($locale = null)
{
    $locale = $locale ?? app()->getLocale();
    return $this->translations->firstWhere('locale', $locale)
        ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
}

}
