<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourAvailability extends Model
{
    use HasFactory;

    protected $table = 'tour_availability';
    protected $primaryKey = 'availability_id';

    protected $fillable = [
        'tour_id',
        'date',
        'start_time',
        'end_time',
        'available',
        'is_active',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
