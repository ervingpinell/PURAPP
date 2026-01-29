<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ProductAvailability Model
 *
 * Defines product availability by day of week.
 */
class ProductAvailability extends Model
{
    use HasFactory;

    protected $table = 'product_availability';
    protected $primaryKey = 'availability_id';

    protected $fillable = [
        'product_id',
        'schedule_id',
        'date',
        'max_capacity',
        'is_blocked',
        'is_active',
    ];

    protected $casts = [
        'date'         => 'date',
        'max_capacity' => 'integer',
        'is_blocked'   => 'boolean',
        'is_active'    => 'boolean',
    ];

    protected $attributes = [
        'is_active'  => true,
        'is_blocked' => false,
    ];

    /* ===========================
     *          Scopes
     * =========================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForSchedule($query, ?int $scheduleId)
    {
        return $query->when($scheduleId, fn($q) => $q->where('schedule_id', $scheduleId),
            fn($q) => $q->whereNull('schedule_id'));
    }

    public function scopeOnDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    // Backward compatibility alias
    public function scopeForProduct($query, int $productId)
    {
        return $this->scopeForProduct($query, $productId);
    }

    public function scopeForDate($query, int $productId, string $date, ?int $scheduleId = null)
    {
        return $query->where('product_id', $productId)
            ->whereDate('date', $date)
            ->when($scheduleId, fn($q) => $q->where('schedule_id', $scheduleId));
    }

    /* ===========================
     *       Relaciones
     * =========================== */

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    /* ===========================
     *        Helpers
     * =========================== */

    public function blocksAvailability(): bool
    {
        return (bool) $this->is_blocked || (int) $this->max_capacity === 0;
    }

    public function isGeneralDayOverride(): bool
    {
        return is_null($this->schedule_id);
    }

    public function isScheduleSpecificOverride(): bool
    {
        return !is_null($this->schedule_id);
    }
}
