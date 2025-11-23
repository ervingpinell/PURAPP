<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'label',
        'description',
        'validation_rules',
        'is_public',
        'sort_order',
        'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
        'validation_rules' => 'array',
    ];

    /**
     * Accessor: Convert value based on type
     */
    public function getValueAttribute($value)
    {
        return match ($this->attributes['type'] ?? 'string') {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            'email' => $value,
            default => $value,
        };
    }

    /**
     * Mutator: Store value based on type
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match ($this->attributes['type'] ?? 'string') {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }

    /**
     * Boot method to clear cache on save
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }

    /**
     * Scope: Filter by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Only public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Relationship: User who last updated
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
