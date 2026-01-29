<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductAuditLog;

/**
 * ProductObserver
 *
 * Observer específico para Product que complementa el trait Auditable
 * con lógica de negocio específica del wizard y otras acciones complejas.
 *
 * REGISTRO en AppServiceProvider:
 * Product::observe(ProductObserver::class);
 */
class ProductObserver
{
    /**
     * Handle el evento "saving" del product
     * Se ejecuta ANTES de guardar (create o update)
     */
    public function saving(Product $product): void
    {
        // Auto-asignar created_by si es nuevo product
        if (!$product->exists && !$product->created_by && auth()->check()) {
            $product->created_by = auth()->id();
        }

        // Auto-asignar updated_by siempre que haya cambios
        if ($product->isDirty() && auth()->check()) {
            $product->updated_by = auth()->id();
        }
    }

    /**
     * Handle el evento "created" del producto
     * Se ejecuta DESPUÉS de crear
     */
    public function created(Product $product): void
    {
        // La auditoría básica la maneja el trait Auditable
        // Aquí agregamos lógica adicional si es necesario

        // Si es un draft del wizard, registrar el paso inicial
        if ($product->is_draft && request()->is('*/wizard/*')) {
            ProductAuditLog::logAction(
                action: 'draft_created',
                productId: $product->product_id,
                description: "Borrador de producto '{$product->name}' creado en wizard (Paso 1)",
                context: 'wizard',
                wizardStep: 1,
                tags: ['wizard', 'draft', 'step-1']
            );
        }
    }

    /**
     * Handle el evento "updated" del producto
     */
    public function updated(Product $product): void
    {
        // El trait Auditable ya maneja la auditoría básica
        // Aquí agregamos logs especiales para cambios importantes

        // Si cambió de draft a publicado
        if ($product->wasChanged('is_draft') && !$product->is_draft) {
            ProductAuditLog::logAction(
                action: 'published',
                productId: $product->product_id,
                description: "Producto '{$product->name}' fue publicado (ya no es borrador)",
                context: $this->getContext(),
                tags: ['published', 'completed']
            );
        }

        // Si completó un paso del wizard
        if ($product->wasChanged('current_step') && $product->is_draft) {
            $newStep = $product->current_step;
            $oldStep = $product->getOriginal('current_step');

            ProductAuditLog::logAction(
                action: 'step_completed',
                productId: $product->product_id,
                description: "Paso {$oldStep} completado, avanzando a paso {$newStep} del wizard",
                context: 'wizard',
                wizardStep: $newStep,
                tags: ['wizard', "step-{$newStep}"]
            );
        }

        // Si cambió el estado activo/inactivo
        if ($product->wasChanged('is_active')) {
            $action = $product->is_active ? 'published' : 'unpublished';
            $status = $product->is_active ? 'activado' : 'desactivado';

            ProductAuditLog::logAction(
                action: $action,
                productId: $product->product_id,
                description: "Producto '{$product->name}' fue {$status}",
                context: $this->getContext(),
                tags: [$action, 'status-change']
            );
        }
    }

    /**
     * Handle el evento "deleting" del producto
     * Se ejecuta ANTES de eliminar
     */
    public function deleting(Product $product): void
    {
        // Aquí puedes agregar lógica ANTES de eliminar
        // Por ejemplo, validaciones o limpieza de datos relacionados
    }

    /**
     * Handle el evento "deleted" del producto
     */
    public function deleted(Product $product): void
    {
        // El trait Auditable ya maneja el log básico
        // Podemos agregar información adicional

        $isDraft = $product->is_draft;
        $action = $isDraft ? 'draft_deleted' : 'deleted';
        $type = $isDraft ? 'borrador' : 'producto publicado';

        ProductAuditLog::logAction(
            action: $action,
            productId: $product->product_id,
            description: "El {$type} '{$product->name}' fue eliminado",
            context: $this->getContext(),
            tags: ['deleted', $isDraft ? 'draft' : 'published']
        );
    }

    /**
     * Handle el evento "restored" del producto (si usa SoftDeletes)
     */
    public function restored(Product $product): void
    {
        ProductAuditLog::logAction(
            action: 'restored',
            productId: $product->product_id,
            description: "Producto '{$product->name}' fue restaurado",
            context: $this->getContext(),
            tags: ['restored', 'recovered']
        );
    }

    /**
     * Helper: Determinar el contexto actual
     */
    private function getContext(): string
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
}
