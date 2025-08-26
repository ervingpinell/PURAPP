<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyTranslation extends Model
{
    protected $table = 'policies_translations';
    public $timestamps = true;

    protected $fillable = [
        'policy_id',
        'locale',
        'name',
        'content',
    ];

    public function policy()
    {
        return $this->belongsTo(Policy::class, 'policy_id', 'policy_id');
    }
}
