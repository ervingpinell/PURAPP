<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * MeetingPoint Model
 *
 * Represents a tour meeting point location.
 */
class MeetingPoint extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'meeting_points';
    protected $primaryKey = 'id';

    public $translatable = ['name', 'description', 'instructions'];

    protected $fillable = [
        'name',
        'description',
        'instructions',
        'pickup_time',
        'map_url',
        'sort_order',
        'is_active',
        'deleted_by',
    ];

    protected $casts = [
        'is_active'  => 'bool',
        'sort_order' => 'int',
    ];

    /* ----------------------------------------
     | Scopes
     |-----------------------------------------*/
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        // Primero los que tienen sort_order definido, luego por nombre
        return $q->orderByRaw('sort_order IS NULL, sort_order ASC');
    }

    /**
     * Scope para items eliminados hace más de X días
     */
    public function scopeOlderThan($q, int $days = 30)
    {
        return $q->onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days));
    }

    /* ----------------------------------------
     | Relaciones
     |-----------------------------------------*/
    // Removed translations relation

    /**
     * Usuario que eliminó este meeting point
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /* ----------------------------------------
     | Helpers de localización
     |-----------------------------------------*/
    // Spatie handles getTranslated via $model->attribute

    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        // Spatie uses full locale or fallback mapping configured in config/translatable
        // Ideally we just call getTranslation
        return $this->getTranslation($field, $locale);
    }

    public function getNameLocalizedAttribute(): string
    {
        return (string) $this->name;
    }

    public function getDescriptionLocalizedAttribute(): ?string
    {
        return $this->description;
    }

    public function getInstructionsLocalizedAttribute(): ?string
    {
        return $this->instructions;
    }
}
