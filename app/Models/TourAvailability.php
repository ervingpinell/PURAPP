<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourAvailability extends Model
{
    use HasFactory;

    protected $table = 'tour_availability'; // Confirmar que la tabla se llama así
    protected $primaryKey = 'availability_id';

    // Si la tabla no tiene created_at y updated_at, desactiva timestamps:
    // public $timestamps = false;

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
