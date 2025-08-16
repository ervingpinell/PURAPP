<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'start_time',
        'end_time',
        'label',
        'max_capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_capacity' => 'integer',
    ];

    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'schedule_tour',
            'schedule_id',
            'tour_id'
        )
        ->withPivot('is_active')
        ->withTimestamps();
    }
}
