<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ReviewModerationLog Model
 *
 * Tracks review moderation actions.
 */
class ReviewModerationLog extends Model
{
    protected $fillable = ['review_id','admin_user_id','action','meta'];

    protected $casts = ['meta' => 'array'];

    public function review() { return $this->belongsTo(Review::class); }
    public function admin()  { return $this->belongsTo(\App\Models\User::class,'admin_user_id'); }
}
