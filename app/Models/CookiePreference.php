<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CookiePreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'essential',
        'functional',
        'analytics',
        'marketing',
        'accepted_at',
    ];

    protected $casts = [
        'essential' => 'boolean',
        'functional' => 'boolean',
        'analytics' => 'boolean',
        'marketing' => 'boolean',
        'accepted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the preference
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convert preferences to array for cookie storage
     */
    public function toPreferencesArray(): array
    {
        return [
            'essential' => $this->essential,
            'functional' => $this->functional,
            'analytics' => $this->analytics,
            'marketing' => $this->marketing,
        ];
    }

    /**
     * Check if a specific category is allowed
     */
    public function allows(string $category): bool
    {
        return $this->{$category} ?? false;
    }
}
