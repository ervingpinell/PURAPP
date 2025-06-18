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
    ];

    public function tours()
    {
        return $this->hasMany(Tour::class, 'itinerary_id', 'itinerary_id');
    }
public function items()
{
    return $this->belongsToMany(ItineraryItem::class, 'itinerary_item_itinerary', 'itinerary_id', 'itinerary_item_id')
                ->withPivot('item_order', 'is_active')
                ->orderBy('pivot_item_order'); // ← aquí el cambio importante
}

}
