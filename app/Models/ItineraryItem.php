<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItineraryItem extends Model
{
    use HasFactory;

    protected $table = 'itinerary_items';
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'title',
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

    public function itineraries()
    {
        return $this->belongsToMany(
                Itinerary::class,
                'itinerary_item_itinerary',
                'itinerary_item_id',
                'itinerary_id'
            )
            ->withPivot('item_order', 'is_active')
            ->withTimestamps()
            ->orderBy('itinerary_item_itinerary.item_order');
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
}
