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
        'tour_id',
        'tour_language_id',
        'booking_reference',
        'booking_date',
        'status',
        'total',
        'hotel_id',
        'is_active',
    ];


    /** Relación con el usuario */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Relación con el tour */
    public function tour()
    {
        return $this->belongsTo(\App\Models\Tour::class, 'tour_id');
    }

    /** Al asignar status, lo guardamos en minúsculas */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }
    //relacion a bookingDetails
    public function details()
    {
        return $this->hasMany(BookingDetail::class, 'booking_id', 'booking_id');
    }

    public function detail()
    {
        return $this->hasOne(BookingDetail::class, 'booking_id', 'booking_id');
    }

    public function hotel()
    {
        return $this->belongsTo(HotelList::class, 'hotel_id', 'hotel_id');
    }
}
