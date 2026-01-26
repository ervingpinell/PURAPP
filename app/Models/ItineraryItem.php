<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

/**
 * ItineraryItem Model
 *
 * Represents a single item/stop in an itinerary.
 */
class ItineraryItem extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes, HasTranslations;

    protected $table = 'itinerary_items';
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
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

    public function getTitleTranslatedAttribute(): ?string
    {
        return $this->title;
    }

    public function getDescriptionTranslatedAttribute(): ?string
    {
        return $this->description;
    }

    public function deletedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by', 'user_id');
    }
}
