<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    protected $table = 'faq_translations';

    // ✅ Si tienes una columna 'id', no necesitas tocar $primaryKey ni $incrementing
    protected $fillable = [
        'faq_id',
        'locale',
        'question',
        'answer',
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class, 'faq_id');
    }
}
