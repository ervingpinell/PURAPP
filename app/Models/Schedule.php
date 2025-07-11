<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules'; 
    protected $primaryKey = 'schedule_id'; 

    protected $fillable = [
        'start_time',
        'end_time',
        'label',
        'is_active',
    ];

    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'schedule_tour', //PIVOT
            'schedule_id',
            'tour_id'
        );
    }
}

