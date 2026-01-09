<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FaqTranslation Model
 *
 * Stores translated FAQ content.
 */
class FaqTranslation extends Model
{
    protected $table = 'faq_translations';
    public $timestamps = true;

    protected $fillable = [
        'faq_id',
        'locale',
        'question',
        'answer',
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class, 'faq_id', 'faq_id');
    }
}
