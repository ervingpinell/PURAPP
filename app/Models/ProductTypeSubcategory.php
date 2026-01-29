<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ProductTypeSubcategory extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'product_type_subcategories';
    protected $primaryKey = 'subtype_id';
    
    public $translatable = ['name', 'description', 'meta_title', 'meta_description'];
    
    protected $fillable = [
        'product_type_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relación con ProductType
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id', 'product_type_id');
    }

    /**
     * Relación con Products
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'product_subtype_id', 'subtype_id');
    }

    /**
     * Get translated name for current or specified locale
     */
    public function getTranslatedName(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->getTranslation('name', $locale, false) ?: $this->name;
    }

    /**
     * Get translated meta title
     */
    public function getTranslatedMetaTitle(string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->getTranslation('meta_title', $locale, false);
    }

    /**
     * Get translated meta description
     */
    public function getTranslatedMetaDescription(string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->getTranslation('meta_description', $locale, false);
    }

    /**
     * Scope: only active subtypes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
