<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;
use App\Models\Tour;              
use App\Models\TourLanguage;
use App\Models\TourSchedule;

class BookingDetail extends Model
{
    protected $table      = 'booking_details';
    protected $primaryKey = 'details_id';
    protected $casts = [
        'tour_date' => 'date',
    ];
    public    $timestamps = true; 
    protected $fillable = [
        'booking_id',
        'tour_id',                  
        'tour_schedule_id',
        'tour_date',
        'tour_language_id',
        'adults_quantity',
        'kids_quantity',
        'adult_price',
        'kid_price',
        'total',
        'hotel_id',
        'is_other_hotel',
        'other_hotel_name',
        // …
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function tourLanguage()
    {
        return $this->belongsTo(TourLanguage::class, 'tour_language_id', 'tour_language_id');
    }

    public function tourSchedule()
    {
        return $this->belongsTo(TourSchedule::class, 'tour_schedule_id', 'tour_schedule_id');
    }
    public function hotel()
    {
        return $this->belongsTo(HotelList::class, 'hotel_id', 'hotel_id');
    }
}
