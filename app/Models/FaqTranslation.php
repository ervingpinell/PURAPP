<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        // owner key explícito por PK no estándar
        return $this->belongsTo(Faq::class, 'faq_id', 'faq_id');
    }
}
