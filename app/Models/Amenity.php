<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Amenity Model
 *
 * Represents a tour amenity/feature (WiFi, AC, etc.).
 */
class Amenity extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'amenities';
    protected $primaryKey = 'amenity_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public $translatable = ['name'];

    protected $fillable = ['name', 'icon', 'category', 'is_active', 'deleted_by'];

    protected $casts = [
        'is_active' => 'bool',
        'deleted_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }

    public function scopeOlderThan($query, $days)
    {
        return $query->where('deleted_at', '<=', now()->subDays($days));
    }


    public function excludedFromTours()
    {
        return $this->belongsToMany(
            Product::class,
            'excluded_amenity_tour',
            'amenity_id',
            'product_id'
        )
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function getNameTranslatedAttribute(): ?string
    {
        return $this->name;
    }
}
