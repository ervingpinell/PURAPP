<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Tour;
use App\Models\ItineraryItem;

class Itinerary extends Model
{
    use HasFactory;

    protected $primaryKey = 'itinerary_id';

    /**
     * Campos que pueden asignarse masivamente.
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Casts para que is_active se maneje como booleano.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Tours que usan este itinerario.
     */
    public function tours()
    {
        return $this->hasMany(Tour::class, 'itinerary_id', 'itinerary_id');
    }

    /**
     * Ítems activos de este itinerario, ordenados por pivot.item_order.
     */
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
}
