<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

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
    CustomerCategory,
    User,
    TourAuditLog
};

/**
 * Tour Model
 *
 * Represents a tour/experience offered by the platform.
 * Handles tour lifecycle, pricing, scheduling, capacity management, and draft/publish workflow.
 *
 * @property int $tour_id Primary key
 * @property string $name Tour name
 * @property string $slug URL-friendly slug (auto-generated)
 * @property string|null $description Tour description
 * @property int|null $tour_type_id Tour type/category
 * @property int|null $duration_hours Duration in hours
 * @property int|null $duration_minutes Duration in minutes
 * @property int|null $base_capacity Default capacity per schedule
 * @property float|null $base_price Base price (deprecated, use TourPrice)
 * @property string|null $difficulty Difficulty level
 * @property string|null $main_image Main tour image path
 * @property bool $is_active Tour active status
 * @property bool $is_draft Whether tour is in draft mode
 * @property int|null $wizard_step Current wizard step (1-5)
 * @property int|null $created_by_user_id User who created the tour
 * @property int|null $last_edited_by_user_id User who last edited the tour
 * @property \Carbon\Carbon|null $published_at When tour was published
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at Soft delete timestamp
 *
 * @property-read TourType|null $tourType
 * @property-read \Illuminate\Database\Eloquent\Collection|TourLanguage[] $languages
 * @property-read \Illuminate\Database\Eloquent\Collection|Amenity[] $amenities
 * @property-read \Illuminate\Database\Eloquent\Collection|Amenity[] $excludedAmenities
 * @property-read \Illuminate\Database\Eloquent\Collection|Schedule[] $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection|Schedule[] $activeSchedules
 * @property-read \Illuminate\Database\Eloquent\Collection|TourAvailability[] $availabilities
 * @property-read Itinerary|null $itinerary
 * @property-read \Illuminate\Database\Eloquent\Collection|TourExcludedDate[] $excludedDates
 * @property-read \Illuminate\Database\Eloquent\Collection|TourTranslation[] $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|TourImage[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|TourPrice[] $prices
 * @property-read \Illuminate\Database\Eloquent\Collection|Tax[] $taxes
 * @property-read \Illuminate\Database\Eloquent\Collection|Booking[] $bookings
 * @property-read User|null $creator
 * @property-read User|null $lastEditor
 * @property-read string $creator_name
 * @property-read string $last_editor_name
 */
class Tour extends Model
{
    use HasFactory, SoftDeletes;
    use Auditable;

    protected $table = 'tours';
    protected $primaryKey = 'tour_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $appends = ['images', 'cover_image_url'];

    protected $fillable = [
        'name',  // TODO: Remove after migration drops this column
        'slug',
        'overview',  // TODO: Remove after migration drops this column
        'length',
        'max_capacity',
        'group_size',
        'is_active',
        'tour_type_id',
        'itinerary_id',
        'color',
        'cutoff_hour',
        'lead_days',

        'is_draft',
        'current_step',
        'created_by',
        'updated_by',
        'recommendations',
    ];

    protected $casts = [
        'length'       => 'float',
        'max_capacity' => 'int',
        'is_active'    => 'bool',


        'is_draft'     => 'bool',
        'current_step' => 'int',
    ];

    /**
     * ============================================================
     * 🆕 CONFIGURACIÓN DE AUDITORÍA
     * ============================================================
     */
    protected $auditableFields = [
        'name',
        'slug',
        'overview',
        'length',
        'max_capacity',
        'group_size',
        'tour_type_id',
        'is_active',
        'is_draft',
        'current_step',
        'color',
    ];

    /**
     * Boot del modelo para generar slug automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tour) {
            // Si no viene slug Y existe name, generarlo
            // (Con translations, el name puede no existir en el modelo)
            if (empty($tour->slug) && !empty($tour->name)) {
                $tour->slug = static::generateUniqueSlug($tour->name);
            }

            // 👇 Asegurar defaults de borrador si no vienen
            if (is_null($tour->is_draft)) {
                $tour->is_draft = true;
            }
            if (is_null($tour->current_step)) {
                $tour->current_step = 1;
            }

            // 🆕 AGREGAR: Auto-asignar created_by si está autenticado
            if (is_null($tour->created_by) && auth()->check()) {
                $tour->created_by = auth()->id();
            }
        });

        static::updating(function ($tour) {
            // Solo regenerar si el nombre cambió Y el usuario no proveyó un slug personalizado
            if ($tour->isDirty('name') && !$tour->isDirty('slug') && !empty($tour->name)) {
                $tour->slug = static::generateUniqueSlug($tour->name, $tour->tour_id);
            }

            // 🆕 AGREGAR: Auto-asignar updated_by si está autenticado
            if ($tour->isDirty() && auth()->check()) {
                $tour->updated_by = auth()->id();
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

    /* =====================
     * SCOPES - EXISTENTES
     * ===================== */

