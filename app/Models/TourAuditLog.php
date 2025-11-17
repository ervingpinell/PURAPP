<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourAuditLog extends Model
{
    protected $table = 'tour_audit_logs';
    protected $primaryKey = 'audit_id';

    protected $fillable = [
        'tour_id',
        'user_id',
        'user_name',
        'user_email',
        'action',
        'context',
        'wizard_step',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'description',
        'tags',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el tour
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * SCOPES - Para búsquedas fáciles
     */

    public function scopeByTour($query, $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByContext($query, $context)
    {
        return $query->where('context', $context);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeDraftActions($query)
    {
        return $query->whereIn('action', ['draft_created', 'draft_continued', 'draft_deleted']);
    }

    public function scopeWizardActions($query)
    {
        return $query->where('context', 'wizard');
    }

    /**
     * MÉTODOS HELPER
     */

    /**
     * Obtener descripción legible de la acción
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'created' => 'Tour Creado',
            'updated' => 'Tour Actualizado',
            'deleted' => 'Tour Eliminado',
            'restored' => 'Tour Restaurado',
            'published' => 'Tour Publicado',
            'unpublished' => 'Tour Despublicado',
            'draft_created' => 'Borrador Creado',
            'draft_continued' => 'Borrador Continuado',
            'draft_deleted' => 'Borrador Eliminado',
            'step_completed' => 'Paso Completado',
            'bulk_action' => 'Acción Masiva',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Obtener el nombre del usuario (de la relación o del campo guardado)
     */
    public function getUserDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->user_name ?? 'Usuario Desconocido';
    }

    /**
     * Obtener badge color según la acción
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'restored' => 'warning',
            'published' => 'success',
            'unpublished' => 'secondary',
            'draft_created' => 'primary',
            'draft_continued' => 'info',
            'draft_deleted' => 'warning',
            'step_completed' => 'info',
            'bulk_action' => 'dark',
        ];

        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Verificar si el log tiene cambios de valores
     */
    public function hasValueChanges(): bool
    {
        return !empty($this->changed_fields) && count($this->changed_fields) > 0;
    }

    /**
     * Obtener resumen de cambios en formato legible
     */
    public function getChangesSummary(): array
    {
        if (!$this->hasValueChanges()) {
            return [];
        }

        $summary = [];

        foreach ($this->changed_fields as $field) {
            $summary[] = [
                'field' => $this->getFieldLabel($field),
                'old' => $this->old_values[$field] ?? null,
                'new' => $this->new_values[$field] ?? null,
            ];
        }

        return $summary;
    }

    /**
     * Obtener etiqueta legible del campo
     */
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'name' => 'Nombre',
            'slug' => 'URL',
            'overview' => 'Descripción',
            'is_active' => 'Estado',
            'is_draft' => 'Borrador',
            'current_step' => 'Paso Actual',
            'max_capacity' => 'Capacidad Máxima',
            'tour_type_id' => 'Tipo de Tour',
            'length' => 'Duración',
            // Agrega más según tus campos
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * MÉTODO ESTÁTICO: Crear log de auditoría fácilmente
     */
    public static function logAction(
        string $action,
        ?int $tourId = null,
        ?int $userId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?string $context = null,
        ?int $wizardStep = null,
        ?array $tags = null
    ): self {
        // Obtener info del usuario actual si no se provee
        if ($userId === null && auth()->check()) {
            $userId = auth()->id();
        }

        $user = $userId ? \App\Models\User::find($userId) : null;

        // Calcular campos que cambiaron
        $changedFields = [];
        if ($oldValues && $newValues) {
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
        }

        return self::create([
            'tour_id' => $tourId,
            'user_id' => $userId,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'action' => $action,
            'context' => $context ?? (request()->is('*/wizard/*') ? 'wizard' : 'admin'),
            'wizard_step' => $wizardStep,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'description' => $description,
            'tags' => $tags,
        ]);
    }

    /**
     * ESTADÍSTICAS
     */
    public static function getStatsByAction(int $days = 30): array
    {
        return self::where('created_at', '>=', now()->subDays($days))
            ->select('action', \DB::raw('count(*) as total'))
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->pluck('total', 'action')
            ->toArray();
    }

    public static function getMostActiveUsers(int $limit = 10, int $days = 30): array
    {
        return self::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('user_id')
            ->select('user_id', 'user_name', \DB::raw('count(*) as actions_count'))
            ->groupBy('user_id', 'user_name')
            ->orderBy('actions_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
