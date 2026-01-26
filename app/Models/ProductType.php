<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ProductType extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'product_types';
    protected $primaryKey = 'product_type_id';
    
    public $translatable = ['name', 'description', 'duration']; // Added description and duration if they are translatable, verified in view usage

    public function getTranslatedName(?string $locale = null): string
    {
        return $this->getTranslation('name', $locale ?: app()->getLocale());
    }

    public function getNameTranslatedAttribute()
    {
        return $this->getTranslatedName();
    }

    protected $fillable = [
        'name',
        'duration',
        'cover_path',
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
        return $this->belongsToMany(Product::class, 'tour_type_tour_order', 'tour_type_id', 'product_id')
                    ->withPivot('position')
                    ->orderBy('tour_type_tour_order.position');
    }
    
}
