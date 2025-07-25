<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tour extends Model
{
    use HasFactory;

    protected $table = 'tours';
    protected $primaryKey = 'tour_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'overview',
        'adult_price',
        'kid_price',
        'length',
        'max_capacity',
        'is_active',
        'tour_type_id',
        'itinerary_id',
        'color',
    ];

    // ✅ Relaciones
    public function tourType()
    {
        return $this->belongsTo(TourType::class, 'tour_type_id', 'tour_type_id');
    }

    public function languages()
    {
        return $this->belongsToMany(TourLanguage::class, 'tour_language_tour', 'tour_id', 'tour_language_id');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_tour', 'tour_id', 'amenity_id');
    }

    public function excludedAmenities()
    {
        return $this->belongsToMany(Amenity::class, 'excluded_amenity_tour', 'tour_id', 'amenity_id');
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_tour', 'tour_id', 'schedule_id');
    }

    public function availabilities()
    {
        return $this->hasMany(TourAvailability::class, 'tour_id', 'tour_id');
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id', 'itinerary_id');
    }

    public function excludedDates()
    {
        return $this->hasMany(TourExcludedDate::class, 'tour_id', 'tour_id');
    }

    // ✅ Traducciones
    public function translations()
    {
        return $this->hasMany(TourTranslation::class, 'tour_id', 'tour_id');
    }

    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
    }
}
