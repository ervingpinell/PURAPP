<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Itinerary extends Model
{
    use HasFactory;

    protected $primaryKey = 'itinerary_id';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
            ->wherePivot('is_active', true)
            ->where('itinerary_items.is_active', true)
            ->orderBy('pivot_item_order');
    }

    // 🔁 Traducciones
public function translations()
{
    return $this->hasMany(\App\Models\ItineraryTranslation::class, 'itinerary_id');
}

public function translate($locale = null)
{
    $locale = $locale ?? app()->getLocale();
    return $this->translations->firstWhere('locale', $locale)
        ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
}

}
