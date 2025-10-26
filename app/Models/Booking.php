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
        'schedule_id',
        'notes',
    ];

    // ---------------- Relaciones ----------------
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Incluir tours archivados
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id')->withTrashed();
    }

    public function tourLanguage()
    {
        return $this->belongsTo(TourLanguage::class, 'tour_language_id', 'tour_language_id');
    }

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

    /**
     * Redención real (pivot) con el código cargado.
     * Carga encadenada del modelo PromoCode.
     */
    public function redemption()
    {
        return $this->hasOne(PromoCodeRedemption::class, 'booking_id', 'booking_id')
                    ->with('promoCode');
    }

    /**
     * Compatibilidad: $booking->promoCode devuelve el modelo del cupón
     * (desde pivot) o, si lo usabas antes, por columna legacy.
     */
    public function getPromoCodeAttribute()
    {
        return $this->redemption?->promoCode;
    }

    // (opcional) legacy si aún existe la columna used_by_booking_id en promo_codes
    public function promoCodeLegacy()
    {
        return $this->hasOne(PromoCode::class, 'used_by_booking_id', 'booking_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tour_id','tour_id')
                    ->whereColumn('user_id', 'bookings.user_id');
    }

    public function reviewRequests()
    {
        return $this->hasMany(ReviewRequest::class, 'booking_id','booking_id');
    }

    // ---------------- Mutators/Accesors ----------------
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    public function getReferenceAttribute()
    {
        return $this->booking_reference;
    }
}
