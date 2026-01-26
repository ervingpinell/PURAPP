<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPickupZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'zone_name',
        'price_modifier',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
