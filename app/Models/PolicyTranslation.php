<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyTranslation extends Model
{
    protected $table = 'policy_translations';
    public $timestamps = true;

    protected $fillable = [
        'policy_id',
        'locale',
        'title',
        'content',
    ];

    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id', 'policy_id');
    }
}
