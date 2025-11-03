<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourAvailability extends Model
{
    use HasFactory;

    protected $table = 'tour_availability';
    protected $primaryKey = 'availability_id';

    protected $fillable = [
        'tour_id',
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

    /**
     * Scope para obtener overrides activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para un tour y fecha específica
     */
    public function scopeForDate($query, int $tourId, string $date, ?int $scheduleId = null)
    {
        return $query->where('tour_id', $tourId)
            ->whereDate('date', $date)
            ->when($scheduleId, fn($q) => $q->where('schedule_id', $scheduleId));
    }

    /**
     * Relaciones
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    /**
     * Helper: ¿Este override bloquea completamente el día/horario?
     */
    public function blocksAvailability(): bool
    {
        return $this->is_blocked || $this->max_capacity === 0;
    }

    /**
     * Helper: ¿Es un override general del día (todos los horarios)?
     */
    public function isGeneralDayOverride(): bool
    {
        return is_null($this->schedule_id);
    }

    /**
     * Helper: ¿Es un override específico de un horario?
     */
    public function isScheduleSpecificOverride(): bool
    {
        return !is_null($this->schedule_id);
    }
}
