<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

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

    protected $appends = ['cover_image', 'display_category'];

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

    public function getCoverImageAttribute()
    {
        return $this->images()->first()?->path ?? '/images/placeholder-tour.jpg';
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
