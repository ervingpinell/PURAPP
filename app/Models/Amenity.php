<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    use HasFactory;

    protected $table = 'amenities';
    protected $primaryKey = 'amenity_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'amenity_tour',
            'amenity_id',
            'tour_id'
        )
        ->withPivot('is_active')     // si tu pivote lo tiene
        ->withTimestamps();          // si tu pivote tiene timestamps
    }

    public function excludedFromTours()
    {
        return $this->belongsToMany(
            Tour::class,
            'excluded_amenity_tour',
            'amenity_id',
            'tour_id'
        )
        ->withPivot('is_active')
        ->withTimestamps();
    }

    // Translations
    public function translations()
    {
        // 👇 especifica también el local key (amenity_id)
        return $this->hasMany(AmenityTranslation::class, 'amenity_id', 'amenity_id');
    }

    public function translate(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        // Evita N+1 si ya está cargada la relación
        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
        }

        return $this->translations()
            ->where('locale', $locale)
            ->first()
            ?? $this->translations()
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }

    // (Opcional) Accessor directo
    public function getNameTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->name;
    }
}
