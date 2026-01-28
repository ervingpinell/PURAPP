<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteMedia extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'site_media';
    
    protected $fillable = [
        'type',
        'title',
        'description',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // ==========================================
    // MEDIA LIBRARY
    // ==========================================

    public function registerMediaCollections(): void
    {
        // Hero video (landing page background)
        $this->addMediaCollection('hero_video')
             ->singleFile() // Only one video allowed
             ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/quicktime'])
             ->useFallbackUrl(asset('images/hero-placeholder.jpg'));
        
        // Welcome animation (first visit popup/modal)
        $this->addMediaCollection('welcome_animation')
             ->singleFile()
             ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/quicktime'])
             ->useFallbackUrl(asset('images/welcome-placeholder.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Poster/thumbnail for video
        $this->addMediaConversion('poster')
             ->width(1920)
             ->height(1080)
             ->format('jpg')
             ->quality(90)
             ->performOnCollections('hero_video', 'welcome_animation')
             ->nonQueued();
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    /**
     * Get hero video instance
     */
    public static function heroVideo()
    {
        return static::active()->byType('hero_video')->first();
    }

    /**
     * Get welcome animation instance
     */
    public static function welcomeAnimation()
    {
        return static::active()->byType('welcome_animation')->first();
    }
}
