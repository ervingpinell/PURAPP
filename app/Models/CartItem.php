<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Schedule;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'cart_id',
        'tour_id',
        'schedule_id',
        'tour_date',
        'tour_language_id',
        'hotel_id',
        'is_other_hotel',
        'other_hotel_name',
        'adults_quantity',
        'kids_quantity',
        'adult_price',
        'kid_price',
        'is_active',
        'meeting_point_id',
        'meeting_point_name',
        'meeting_point_pickup_time',
        'meeting_point_description',
        'meeting_point_map_url',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    public function hotel()
    {
        return $this->belongsTo(HotelList::class, 'hotel_id');
    }

    public function language()
    {
        return $this->belongsTo(TourLanguage::class, 'tour_language_id');
    }
    public function meetingPoint()
    {
        return $this->belongsTo(\App\Models\MeetingPoint::class, 'meeting_point_id');
    }
}
