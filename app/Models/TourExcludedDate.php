<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourExcludedDate extends Model
{
    protected $primaryKey = 'tour_excluded_date_id'; 

    protected $fillable = [
        'tour_id',
        'schedule_id', 
        'start_date',
        'end_date',
        'reason',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function schedule()
    {
        return $this->belongsTo(\App\Models\Schedule::class, 'schedule_id', 'schedule_id');
    }
}
