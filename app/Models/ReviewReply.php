<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    protected $table = 'review_replies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'review_id',
        'admin_user_id',
        'body',
        'public',          // <- coincide con la migración
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id', 'user_id');
    }
}
