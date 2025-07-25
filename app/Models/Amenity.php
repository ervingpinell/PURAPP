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

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'amenity_tour', 'amenity_id', 'tour_id');
    }

    public function excludedFromTours()
    {
        return $this->belongsToMany(Tour::class, 'excluded_amenity_tour', 'amenity_id', 'tour_id');
    }

    // 🔁 Traducciones
public function translations()
{
    return $this->hasMany(\App\Models\AmenityTranslation::class, 'amenity_id');
}

public function translate($locale = null)
{
    $locale = $locale ?? app()->getLocale();
    return $this->translations->firstWhere('locale', $locale)
        ?? $this->translations->firstWhere('locale', config('app.fallback_locale'));
}

}
