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
        // (si tienes schedule_id en bookings, inclúyelo también)
        'schedule_id',
        'notes',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔴 Importante: incluir tours archivados
    public function tour()
    {
        return $this->belongsTo(\App\Models\Tour::class, 'tour_id')->withTrashed();
    }

    public function tourLanguage()
    {
        return $this->belongsTo(\App\Models\TourLanguage::class, 'tour_language_id', 'tour_language_id');
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    // Alias legible
    public function getReferenceAttribute()
    {
        return $this->booking_reference;
    }

    public function details()
    {
        return $this->hasMany(\App\Models\BookingDetail::class, 'booking_id', 'booking_id');
    }

    public function detail()
    {
        return $this->hasOne(\App\Models\BookingDetail::class, 'booking_id', 'booking_id');
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Models\HotelList::class, 'hotel_id', 'hotel_id');
    }

    public function promoCode()
    {
        return $this->hasOne(\App\Models\PromoCode::class, 'used_by_booking_id', 'booking_id');
    }

    public function redemption()
    {
        return $this->hasOne(\App\Models\PromoCodeRedemption::class, 'booking_id', 'booking_id')->with('promoCode');
    }

    // accessor para mantener $booking->promoCode funcionando
    public function getPromoCodeAttribute()
    {
        return $this->redemption?->promoCode;
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'tour_id','tour_id')
                    ->whereColumn('user_id', 'bookings.user_id');
    }

    public function reviewRequests()
    {
        return $this->hasMany(\App\Models\ReviewRequest::class, 'booking_id','booking_id');
    }
}
