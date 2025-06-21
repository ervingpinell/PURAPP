<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'booking_reference',
        'booking_date',
        'status',
        'total',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

