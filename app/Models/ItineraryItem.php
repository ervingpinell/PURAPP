<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'tour_id',
        'title',
        'description',
        'order',
        'is_active',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
