<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    protected $fillable = [
        'template_key',
        'name',
        'description',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all content translations for this template.
     */
    public function contents(): HasMany
    {
        return $this->hasMany(EmailTemplateContent::class);
    }

    /**
     * Get content for a specific locale.
     */
    public function getContentForLocale(string $locale): ?EmailTemplateContent
    {
        return $this->contents()->where('locale', $locale)->first();
    }

    /**
     * Get content for locale with fallback to English.
     */
    public function getContentWithFallback(string $locale): ?EmailTemplateContent
    {
        $content = $this->getContentForLocale($locale);

        if (!$content && $locale !== 'en') {
            $content = $this->getContentForLocale('en');
        }

        return $content;
    }

    /**
     * Scope to filter active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all available template keys.
     */
    public static function getAllTemplateKeys(): array
    {
        return [
            // Customer emails
            'booking_created_customer',
            'booking_confirmed',
            'booking_updated',
            'booking_cancelled',
            'booking_expired',
            'payment_success',
            'payment_failed',
            'payment_reminder',
            'password_setup',
            'welcome',
            'review_request',
            'review_reply',

            // Admin emails
            'booking_created_admin',
            'paid_booking_admin',
            'booking_expiring_admin',
            'daily_operations_report',
            'review_submitted_admin',

            // Other
            'contact_message',
            'test_email',
        ];
    }

    /**
     * Get template by key.
     */
    public static function findByKey(string $key): ?self
    {
        return static::where('template_key', $key)->first();
    }
}
