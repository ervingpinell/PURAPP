<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Tour;
use App\Models\TourTypeTranslation;

class TourType extends Model
{
    use HasFactory;

    protected $table = 'tour_types';
    protected $primaryKey = 'tour_type_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'is_active',
        'cover_path',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    // Para exponer cover_url ya listo en el modelo
    protected $appends = ['cover_url'];

    public function getCoverUrlAttribute(): string
    {
        return $this->cover_path
            ? asset('storage/' . ltrim($this->cover_path, '/'))
            : asset('images/volcano.png');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'tour_type_id', 'tour_type_id');
    }

    public function translations()
    {
        return $this->hasMany(TourTypeTranslation::class, 'tour_type_id', 'tour_type_id');
    }

    public function translate(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

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

    public function getNameTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->name;
    }

    public function getDescriptionTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->description;
    }

    public function getDurationTranslatedAttribute(): ?string
    {
        return optional($this->translate())?->duration;
    }

    public function getRouteKeyName()
    {
        return 'tour_type_id';
    }
}
