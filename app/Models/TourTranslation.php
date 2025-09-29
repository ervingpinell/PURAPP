<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TourTranslation extends Model
{
    protected $table = 'tour_translations';
    public $timestamps = true;

    protected $fillable = [
        'tour_id',
        'locale',
        'name',
        'overview',
        'slug',      // <-- NUEVO
    ];

    protected static function booted()
    {
        static::saving(function (TourTranslation $tr) {
            if (empty($tr->slug)) {
                $source = $tr->name ?: ('tour-'.$tr->tour_id.'-'.$tr->locale);
                $tr->slug = Str::slug($source);
            } else {
                $tr->slug = Str::slug($tr->slug);
            }
        });
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
