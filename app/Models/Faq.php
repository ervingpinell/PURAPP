<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * Faq Model
 *
 * Represents a frequently asked question.
 */
class Faq extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public $translatable = ['question', 'answer'];

    protected $fillable = [
        'question',
        'answer',
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
    
    // Removed hasMany translations

    public function getQuestionTranslatedAttribute(): ?string
    {
        return $this->question;
    }

    public function getAnswerTranslatedAttribute(): ?string
    {
        return $this->answer;
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
