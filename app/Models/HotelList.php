<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * HotelList Model
 *
 * Represents a hotel for pickup/dropoff.
 */
class HotelList extends Model
{
    use HasFactory;

    protected $table = 'hotels_list';
    protected $primaryKey = 'hotel_id';

protected $fillable = ['name', 'is_active', 'sort_order'];


    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'hotel_id');
    }
}