    /**
     * Tours activos para la app (ya publicados)
     * AHORA: no incluye borradores.
     */
    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where('is_draft', false);
    }

    /**
     * Solo borradores (en proceso de wizard)
     */
    public function scopeDraft($query)
    {
        return $query->where('is_draft', true);
    }

    /**
     * Publicados (sin importar si is_active está on/off)
     * Útil si quieres distinguir "publicado pero inactivo".
     */
    public function scopePublished($query)
    {
        return $query->where('is_draft', false);
    }

    /**
     * Borradores que están en un paso >= X (por ejemplo para UX)
     */
    public function scopeWizardStepAtLeast($query, int $step)
    {
        return $query->where('is_draft', true)
            ->where('current_step', '>=', $step);
    }

    /* =====================
     * 🆕 SCOPES NUEVOS
     * ===================== */

    /**
     * Borradores de un usuario específico
     */
    public function scopeUserDrafts($query, $userId)
    {
        return $query->where('is_draft', true)
            ->where('created_by', $userId);
    }

    /**
     * Tours modificados recientemente
     */
    public function scopeRecentlyModified($query, $days = 7)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    /**
     * Drafts antiguos (sin actividad)
     */
    public function scopeOldDrafts($query, $days = 30)
    {
        return $query->where('is_draft', true)
            ->where('updated_at', '<', now()->subDays($days));
    }

    /**
     * Tours inactivos
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false)
            ->where('is_draft', false);
    }

    /* =====================
     * HELPERS WIZARD - EXISTENTES
     * ===================== */

    public function isDraft(): bool
    {
        return (bool) $this->is_draft;
    }

    public function isPublished(): bool
    {
        return !$this->is_draft && (bool) $this->is_active;
    }

    public function inStep(int $step): bool
    {
        return (int) $this->current_step === $step;
    }

    public function advanceToStep(int $step): void
    {
        // Útil si quieres centralizar la lógica
        if ($step > (int) $this->current_step) {
            $this->current_step = $step;
            $this->save();
        }
    }

    /* =====================
     * 🆕 HELPERS NUEVOS
     * ===================== */

    /**
     * Verificar si el tour es editable por un usuario
     */
    public function isEditableBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        // Si no es draft, cualquier admin puede editar
        if (!$this->is_draft) {
            return true;
        }

        // Si es draft y está configurado creator_only_edit_drafts
        if (config('tours.permissions.creator_only_edit_drafts', true)) {
            return $this->created_by === $userId;
        }

        return true;
    }

    /**
     * Verificar si el draft está inactivo (sin cambios recientes)
     */
    public function isInactiveDraft(int $days = 7): bool
    {
        if (!$this->is_draft) {
            return false;
        }

        return $this->updated_at->diffInDays(now()) >= $days;
    }

    /**
     * Obtener porcentaje de completado del wizard
     */
    public function getWizardCompletionPercentage(): int
    {
        if (!$this->is_draft || !$this->current_step) {
            return 100;
        }

        return (int) (($this->current_step / 6) * 100);
    }

    /**
     * Obtener nombre del creador
     */
    public function getCreatorNameAttribute(): string
    {
        return $this->created_by_user?->name ?? 'Desconocido';
    }

    /**
     * Obtener nombre del último editor
     */
    public function getLastEditorNameAttribute(): string
    {
        return $this->updated_by_user?->name ?? 'Desconocido';
    }

    /* =====================
     * RELACIONES - EXISTENTES
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
            ->with(['category.translations']) // <-- PRELOAD traducciones
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
        return $adultPrice ? (float) $adultPrice->final_price : null;
    }

    /**
     * Accessor de SOLO LECTURA para no romper vistas que lean $tour->kid_price
     * (calcula desde tour_prices con slug 'kid' o 'child')
     */
    public function getKidPriceAttribute(): ?float
    {
        $kid = $this->getPriceForCategory('kid') ?: $this->getPriceForCategory('child');
        return $kid ? (float) $kid->final_price : null;
    }

    /**
     * Relación polimórfica con impuestos
     */
    public function taxes()
    {
        return $this->morphToMany(Tax::class, 'taxable', 'taxables', 'taxable_id', 'tax_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * 🆕 Obtiene precios activos para una fecha específica
     * Retorna SOLO UN PRECIO por categoría (el válido para la fecha)
     *
     * @param string|Carbon|null $date Fecha para filtrar (default: hoy)
     * @return \Illuminate\Support\Collection
     */
    public function activePricesForDate($date = null)
    {
        $date = $date ?? now();

        // Obtener todas las categorías activas con precios
        $allPrices = $this->prices()
            ->where('is_active', true)
            ->with(['category.translations'])
            ->whereHas('category', fn($q) => $q->where('is_active', true))
            ->validForDate($date)
            ->get();

        // Agrupar por categoría y tomar solo el primero (prioridad: con fechas > sin fechas)
        $uniquePrices = $allPrices->groupBy('category_id')->map(function ($prices) {
            // Ordenar: primero los que tienen fechas (seasonal), luego los por defecto
            return $prices->sortBy(function ($price) {
                // Precios con fechas tienen prioridad (0), sin fechas van después (1)
                return ($price->valid_from || $price->valid_until) ? 0 : 1;
            })->first();
        })->values();

        return $uniquePrices;
    }

    public function images()
    {
        return $this->hasMany(TourImage::class, 'tour_id', 'tour_id')->orderBy('position');
    }

    public function coverImage()
    {
        return $this->hasOne(TourImage::class, 'tour_id', 'tour_id')->where('is_cover', true);
    }

    /* =====================
     * 🆕 RELACIONES NUEVAS - AUDITORÍA Y USUARIOS
     * ===================== */

    /**
     * Usuario que creó el tour
     */
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Usuario que hizo la última actualización
     */
    public function updated_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    /**
     * Logs de auditoría del tour
     * (Ya está definido en el trait Auditable, pero puedes personalizarlo aquí)
     */
    public function auditLogs()
    {
        return $this->hasMany(TourAuditLog::class, 'tour_id', 'tour_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Último log de auditoría
     */
    public function lastAuditLog()
    {
        return $this->hasOne(TourAuditLog::class, 'tour_id', 'tour_id')
            ->latest('created_at');
    }

    /* =====================
     * Traducciones helpers - EXISTENTES
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
     * Imágenes helpers - EXISTENTES
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
     * Capacidad helpers - EXISTENTES
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
