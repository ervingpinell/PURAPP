<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ProductImage Model
 *
 * Represents an image for a product.
 */
class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'path',
        'caption',
        'position',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'bool',
        'position' => 'int',
    ];

    protected $appends = ['url'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function getUrlAttribute(): string
    {
        $path = ltrim((string) $this->path, '/');

        if ($path === '') {
            return asset('images/volcano.png'); // Placeholder
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }
}
