<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTranslation extends Model
{
    protected $table = 'tour_translations';
    public $timestamps = true;

    protected $fillable = [
        'tour_id',
        'locale',
        'name',
        'overview',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
