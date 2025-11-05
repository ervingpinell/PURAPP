<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourAvailability extends Model
{
    use HasFactory;

    protected $table = 'tour_availability';
    protected $primaryKey = 'availability_id';

    // Descomenta si tu tabla NO tiene created_at / updated_at
    // public $timestamps = false;

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
     * Valores por defecto a nivel modelo (por si vienen nulos del form).
     */
    protected $attributes = [
        'is_active'  => true,
        'is_blocked' => false,
    ];

    /* ===========================
     *          Scopes
     * =========================== */

    /**
     * Overrides activos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Para un tour específico.
     */
    public function scopeForTour($query, int $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    /**
     * Para un horario específico (schedule).
     */
    public function scopeForSchedule($query, ?int $scheduleId)
    {
        return $query->when($scheduleId, fn($q) => $q->where('schedule_id', $scheduleId),
            fn($q) => $q->whereNull('schedule_id'));
    }

    /**
     * Para una fecha específica (YYYY-MM-DD).
     */
    public function scopeOnDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope original tuyo (tour + fecha + opcional schedule).
     * Lo mantengo por compatibilidad.
     */
    public function scopeForDate($query, int $tourId, string $date, ?int $scheduleId = null)
    {
        return $query->where('tour_id', $tourId)
            ->whereDate('date', $date)
            ->when($scheduleId, fn($q) => $q->where('schedule_id', $scheduleId));
    }

    /* ===========================
     *       Relaciones
     * =========================== */

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    /* ===========================
     *        Helpers
     * =========================== */

    /**
     * ¿Este override bloquea completamente el día/horario?
     * max_capacity = 0 también se considera bloqueo efectivo.
     */
    public function blocksAvailability(): bool
    {
        return (bool) $this->is_blocked || (int) $this->max_capacity === 0;
    }

    /**
     * ¿Override general del día (todos los horarios)?
     */
    public function isGeneralDayOverride(): bool
    {
        return is_null($this->schedule_id);
    }

    /**
     * ¿Override específico a un horario?
     */
    public function isScheduleSpecificOverride(): bool
    {
        return !is_null($this->schedule_id);
    }
}
