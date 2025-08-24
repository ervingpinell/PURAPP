<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourImage extends Model
{
    use HasFactory;

    protected $table = 'tour_images';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'tour_id',
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

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function getUrlAttribute(): string
    {
        $path = ltrim((string) $this->path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }
        return asset('storage/'.$path);
    }
}
