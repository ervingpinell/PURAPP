<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Schedule extends Model
{
    protected $table = 'schedules';


    protected $primaryKey = 'schedule_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public function getRouteKeyName()
    {
        return 'schedule_id';
    }

    protected $fillable = [
        'start_time',
        'end_time',
        'label',
        'max_capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'max_capacity' => 'integer',
    ];


    protected function startTime(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (is_string($value) && preg_match('/^\d{2}:\d{2}$/', $value)) {
                    return $value . ':00';
                }
                return $value;
            }
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (is_string($value) && preg_match('/^\d{2}:\d{2}$/', $value)) {
                    return $value . ':00';
                }
                return $value;
            }
        );
    }
    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'schedule_tour',
            'schedule_id',
            'tour_id'
        )->withPivot('is_active')
         ->withTimestamps();
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
