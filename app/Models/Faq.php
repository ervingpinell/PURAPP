<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'question',
        'answer',
        'translated_question',
        'translated_answer',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function translations()
    {
        return $this->hasMany(FaqTranslation::class, 'faq_id', 'faq_id');
    }

    public function translate(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
        }

        return $this->translations()
            ->where('locale', $locale)
            ->first()
            ?? $this->translations()
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }

    public function getQuestionTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->question;
    }

    public function getAnswerTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->answer;
    }
}
