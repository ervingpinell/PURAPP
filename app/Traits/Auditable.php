<?php

namespace App\Traits;

use App\Models\TourAuditLog;

/**
 * Trait Auditable
 *
 * Agrega funcionalidad de auditoría automática a cualquier modelo.
 *
 * USO:
 * 1. Agrega el trait al modelo Tour: use Auditable;
 * 2. Los cambios se registrarán automáticamente
 *
 * PERSONALIZACIÓN:
 * - Define $auditableFields para limitar qué campos auditar
 * - Define $auditExclude para excluir campos específicos
 */
trait Auditable
{
    /**
     * Boot del trait - registrar observers
     */
    protected static function bootAuditable(): void
    {
        // Al crear
        static::created(function ($model) {
            $model->auditCreated();
        });

        // Al actualizar
        static::updated(function ($model) {
            $model->auditUpdated();
        });

        // Al eliminar
        static::deleted(function ($model) {
            $model->auditDeleted();
        });

        // Al restaurar (si usa SoftDeletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->auditRestored();
            });
        }
    }

    /**
     * Auditar creación
     */
    protected function auditCreated(): void
    {
        $action = $this->is_draft ? 'draft_created' : 'created';

        TourAuditLog::logAction(
            action: $action,
            tourId: $this->getKey(),
            newValues: $this->getAuditableData(),
            description: "Tour '{$this->name}' fue creado",
            context: $this->getAuditContext(),
            wizardStep: $this->current_step ?? null
        );
    }

    /**
     * Auditar actualización
     */
    protected function auditUpdated(): void
    {
        // Solo auditar si hubo cambios reales
        if (!$this->wasChanged()) {
            return;
        }

        $changes = $this->getChanges();
        $original = $this->getOriginal();

        // Filtrar solo campos auditables
        $auditableChanges = $this->filterAuditableFields($changes);

        if (empty($auditableChanges)) {
            return;
        }

        // Determinar el tipo de acción específica
        $action = $this->determineUpdateAction($changes);

        $oldValues = [];
        $newValues = [];

        foreach (array_keys($auditableChanges) as $field) {
            $oldValues[$field] = $original[$field] ?? null;
            $newValues[$field] = $changes[$field];
        }

        TourAuditLog::logAction(
            action: $action,
            tourId: $this->getKey(),
            oldValues: $oldValues,
            newValues: $newValues,
            description: $this->generateUpdateDescription($auditableChanges),
            context: $this->getAuditContext(),
            wizardStep: $this->current_step ?? null
        );
    }

    /**
     * Auditar eliminación
     */
    protected function auditDeleted(): void
    {
        $action = $this->is_draft ? 'draft_deleted' : 'deleted';

        TourAuditLog::logAction(
            action: $action,
            tourId: $this->getKey(),
            oldValues: $this->getAuditableData(),
            description: "Tour '{$this->name}' fue eliminado",
            context: $this->getAuditContext()
        );
    }

    /**
     * Auditar restauración
     */
    protected function auditRestored(): void
    {
        TourAuditLog::logAction(
            action: 'restored',
            tourId: $this->getKey(),
            newValues: $this->getAuditableData(),
            description: "Tour '{$this->name}' fue restaurado",
            context: $this->getAuditContext()
        );
    }

    /**
     * Obtener datos auditables del modelo
     */
    protected function getAuditableData(): array
    {
        $data = $this->getAttributes();
        return $this->filterAuditableFields($data);
    }

    /**
     * Filtrar solo campos auditables
     */
    protected function filterAuditableFields(array $data): array
    {
        // Si se definió una lista específica de campos auditables, usarla
        if (property_exists($this, 'auditableFields') && !empty($this->auditableFields)) {
            $data = array_intersect_key($data, array_flip($this->auditableFields));
        }

        // Excluir campos que no deben auditarse
        $excludeFields = array_merge(
            ['created_at', 'updated_at', 'deleted_at'], // Timestamps
            property_exists($this, 'auditExclude') ? $this->auditExclude : []
        );

        return array_diff_key($data, array_flip($excludeFields));
    }

    /**
     * Determinar tipo específico de acción en una actualización
     */
    protected function determineUpdateAction(array $changes): string
    {
        // Publicación/despublicación
        if (isset($changes['is_active'])) {
            return $changes['is_active'] ? 'published' : 'unpublished';
        }

        // Cambio en paso del wizard
        if (isset($changes['current_step'])) {
            return 'step_completed';
        }

        // Actualización de borrador
        if ($this->is_draft) {
            return 'updated';
        }

        // Actualización normal
        return 'updated';
    }

    /**
     * Generar descripción automática de los cambios
     */
    protected function generateUpdateDescription(array $changes): string
    {
        $changedFields = array_keys($changes);
        $fieldCount = count($changedFields);

        if ($fieldCount === 0) {
            return "Tour '{$this->name}' fue actualizado";
        }

        if ($fieldCount === 1) {
            $field = $this->getFieldLabel($changedFields[0]);
            return "Se actualizó el campo '{$field}' del tour '{$this->name}'";
        }

        return "Se actualizaron {$fieldCount} campos del tour '{$this->name}'";
    }

    /**
     * Obtener etiqueta legible de un campo
     */
    protected function getFieldLabel(string $field): string
    {
        $labels = [
            'name' => 'Nombre',
            'slug' => 'URL',
            'overview' => 'Descripción',
            'is_active' => 'Estado',
            'is_draft' => 'Borrador',
            'current_step' => 'Paso del Wizard',
            'max_capacity' => 'Capacidad Máxima',
            'tour_type_id' => 'Tipo de Tour',
            'length' => 'Duración',
            // Agrega más según necesites
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Determinar contexto de la auditoría
     */
    protected function getAuditContext(): string
    {
        $url = request()->path();

        if (str_contains($url, 'wizard')) {
            return 'wizard';
        }

        if (str_contains($url, 'api')) {
            return 'api';
        }

        if (str_contains($url, 'admin')) {
            return 'admin';
        }

        return 'web';
    }

    /**
     * MÉTODOS PÚBLICOS PARA USAR EN EL MODELO
     */

    /**
     * Registrar acción personalizada de auditoría
     */
    public function logAuditAction(
        string $action,
        ?string $description = null,
        ?array $additionalData = null
    ): TourAuditLog {
        return TourAuditLog::logAction(
            action: $action,
            tourId: $this->getKey(),
            newValues: $additionalData,
            description: $description ?? "Acción '{$action}' ejecutada en tour '{$this->name}'",
            context: $this->getAuditContext()
        );
    }

    /**
     * Obtener historial de auditoría del modelo
     */
    public function auditLogs()
    {
        return $this->hasMany(TourAuditLog::class, 'product_id', 'product_id')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Obtener último log de auditoría
     */
    public function lastAuditLog()
    {
        return $this->hasOne(TourAuditLog::class, 'product_id', 'product_id')
                    ->latest('created_at');
    }

    /**
     * Verificar si el modelo fue modificado recientemente
     */
    public function wasRecentlyModified(int $minutes = 60): bool
    {
        $lastLog = $this->lastAuditLog;

        if (!$lastLog) {
            return false;
        }

        return $lastLog->created_at->diffInMinutes(now()) <= $minutes;
    }

    /**
     * Obtener usuario que creó el modelo
     */
    public function getCreatorAttribute()
    {
        return $this->auditLogs()
                    ->where('action', 'created')
                    ->with('user')
                    ->first()?->user;
    }

    /**
     * Obtener usuario que hizo la última modificación
     */
    public function getLastModifierAttribute()
    {
        return $this->lastAuditLog?->user;
    }
}
