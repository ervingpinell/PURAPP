<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingPoint extends Model
{
    protected $table = 'meeting_points';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'pickup_time',
        'address',
        'map_url',
        'sort_order',
        'is_active',
    ];

    // Scope útil
    public function scopeActive($q) { return $q->where('is_active', true); }
}
