<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicySectionTranslation extends Model
{
    protected $table = 'policy_section_translations';
    public $timestamps = true;

    protected $fillable = [
        'section_id',
        'locale',
        'title',
        'content',
    ];

    public function section()
    {
        return $this->belongsTo(PolicySection::class, 'section_id', 'section_id');
    }
}
