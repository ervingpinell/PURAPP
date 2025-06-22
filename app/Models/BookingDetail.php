<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    protected $table      = 'booking_details';
    protected $primaryKey = 'details_id';
    public    $timestamps = true; 
    protected $fillable = [
      'booking_id','tour_id','tour_schedule_id','tour_date',
      'tour_language_id','adults_quantity','kids_quantity',
      'adult_price','kid_price','total','is_other_hotel','other_hotel_name',
      // …
    ];

    public function booking()
    {
      return $this->belongsTo(Booking::class,'booking_id','booking_id');
    }
    public function tourLanguage()
    {
        return $this->belongsTo(\App\Models\TourLanguage::class, 'tour_language_id', 'tour_language_id');
    }

    public function tourSchedule()
    {
        return $this->belongsTo(\App\Models\TourSchedule::class, 'tour_schedule_id', 'tour_schedule_id');
    }
}
