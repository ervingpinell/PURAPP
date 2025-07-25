<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'translated_question',
        'translated_answer',
        'is_active',
    ];

    public function translations()
    {
        return $this->hasMany(FaqTranslation::class, 'faq_id');
    }

    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
    }
}
