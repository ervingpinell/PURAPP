<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicySectionTranslation extends Model
{
    protected $table = 'policy_section_translations';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'section_id',
        'locale',   // es, en, fr, de, pt
        'name',
        'content',
    ];

    public function section()
    {
        return $this->belongsTo(PolicySection::class, 'section_id', 'section_id');
    }
}
