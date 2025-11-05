<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    protected $table = 'booking_details';
    protected $primaryKey = 'details_id';
    public $timestamps = true;

    protected $casts = [
        'tour_date'      => 'date',
        'categories'     => 'array',
        'is_other_hotel' => 'boolean',
    'created_at'   => 'datetime',
    'updated_at'   => 'datetime',
    ];

    protected $fillable = [
        'booking_id',
        'tour_id',
        'schedule_id',
        'tour_date',
        'tour_language_id',
        'categories',   // JSON: [ {category_id, category_name, category_slug, quantity, price}, ... ]
        'total',
        'hotel_id',
        'is_other_hotel',
        'other_hotel_name',
        'meeting_point_id',
        'meeting_point_name',
        'meeting_point_pickup_time',
        'meeting_point_description',
        'meeting_point_map_url',
    ];

    /* =======================
       Relaciones
       ======================= */
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

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    public function hotel()
    {
        return $this->belongsTo(HotelList::class, 'hotel_id', 'hotel_id');
    }

    public function meetingPoint()
    {
        return $this->belongsTo(\App\Models\MeetingPoint::class, 'meeting_point_id');
    }

    /* =======================
       Atributos calculados
       ======================= */

    /** Personas totales (todas las categorías) */
    public function getTotalPaxAttribute(): int
    {
        return collect($this->categories ?? [])->sum(fn($c) => (int)($c['quantity'] ?? 0));
    }

    /** Subtotal calculado desde categories */
    public function getSubtotalAttribute(): float
    {
        $total = collect($this->categories ?? [])->sum(function ($c) {
            $q = (int)($c['quantity'] ?? 0);
            $p = (float)($c['price'] ?? 0);
            return $q * $p;
        });
        return round((float)$total, 2);
    }

    /** Compat: adultos por slug=adult (si existe) */
    public function getAdultsQuantityAttribute(): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_slug', 'adult');
        return (int)($cat['quantity'] ?? 0);
    }

    /** Compat: niños por slug=kid (si existe) */
    public function getKidsQuantityAttribute(): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_slug', 'kid');
        return (int)($cat['quantity'] ?? 0);
    }

    /** Cantidad por category_id específico */
    public function getQuantityForCategory(int $categoryId): int
    {
        $cat = collect($this->categories ?? [])->firstWhere('category_id', $categoryId);
        return (int)($cat['quantity'] ?? 0);
    }


}
