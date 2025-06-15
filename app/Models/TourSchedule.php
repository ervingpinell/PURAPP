<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourSchedule extends Model
{
    protected $table = 'tour_schedules';
    protected $primaryKey = 'tour_schedule_id';
     protected $fillable = [
        'tour_id',
        'start_time',
        'label',
        'is_active',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
