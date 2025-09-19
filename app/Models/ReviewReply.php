<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    protected $fillable = ['review_id','admin_user_id','body','public'];

    protected $casts = ['public' => 'boolean'];

    public function review() { return $this->belongsTo(Review::class); }
    public function admin()  { return $this->belongsTo(\App\Models\User::class,'admin_user_id'); }
}
