<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryTranslation extends Model
{
    protected $table = 'itinerary_translations';
    public $timestamps = true;

    protected $fillable = [
        'itinerary_id',
        'locale',
        'name',
        'description',
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id', 'itinerary_id');
    }
}
