<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryItemTranslation extends Model
{
    protected $table = 'itinerary_item_translations';

    protected $fillable = [
        'item_id',
        'locale',
        'title',
        'description',
    ];

    public function item()
    {
        return $this->belongsTo(ItineraryItem::class, 'item_id');
    }
}
