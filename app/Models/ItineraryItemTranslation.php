<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ItineraryItemTranslation Model
 *
 * Stores translated itinerary item content.
 */
class ItineraryItemTranslation extends Model
{
    protected $table = 'itinerary_item_translations';
    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'locale',
        'title',
        'description',
    ];

    public function item()
    {
        return $this->belongsTo(ItineraryItem::class, 'item_id', 'item_id');
    }
}
