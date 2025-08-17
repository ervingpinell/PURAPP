<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Tour extends Model
{
    use HasFactory;

    protected $table = 'tours';
    protected $primaryKey = 'tour_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Exponer atributos calculados
    protected $appends = ['images', 'cover_image_url'];

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
        'viator_code',
    ];

    protected $casts = [
        'adult_price'  => 'float',
        'kid_price'    => 'float',
        'length'       => 'float',
        'max_capacity' => 'int',
        'is_active'    => 'bool',
    ];

    /* =====================
     * Scopes
     * ===================== */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /* =====================
     * Relaciones
     * ===================== */
    public function tourType()
    {
        return $this->belongsTo(TourType::class, 'tour_type_id', 'tour_type_id');
    }

    public function languages()
    {
        return $this->belongsToMany(
            TourLanguage::class,
            'tour_language_tour',
            'tour_id',
            'tour_language_id'
        )->withTimestamps();
    }

    public function amenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'amenity_tour',
            'tour_id',
            'amenity_id'
        )->withTimestamps();
    }

    public function excludedAmenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'excluded_amenity_tour',
            'tour_id',
            'amenity_id'
        )->withTimestamps();
    }

    public function schedules()
    {
        return $this->belongsToMany(
            Schedule::class,
            'schedule_tour',
            'tour_id',
            'schedule_id'
        )
        ->withPivot('is_active') // 👈 necesario para leer/guardar estado por tour
        ->withTimestamps();
    }
public function activeSchedules()
{
    return $this->belongsToMany(
        \App\Models\Schedule::class,
        'schedule_tour',
        'tour_id',
        'schedule_id'
    )
    ->withPivot('is_active')
    ->where('schedules.is_active', true)   // activo global
    ->wherePivot('is_active', true)        // activo en la asignación
    ->orderBy('schedules.start_time')
    ->withTimestamps();
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

    /* =====================
     * Traducciones
     * ===================== */
    public function translations()
    {
        return $this->hasMany(TourTranslation::class, 'tour_id', 'tour_id');
    }

    public function translate(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale() ?: 'es';

        // Normaliza a guiones y arma candidatos
        $locale = str_replace('_', '-', $locale);
        $candidates = [$locale];

        // sin región (es-CR -> es)
        if (str_contains($locale, '-')) {
            $base = explode('-', $locale)[0];
            if (!in_array($base, $candidates, true)) {
                $candidates[] = $base;
            }
        }

        // fallback de la app (ej. en o en-US)
        $appFallback = str_replace('_', '-', (string) config('app.fallback_locale', 'en'));
        if (!in_array($appFallback, $candidates, true)) {
            $candidates[] = $appFallback;
        }
        if (str_contains($appFallback, '-')) {
            $base = explode('-', $appFallback)[0];
            if (!in_array($base, $candidates, true)) {
                $candidates[] = $base;
            }
        }

        // español como último recurso
        if (!in_array('es', $candidates, true)) {
            $candidates[] = 'es';
        }

        // Obtiene translations (si ya está eager-loaded no dispara query)
        $translations = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        if ($translations->isEmpty()) {
            return null;
        }

        // Indexa por exacto y por idioma base
        $byExact = [];
        $byLang  = [];
        foreach ($translations as $tr) {
            $loc = str_replace('_', '-', (string) $tr->locale);
            $byExact[$loc] = $tr;

            $lang = explode('-', $loc)[0];
            if (!isset($byLang[$lang])) {
                $byLang[$lang] = $tr;
            }
        }

        // Busca por candidatos: exacto -> idioma
        foreach ($candidates as $cand) {
            if (isset($byExact[$cand])) {
                return $byExact[$cand];
            }
            $lang = explode('-', $cand)[0];
            if (isset($byLang[$lang])) {
                return $byLang[$lang];
            }
        }

        return null;
    }

    public function getTranslatedName(?string $preferredLocale = null): string
    {
        $tr = $this->translate($preferredLocale);
        return ($tr?->name) ?? ($this->name ?? '');
    }

    public function getTranslatedOverview(?string $preferredLocale = null): string
    {
        $tr = $this->translate($preferredLocale);
        return ($tr?->overview) ?? ($this->overview ?? '');
    }

    /* =====================
     * Accessors de imágenes
     * ===================== */

    /**
     * Array de URLs públicas de las imágenes del tour
     * (lee de storage/app/public/tours/{tour_id}/gallery/*.{webp,jpg,jpeg,png})
     */
    public function getImagesAttribute(): array
    {
        $dir = "tours/{$this->tour_id}/gallery";

        if (!Storage::disk('public')->exists($dir)) {
            return [];
        }

        $files = collect(Storage::disk('public')->files($dir))
            ->filter(fn ($p) => preg_match('/\.(webp|jpe?g|png)$/i', $p))
            ->sort(function ($a, $b) {
                return strnatcasecmp(basename($a), basename($b));
            })
            ->values();

        return $files->map(fn ($path) => Storage::url($path))->all();
    }

    /**
     * URL de portada (primera imagen o fallback)
     */
    public function getCoverImageUrlAttribute(): string
    {
        $images = $this->images;
        return $images[0] ?? asset('images/volcano.png');
    }

    public function images()
    {
        return $this->hasMany(TourImage::class, 'tour_id', 'tour_id')->orderBy('position');
    }

    public function coverImage()
    {
        return $this->hasOne(TourImage::class, 'tour_id', 'tour_id')->where('is_cover', true);
    }

    // Helpers para front
    public function coverUrl(): string
    {
        if ($this->relationLoaded('coverImage') ? $this->coverImage : $this->coverImage()->first()) {
            return $this->coverImage->url();
        }
        // fallback al campo image_path si lo tienes, o al volcano
        if (!empty($this->image_path)) return asset('storage/'.$this->image_path);
        return asset('images/volcano.png');
    }

    public function galleryUrls(): array
    {
        $imgs = ($this->relationLoaded('images') ? $this->images : $this->images()->get());
        if ($imgs->isNotEmpty()) return $imgs->map->url()->all();

        // fallback a escanear carpeta si aún no migras todo
        $folder = "tours/{$this->tour_id}/gallery";
        if (\Storage::disk('public')->exists($folder)) {
            return collect(\Storage::disk('public')->files($folder))
                ->filter(fn($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']))
                ->sort(fn($a,$b) => strnatcasecmp($a,$b))
                ->map(fn($p) => asset('storage/'.$p))
                ->values()->all();
        }
        return [asset('images/volcano.png')];
    }
}
