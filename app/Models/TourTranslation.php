<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTranslation extends Model
{
    protected $fillable = ['tour_id', 'locale', 'name', 'overview'];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
