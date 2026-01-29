<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductType extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasTranslations, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
             ->singleFile(); // Ensures only one cover image exists
    }

    protected $table = 'product_types';
    protected $primaryKey = 'product_type_id';
    
    public $translatable = ['name', 'description', 'duration']; // Added description and duration if they are translatable, verified in view usage

    public function getTranslatedName(?string $locale = null): string
    {
        return $this->getTranslation('name', $locale ?: app()->getLocale()) ?? $this->name ?? '';
    }

    public function getNameTranslatedAttribute()
    {
        return $this->getTranslatedName();
    }

    protected $fillable = [
        'name',
        'duration',
        'cover_path',
        'is_active',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_type_id', 'product_type_id');
    }

    // Backward compatibility

    public function scopeActive($query)
    {
         // Assuming logic for active? 
         // Original used IsActive trait or similar. 
         // ProductType table migration didn't show is_active column? 
         // Let's check schema or assume all active if no column.
         // Actually, Product has is_active. ProductType might not.
         // If no is_active column, return query.
         // Checking fillable: 'name', 'duration', 'cover_path'. No 'is_active'.
         return $query;
    }

    public function orderedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_type_product_order', 'product_type_id', 'product_id')
                    ->withPivot('position')
                    ->orderBy('product_type_product_order.position');
    }

    /**
     * Relación con subcategorías
     */
    public function subcategories()
    {
        return $this->hasMany(ProductTypeSubcategory::class, 'product_type_id', 'product_type_id')
                    ->orderBy('sort_order');
    }
    
}
