<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasTranslations, InteractsWithMedia;

    protected $table = 'product2';
    protected $primaryKey = 'product_id';
    
    public $translatable = ['name', 'description', 'overview', 'recommendations'];

    protected $fillable = [
        'product_type_id',
        'product_category',
        'name',
        'description',
        'overview',
        'recommendations',
        'length',
        'max_capacity',
        'is_active',
        'color',
        'slug',
        'itinerary_id',
        'cutoff_lead',
        'allow_custom_time',
        'allow_custom_pickup',
        'requires_vehicle_assignment',
        'custom_fields_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_custom_time' => 'boolean',
        'allow_custom_pickup' => 'boolean',
        'requires_vehicle_assignment' => 'boolean',
        'custom_fields_config' => 'array',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['cover_url', 'display_category'];




    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id', 'product_type_id');
    }
    
    public function type()
    {
        return $this->productType();
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id', 'itinerary_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')
                    ->orderBy('position');
    }

    /**
     * Get the cover image relationship
     */
    public function coverImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
                    ->where('is_cover', true);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'product_id', 'product_id');
    }

    public function availability()
    {
        return $this->hasMany(ProductAvailability::class, 'product_id', 'product_id');
    }

    public function excludedDates()
    {
        return $this->hasMany(ProductExcludedDate::class, 'product_id', 'product_id');
    }

    public function schedules()
    {
        return $this->belongsToMany(
            Schedule::class,
            'schedule_product',
            'product_id',
            'schedule_id'
        )->withPivot(['is_active'])
          ->withTimestamps();
    }

    public function languages()
    {
        return $this->belongsToMany(
            ProductLanguage::class,
            'product_language_product',
            'product_id',
            'tour_language_id'
        )->withTimestamps();
    }

    public function amenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'amenity_product',
            'product_id',
            'amenity_id'
        )->withTimestamps();
    }

    public function excludedAmenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'amenity_product_excluded',
            'product_id',
            'amenity_id'
        )->withTimestamps();
    }

    public function pickupZones()
    {
        return $this->hasMany(ProductPickupZone::class, 'product_id', 'product_id')
                    ->orderBy('sort_order');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'product_id', 'product_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(ProductAuditLog::class, 'product_id', 'product_id');
    }

    public function pricingStrategies()
    {
        return $this->hasMany(ProductPricingStrategy::class, 'product_id', 'product_id');
    }

    public function subtype()
    {
        return $this->belongsTo(ProductTypeSubcategory::class, 'product_subtype_id', 'subtype_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('product_category', $category);
    }

    public function scopeTours($query)
    {
        return $query->where('product_category', 'guided_tour');
    }

    public function scopeTransfers($query)
    {
        return $query->whereIn('product_category', ['private_transfer', 'shuttle_service']);
    }

    public function scopeActivities($query)
    {
        return $query->where('product_category', 'adventure_activity');
    }

    // ==========================================
    // ACCESSORS & MUTATORS
    // ==========================================

    /**
     * Get cover image URL for appends
     * Note: The coverImage() relationship returns the ProductImage model
     * This accessor returns the URL string for backward compatibility
     */
    public function getCoverUrlAttribute(): string
    {
        // If coverImage relationship is loaded, use it
        if ($this->relationLoaded('coverImage') && $this->coverImage) {
            return $this->coverImage->url;
        }
        
        // Otherwise, get first image
        $firstImage = $this->images()->where('is_cover', true)->first();
        if ($firstImage) {
            return $firstImage->url;
        }
        
        // Fallback to placeholder
        return asset('images/volcano.png');
    }


    public function getDisplayCategoryAttribute()
    {
        return match($this->product_category) {
            'guided_tour' => __('Tour Guiado'),
            'private_transfer' => __('Transfer Privado'),
            'shuttle_service' => __('Servicio de Shuttle'),
            'adventure_activity' => __('Actividad de Aventura'),
            'equipment_rental' => __('Alquiler de Equipo'),
            'combo_package' => __('Paquete Combo'),
            'attraction_pass' => __('Pase de Atracción'),
            default => $this->product_category,
        };
    }

    public function getIsTransferAttribute()
    {
        return in_array($this->product_category, ['private_transfer', 'shuttle_service']);
    }

    public function getIsTourAttribute()
    {
        return $this->product_category === 'guided_tour';
    }

    public function getAdultPriceAttribute()
    {
        // Try to find a default price for adult
        return $this->prices->first(function($p) {
             return $p->is_active && $p->category && ($p->category->slug === 'adult' || $p->category->slug === 'adults');
        })?->price ?? 0;
    }

    public function getKidPriceAttribute()
    {
        // Try to find a default price for kid
         return $this->prices->first(function($p) {
             return $p->is_active && $p->category && ($p->category->slug === 'kid' || $p->category->slug === 'child' || $p->category->slug === 'children');
        })?->price ?? 0;
    }

    /**
     * Get active pricing strategy para una fecha
     */
    public function activePricingStrategy(?string $date = null)
    {
        $query = $this->pricingStrategies()->active();
        
        if ($date) {
            $query->validForDate($date);
        }
        
        return $query->orderBy('priority', 'desc')->first();
    }

    /**
     * Calcular precio del producto
     */
    public function calculatePrice(int $totalPassengers, array $breakdown = [], ?string $date = null): array
    {
        $strategy = $this->activePricingStrategy($date);
        
        if (!$strategy) {
            return [
                'error' => 'No hay estrategia de pricing configurada',
                'total' => 0,
            ];
        }
        
        return $strategy->calculatePrice($totalPassengers, $breakdown);
    }

    /**
     * Get base price for display (lowest possible)
     */
    public function getBasePriceAttribute(): float
    {
        $strategy = $this->activePricingStrategy();
        
        if (!$strategy) {
            return 0;
        }

        // Obtener el precio más bajo
        $lowestRule = $strategy->rules()
            ->where('is_active', true)
            ->orderBy('price', 'asc')
            ->first();

        return $lowestRule ? (float) $lowestRule->price : 0;
    }

    // ==========================================
    // AUTO SLUG
    // ==========================================
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ==========================================
    // MEDIA LIBRARY
    // ==========================================

    public function registerMediaCollections(): void
    {
        // Images collection
        $this->addMediaCollection('images')
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif'])
             ->useFallbackUrl(asset('images/volcano.png'))
             ->useFallbackPath(public_path('images/volcano.png'));
        
        // Videos collection
        $this->addMediaCollection('videos')
             ->acceptsMimeTypes(['video/mp4', 'video/quicktime', 'video/webm', 'video/x-msvideo'])
             ->useFallbackUrl(asset('images/video-placeholder.jpg'))
             ->useFallbackPath(public_path('images/video-placeholder.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail for cards/listings (400x300)
        $this->addMediaConversion('thumb')
             ->width(400)
             ->height(300)
             ->format('webp')
             ->quality(90)
             ->optimize()
             ->performOnCollections('images');
        
        // Medium for detail pages (800x600)
        $this->addMediaConversion('medium')
             ->width(800)
             ->height(600)
             ->format('webp')
             ->quality(92)
             ->optimize()
             ->performOnCollections('images');
        
        // Large for lightbox/zoom (1920x1080)
        $this->addMediaConversion('large')
             ->width(1920)
             ->height(1080)
             ->format('webp')
             ->quality(92)
             ->optimize()
             ->performOnCollections('images');
        
        // Optimized original JPEG (fallback)
        $this->addMediaConversion('optimized')
             ->quality(90)
             ->optimize()
             ->performOnCollections('images');
        
        // Video thumbnail/poster (first frame)
        $this->addMediaConversion('video_thumb')
             ->width(1920)
             ->height(1080)
             ->format('jpg')
             ->quality(85)
             ->performOnCollections('videos')
             ->nonQueued(); // Generate immediately for preview
    }

    // ==========================================
    // BACKWARD COMPATIBILITY (TEMPORAL)
    // ==========================================

    /**
     * Helper para obtener el nombre traducido.
     */
    public function getTranslatedName(string $locale = null): string
    {
        return $this->getTranslation('name', $locale ?? app()->getLocale());
    }
}
