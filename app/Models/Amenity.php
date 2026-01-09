<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Amenity Model
 *
 * Represents a tour amenity/feature (WiFi, AC, etc.).
 */
class Amenity extends Model
{
    use HasFactory;

    protected $table = 'amenities';
    protected $primaryKey = 'amenity_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['is_active'];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'amenity_tour',
            'amenity_id',
            'tour_id'
        )
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function excludedFromTours()
    {
        return $this->belongsToMany(
            Tour::class,
            'excluded_amenity_tour',
            'amenity_id',
            'tour_id'
        )
            ->withPivot('is_active')
            ->withTimestamps();
    }


    public function translations()
    {
        return $this->hasMany(AmenityTranslation::class, 'amenity_id', 'amenity_id');
    }

    /**
     * Get translation for a specific locale with robust fallback.
     *
     * Fallback hierarchy:
     * 1. Requested locale (e.g., 'en-US')
     * 2. Base locale (e.g., 'en' from 'en-US')
     * 3. App fallback locale (config('app.fallback_locale'))
     * 4. Spanish ('es')
     * 5. First available translation
     * 6. null
     */
    public function translate(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'es');

        // If translations are already loaded, search in memory
        if ($this->relationLoaded('translations')) {
            $translations = $this->translations;

            // Try exact locale match
            $translation = $translations->firstWhere('locale', $locale);
            if ($translation) {
                return $translation;
            }

            // Try base locale (e.g., 'en' from 'en-US')
            if (str_contains($locale, '-')) {
                $baseLocale = explode('-', $locale)[0];
                $translation = $translations->firstWhere('locale', $baseLocale);
                if ($translation) {
                    return $translation;
                }
            }

            // Try fallback locale
            if ($locale !== $fallbackLocale) {
                $translation = $translations->firstWhere('locale', $fallbackLocale);
                if ($translation) {
                    return $translation;
                }
            }

            // Try Spanish as last resort
            if ($fallbackLocale !== 'es') {
                $translation = $translations->firstWhere('locale', 'es');
                if ($translation) {
                    return $translation;
                }
            }

            // Return first available translation
            return $translations->first();
        }

        // Query database with fallback chain
        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', $fallbackLocale)->first()
            ?? $this->translations()->where('locale', 'es')->first()
            ?? $this->translations()->first();
    }

    /**
     * Scope to eager load translations for current locale
     */
    public function scopeWithTranslation($query, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $query->with(['translations' => function ($q) use ($locale) {
            $q->where('locale', $locale)
                ->orWhere('locale', config('app.fallback_locale', 'es'))
                ->orWhere('locale', 'es');
        }]);
    }

    /**
     * Magic accessor for name - provides backward compatibility
     */
    public function getNameAttribute(): ?string
    {
        return optional($this->translate())->name;
    }

    /**
     * Compatibility accessor for translated name
     */
    public function getNameTranslatedAttribute(): ?string
    {
        return $this->name;
    }
}
