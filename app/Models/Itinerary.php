<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Itinerary extends Model
{
    use HasFactory;

    protected $table = 'itineraries';
    protected $primaryKey = 'itinerary_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'is_active',
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

    // Translations
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
}
