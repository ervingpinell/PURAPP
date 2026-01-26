<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * Policy Model
 *
 * Represents a policy (cancellation, terms, etc.).
 */
class Policy extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'policies';
    protected $primaryKey = 'policy_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Policy types (canonical identifiers)
    public const TYPE_TERMS = 'terms';
    public const TYPE_PRIVACY = 'privacy';
    public const TYPE_CANCELLATION = 'cancellation';
    public const TYPE_REFUND = 'refund';
    public const TYPE_WARRANTY = 'warranty';

    public const TYPES = [
        self::TYPE_TERMS => 'Terms and Conditions',
        self::TYPE_PRIVACY => 'Privacy Policy',
        self::TYPE_CANCELLATION => 'Cancellation Policy',
        self::TYPE_REFUND => 'Refund Policy',
        self::TYPE_WARRANTY => 'Warranty Policy',
    ];

    public $translatable = ['name', 'content'];

    protected $fillable = [
        'slug',
        'type',
        'is_default',
        'is_active',
        'effective_from',
        'effective_to',
        'deleted_by',
        'name',
        'content',
    ];

    protected $casts = [
        'is_default'     => 'bool',
        'is_active'      => 'bool',
        'effective_from' => 'date',
        'effective_to'   => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ===================== BOOT ===================== */

    protected static function booted()
    {
        static::creating(function (self $policy) {
            if (empty($policy->slug)) {
                $policy->slug = $policy->slug ?: null;
            } else {
                $policy->slug = $policy->generateUniqueSlug($policy->slug);
            }
        });

        static::updating(function (self $policy) {
            if ($policy->isDirty('slug') && !empty($policy->slug)) {
                $policy->slug = $policy->generateUniqueSlug($policy->slug);
            }
        });
    }

    /* ===================== SLUG ===================== */

    public function generateUniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->where('policy_id', '!=', $this->policy_id ?? 0)
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function regenerateSlug(string $from): self
    {
        $this->slug = $this->generateUniqueSlug($from);
        $this->save();
        return $this;
    }

    /* ===================== ACCESSORS PARA BLADE ===================== */

    public function getDisplayNameAttribute(): string
    {
        return (string) $this->name;
    }

    public function getDisplayContentAttribute(): string
    {
        return (string) $this->content;
    }

    public function getNameTranslatedAttribute(): ?string
    {
        return $this->name;
    }

    public function getTitleTranslatedAttribute(): ?string
    {
        return $this->name;
    }

    public function getContentTranslatedAttribute(): ?string
    {
        return $this->content;
    }

    /* ===================== RELACIONES ===================== */

    public function sections()
    {
        return $this->hasMany(PolicySection::class, 'policy_id', 'policy_id')
            ->orderBy('sort_order')
            ->orderBy('section_id');
    }

    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }

    public function deletedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }

    /* ===================== SCOPES ===================== */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeEffectiveOn($q, ?Carbon $date = null)
    {
        $d = ($date ?: now())->toDateString();

        return $q->where(function ($qq) use ($d) {
            $qq->whereNull('effective_from')->orWhereDate('effective_from', '<=', $d);
        })->where(function ($qq) use ($d) {
            $qq->whereNull('effective_to')->orWhereDate('effective_to', '>=', $d);
        });
    }

    public function scopeType($q, string $type)
    {
        if (Schema::hasColumn($this->getTable(), 'type')) {
            return $q->where('type', $type);
        }
        return $q;
    }

    /* ===================== HELPERS ===================== */

    public static function byType(string $type): ?self
    {
        $query = static::query()
            ->active()
            ->effectiveOn();
            
        // Assuming 'type' column exists or we search by name?
        // Original code checked Schema for 'type'.
        // If 'type' is not in DB, fallback to name ILIKE.
        
        // Since we migrated Policy without ensuring 'type' column, let's assume it exists as it was in original fillable.
        // Yes, fillable had 'type'.
        
        if (Schema::hasColumn((new static)->getTable(), 'type')) {
            return $query->where('type', $type)
                ->orderByDesc('effective_from')
                ->first();
        }

        // Búsqueda por JSON name
        // Spatie provides convenient scopes usually, or we search raw JSON.
        // where('name->en', 'like', ...) // Postgres specific ->>
        // Or using whereJsonContains if strict match.
        // Let's use ILIKE on casted text if possible or just rely on 'type' column if it exists (which it should).
        
        return $query->get()->filter(function($p) use ($type) {
             return str_contains(strtolower($p->name), strtolower($type));
        })->first();
    }
}
