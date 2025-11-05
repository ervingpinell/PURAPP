<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

// Relacionados
use App\Models\{
    TourType,
    TourLanguage,
    Amenity,
    Schedule,
    TourAvailability,
    Itinerary,
    TourExcludedDate,
    TourTranslation,
    Booking,
    TourImage,
    TourPrice,
    CustomerCategory
};

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tours';
    protected $primaryKey = 'tour_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $appends = ['images', 'cover_image_url'];

    protected $fillable = [
        'name',
        'slug',
        'overview',
        'length',
        'max_capacity',
        'group_size',
        'is_active',
        'tour_type_id',
        'itinerary_id',
        'color',
        'viator_code',
        'cutoff_hour',
        'lead_days',
    ];

    protected $casts = [
        'length'       => 'float',
        'max_capacity' => 'int',
        'is_active'    => 'bool',
    ];

    /**
     * Boot del modelo para generar slug automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tour) {
            if (empty($tour->slug)) {
                $tour->slug = static::generateUniqueSlug($tour->name);
            }
        });

        static::updating(function ($tour) {
            // Solo regenerar si el nombre cambió Y el usuario no proveyó un slug personalizado
            if ($tour->isDirty('name') && !$tour->isDirty('slug')) {
                $tour->slug = static::generateUniqueSlug($tour->name, $tour->tour_id);
            }
        });
    }

    /**
     * Genera un slug único basado en el nombre
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::slugExists($slug, $ignoreId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verifica si un slug ya existe (excepto para el tour actual)
     */
    protected static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = static::where('slug', $slug);

        if ($ignoreId) {
            $query->where('tour_id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    /**
     * Laravel usará slug en lugar de ID para route model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Resolver ruta por slug o ID (backward compatibility)
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Intentar por slug primero
        $tour = $this->where('slug', $value)->first();

        // Si no existe y es numérico, intentar por ID (backward compatibility)
        if (!$tour && is_numeric($value)) {
            $tour = $this->where('tour_id', $value)->first();
        }

        return $tour;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
            ->withPivot(['is_active', 'cutoff_hour', 'lead_days', 'base_capacity'])
            ->withTimestamps();
    }

    public function activeSchedules()
    {
        return $this->belongsToMany(
            Schedule::class,
            'schedule_tour',
            'tour_id',
            'schedule_id'
        )
            ->withPivot('is_active')
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
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

    public function translations()
    {
        return $this->hasMany(TourTranslation::class, 'tour_id', 'tour_id');
    }

    /** Relación con reservas, usada para proteger purgas */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tour_id', 'tour_id');
    }

    /** Orden por tipo (si usas el pivot de ordenamiento por tipo) */
    public function typeOrders()
    {
        return $this->belongsToMany(
            TourType::class,
            'tour_type_tour_order',
            'tour_id',
            'tour_type_id'
        )->withPivot('position');
    }

    /**
     * Relación con precios por categoría
     */
    public function prices()
    {
        return $this->hasMany(TourPrice::class, 'tour_id', 'tour_id');
    }

    /**
     * Precios activos con categorías cargadas
     */
    public function activePrices()
    {
        return $this->hasMany(TourPrice::class, 'tour_id', 'tour_id')
            ->where('is_active', true)
            ->with('category')
            ->whereHas('category', fn($q) => $q->where('is_active', true))
            ->orderBy('category_id');
    }

    /**
     * Obtiene el precio para una categoría específica (id o slug)
     */
    public function getPriceForCategory(int|string $categoryIdOrSlug): ?TourPrice
    {
        $categoryId = $categoryIdOrSlug;

        if (is_string($categoryIdOrSlug)) {
            $category = CustomerCategory::where('slug', $categoryIdOrSlug)->first();
            if (!$category) return null;
            $categoryId = $category->category_id;
        }

        return $this->prices()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Accessor de SOLO LECTURA para no romper vistas que lean $tour->adult_price
     * (calcula desde tour_prices con slug 'adult')
     */
    public function getAdultPriceAttribute(): ?float
    {
        $adultPrice = $this->getPriceForCategory('adult');
        return $adultPrice ? (float) $adultPrice->price : null;
    }

    /**
     * Accessor de SOLO LECTURA para no romper vistas que lean $tour->kid_price
     * (calcula desde tour_prices con slug 'kid' o 'child')
     */
    public function getKidPriceAttribute(): ?float
    {
        $kid = $this->getPriceForCategory('kid') ?: $this->getPriceForCategory('child');
        return $kid ? (float) $kid->price : null;
    }

    /* =====================
     * Traducciones helpers
     * ===================== */
    public function translate(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale() ?: 'es';
        $locale = str_replace('_', '-', $locale);
        $candidates = [$locale];

        if (str_contains($locale, '-')) {
            $base = explode('-', $locale)[0];
            if (!in_array($base, $candidates, true)) {
                $candidates[] = $base;
            }
        }

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

        if (!in_array('es', $candidates, true)) {
            $candidates[] = 'es';
        }

        $translations = $this->relationLoaded('translations')
            ? $this->getRelation('translations')
            : $this->translations()->get();

        if ($translations->isEmpty()) {
            return null;
        }

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
     * Imágenes helpers
     * ===================== */
    public function getImagesAttribute(): array
    {
        $dir = "tours/{$this->tour_id}/gallery";

        if (!Storage::disk('public')->exists($dir)) {
            return [];
        }

        $files = collect(Storage::disk('public')->files($dir))
            ->filter(fn($p) => preg_match('/\.(webp|jpe?g|png)$/i', $p))
            ->sort(function ($a, $b) {
                return strnatcasecmp(basename($a), basename($b));
            })
            ->values();

        return $files->map(fn($path) => Storage::url($path))->all();
    }

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

    public function coverUrl(): string
    {
        if ($this->relationLoaded('coverImage') ? $this->coverImage : $this->coverImage()->first()) {
            return $this->coverImage->url();
        }
        if (!empty($this->image_path)) return asset('storage/' . $this->image_path);
        return asset('images/volcano.png');
    }

    public function galleryUrls(): array
    {
        $imgs = ($this->relationLoaded('images') ? $this->images : $this->images()->get());
        if ($imgs->isNotEmpty()) return $imgs->map->url()->all();

        $folder = "tours/{$this->tour_id}/gallery";
        if (Storage::disk('public')->exists($folder)) {
            return collect(Storage::disk('public')->files($folder))
                ->filter(fn($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                ->sort(fn($a, $b) => strnatcasecmp($a, $b))
                ->map(fn($p) => asset('storage/' . $p))
                ->values()->all();
        }
        return [asset('images/volcano.png')];
    }

    /* =====================
     * Capacidad helpers
     * ===================== */

    /**
     * Obtener capacidad efectiva para una fecha y horario específico
     * Jerarquía (de mayor a menor prioridad):
     * 1. Override Día+Horario (TourAvailability con schedule_id)
     * 2. Override Día (TourAvailability sin schedule_id)
     * 3. Override Horario (pivote base_capacity en schedule_tour)
     * 4. Capacidad Base del Tour (Tour.max_capacity)
     */
    public function getEffectiveCapacity(string $date, int $scheduleId): int
    {
        // 1) Override día+horario
        $dayScheduleOverride = TourAvailability::where('tour_id', $this->tour_id)
            ->where('schedule_id', $scheduleId)
            ->whereDate('date', $date)
            ->first();

        if ($dayScheduleOverride) {
            if ($dayScheduleOverride->is_blocked) {
                return 0;
            }
            if ($dayScheduleOverride->max_capacity !== null) {
                return (int) $dayScheduleOverride->max_capacity;
            }
        }

        // 2) Override por día (sin horario)
        $dayOverride = TourAvailability::where('tour_id', $this->tour_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $date)
            ->first();

        if ($dayOverride) {
            if ($dayOverride->is_blocked) {
                return 0;
            }
            if ($dayOverride->max_capacity !== null) {
                return (int) $dayOverride->max_capacity;
            }
        }

        // 3) Capacidad del pivote schedule_tour
        $schedule = $this->schedules()
            ->where('schedules.schedule_id', $scheduleId)
            ->first();

        if ($schedule && $schedule->pivot && $schedule->pivot->base_capacity !== null) {
            return (int) $schedule->pivot->base_capacity;
        }

        // 4) Capacidad base del tour (o default 15 si no se definió)
        return (int) ($this->max_capacity ?? 15);
    }

    /**
     * Indica el nivel de override aplicado (para UI)
     * Retorna: 'day-schedule', 'day', 'pivot', 'tour', o 'blocked'
     */
    public function getCapacityOverrideLevel(string $date, int $scheduleId): string
    {
        $dayScheduleOverride = TourAvailability::where('tour_id', $this->tour_id)
            ->where('schedule_id', $scheduleId)
            ->whereDate('date', $date)
            ->first();
        if ($dayScheduleOverride) {
            return $dayScheduleOverride->is_blocked ? 'blocked' : 'day-schedule';
        }

        $dayOverride = TourAvailability::where('tour_id', $this->tour_id)
            ->whereNull('schedule_id')
            ->whereDate('date', $date)
            ->first();
        if ($dayOverride) {
            return $dayOverride->is_blocked ? 'blocked' : 'day';
        }

        $schedule = $this->schedules()
            ->where('schedules.schedule_id', $scheduleId)
            ->first();
        if ($schedule && $schedule->pivot && $schedule->pivot->base_capacity !== null) {
            return 'pivot';
        }

        return 'tour';
    }
}
