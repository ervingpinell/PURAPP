<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelList extends Model
{
    use HasFactory;

    protected $table = 'hotels_list';
    protected $primaryKey = 'hotel_id';

    protected $fillable = ['name', 'is_active'];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'hotel_id');
    }
}
