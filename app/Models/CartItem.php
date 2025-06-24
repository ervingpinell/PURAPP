<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'cart_id',
        'tour_id',
        'tour_schedule_id',
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
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function tourSchedule()
    {
        return $this->belongsTo(TourSchedule::class, 'tour_schedule_id');
    }

public function hotel()
{
    return $this->belongsTo(HotelList::class, 'hotel_id');
}

    public function language()
    {
        return $this->belongsTo(TourLanguage::class, 'tour_language_id');
    }
}
