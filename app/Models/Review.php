<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Review Model
 *
 * Represents a customer review for a tour.
 */
class Review extends Model
{
    protected $fillable = [
        'tour_id','booking_id','user_id',
        'provider','provider_review_id',
        'rating','title','body','language',
        'author_name','author_country',
        'is_verified','is_public','status',
        'source_url',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_public'   => 'boolean',
    ];

    public function tour()    { return $this->belongsTo(\App\Models\Tour::class); }

public function booking()
{
    return $this->belongsTo(\App\Models\Booking::class, 'booking_id', 'booking_id');
}
    public function user()    { return $this->belongsTo(\App\Models\User::class); }
    public function replies() { return $this->hasMany(ReviewReply::class); }
}
