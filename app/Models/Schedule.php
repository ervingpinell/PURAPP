<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Schedule Model
 *
 * Represents a time slot/schedule for tours.
 * Tours can have multiple schedules (morning, afternoon, etc.).
 *
 * @property int $schedule_id Primary key
 * @property string $start_time Start time (HH:MM:SS)
 * @property string|null $end_time End time (HH:MM:SS)
 * @property string|null $label Display label (e.g., "Morning Tour")
 * @property bool $is_active Schedule active status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $deleted_by
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Tour[] $tours
 * @property-read User|null $deletedBy
 */
class Schedule extends Model
{
    use SoftDeletes;

    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    public function getRouteKeyName()
    {
        return 'schedule_id';
    }

    protected $fillable = [
        'start_time',
        'end_time',
        'label',
        'is_active',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function startTime(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (is_string($value) && preg_match('/^\d{2}:\d{2}$/', $value)) {
                    return $value . ':00';
                }
                return $value;
            }
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (is_string($value) && preg_match('/^\d{2}:\d{2}$/', $value)) {
                    return $value . ':00';
                }
                return $value;
            }
        );
    }

    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'schedule_tour',
            'schedule_id',
            'tour_id'
        )
            ->withPivot(['is_active', 'base_capacity', 'cutoff_hour', 'lead_days'])
            ->withTimestamps();
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for finding schedules deleted older than days
     */
    public function scopeOlderThan($query, $days)
    {
        return $query->onlyTrashed()->where('deleted_at', '<', now()->subDays($days));
    }
}
