<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * Itinerary Model
 *
 * Represents a product itinerary.
 */
class Itinerary extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'itineraries';
    protected $primaryKey = 'itinerary_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
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

    public function getNameTranslatedAttribute(): ?string
    {
        return $this->name;
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
