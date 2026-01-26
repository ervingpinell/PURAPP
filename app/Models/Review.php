<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Review Model
 *
 * Represents a customer review for a product.
 */
class Review extends Model
{
    protected $fillable = [
        'product_id', // Renamed from product_id
        'booking_id',
        'user_id',
        'provider',
        'provider_review_id',
        'rating',
        'title',
        'body',
        'language',
        'author_name',
        'author_email',
        'manual_booking_ref',
        'author_country',
        'is_verified',
        'is_public',
        'status',
        'source_url',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_public'   => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class, 'booking_id', 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function replies()
    {
        return $this->hasMany(ReviewReply::class);
    }
}
