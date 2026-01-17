<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Faq Model
 *
 * Represents a frequently asked question.
 */
class Faq extends Model
{
    use HasFactory, SoftDeletes;

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
        'sort_order',
        'deleted_by',
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

    public function deletedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by', 'user_id');
    }

    public function scopeOlderThan($query, $days)
    {
        return $query->where('deleted_at', '<=', now()->subDays($days));
    }
}
