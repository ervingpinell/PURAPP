<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProductAuditLog Model
 *
 * Tracks changes made to products.
 */
class ProductAuditLog extends Model
{
    protected $table = 'product_audit_logs';
    protected $primaryKey = 'audit_id'; // Migration renamed 'tour_audit_log' to 'product_audit_log', keeping PK same? 
    // Migration part 1 did NOT rename 'product_audit_log' PK. It renamed table.
    // Assuming PK is 'audit_id' as before.

    protected $fillable = [
        'product_id',
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
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
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

    public function getActionLabelAttribute(): string
    {
        $labels = [
            'created' => 'Producto Creado',
            'updated' => 'Producto Actualizado',
            'deleted' => 'Producto Eliminado',
            'restored' => 'Producto Restaurado',
            'published' => 'Producto Publicado',
            'unpublished' => 'Producto Despublicado',
            'draft_created' => 'Borrador Creado',
            'draft_continued' => 'Borrador Continuado',
            'draft_deleted' => 'Borrador Eliminado',
            'step_completed' => 'Paso Completado',
            'bulk_action' => 'Acción Masiva',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    public function getUserDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->user_name ?? 'Usuario Desconocido';
    }

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

    public function hasValueChanges(): bool
    {
        return !empty($this->changed_fields) && count($this->changed_fields) > 0;
    }

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
            'product_type_id' => 'Tipo de Producto',
            'length' => 'Duración',
            'product_category' => 'Categoría',
            'allow_custom_time' => 'Hora Personalizable',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    public static function logAction(
        string $action,
        ?int $productId = null,
        ?int $userId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?string $context = null,
        ?int $wizardStep = null,
        ?array $tags = null
    ): self {
        if ($userId === null && auth()->check()) {
            $userId = auth()->id();
        }

        $user = $userId ? \App\Models\User::find($userId) : null;

        $changedFields = [];
        if ($oldValues && $newValues) {
            foreach ($newValues as $key => $value) {
                if (array_key_exists($key, $oldValues)) {
                    $old = $oldValues[$key];
                    // Skip if identical
                    if ($old === $value) continue;
                    
                    // Handle array comparisons
                    if (is_array($old) || is_array($value)) {
                         if (json_encode($old) !== json_encode($value)) {
                             $changedFields[] = $key;
                         }
                    } else {
                        // Simple comparison
                        if ($old != $value) {
                            $changedFields[] = $key;
                        }
                    }
                }
            }
        }

        return self::create([
            'product_id' => $productId,
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
