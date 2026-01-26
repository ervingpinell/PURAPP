<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\TourAuditLog;

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
     * Handle el evento "created" del tour
     * Se ejecuta DESPUÉS de crear
     */
    public function created(Product $tour): void
    {
        // La auditoría básica la maneja el trait Auditable
        // Aquí agregamos lógica adicional si es necesario

        // Si es un draft del wizard, registrar el paso inicial
        if ($tour->is_draft && request()->is('*/wizard/*')) {
            TourAuditLog::logAction(
                action: 'draft_created',
                tourId: $tour->product_id,
                description: "Borrador de tour '{$tour->name}' creado en wizard (Paso 1)",
                context: 'wizard',
                wizardStep: 1,
                tags: ['wizard', 'draft', 'step-1']
            );
        }
    }

    /**
     * Handle el evento "updated" del tour
     */
    public function updated(Product $tour): void
    {
        // El trait Auditable ya maneja la auditoría básica
        // Aquí agregamos logs especiales para cambios importantes

        // Si cambió de draft a publicado
        if ($tour->wasChanged('is_draft') && !$tour->is_draft) {
            TourAuditLog::logAction(
                action: 'published',
                tourId: $tour->product_id,
                description: "Tour '{$tour->name}' fue publicado (ya no es borrador)",
                context: $this->getContext(),
                tags: ['published', 'completed']
            );
        }

        // Si completó un paso del wizard
        if ($tour->wasChanged('current_step') && $tour->is_draft) {
            $newStep = $tour->current_step;
            $oldStep = $tour->getOriginal('current_step');

            TourAuditLog::logAction(
                action: 'step_completed',
                tourId: $tour->product_id,
                description: "Paso {$oldStep} completado, avanzando a paso {$newStep} del wizard",
                context: 'wizard',
                wizardStep: $newStep,
                tags: ['wizard', "step-{$newStep}"]
            );
        }

        // Si cambió el estado activo/inactivo
        if ($tour->wasChanged('is_active')) {
            $action = $tour->is_active ? 'published' : 'unpublished';
            $status = $tour->is_active ? 'activado' : 'desactivado';

            TourAuditLog::logAction(
                action: $action,
                tourId: $tour->product_id,
                description: "Tour '{$tour->name}' fue {$status}",
                context: $this->getContext(),
                tags: [$action, 'status-change']
            );
        }
    }

    /**
     * Handle el evento "deleting" del tour
     * Se ejecuta ANTES de eliminar
     */
    public function deleting(Product $tour): void
    {
        // Aquí puedes agregar lógica ANTES de eliminar
        // Por ejemplo, validaciones o limpieza de datos relacionados
    }

    /**
     * Handle el evento "deleted" del tour
     */
    public function deleted(Product $tour): void
    {
        // El trait Auditable ya maneja el log básico
        // Podemos agregar información adicional

        $isDraft = $tour->is_draft;
        $action = $isDraft ? 'draft_deleted' : 'deleted';
        $type = $isDraft ? 'borrador' : 'tour publicado';

        TourAuditLog::logAction(
            action: $action,
            tourId: $tour->product_id,
            description: "El {$type} '{$tour->name}' fue eliminado",
            context: $this->getContext(),
            tags: ['deleted', $isDraft ? 'draft' : 'published']
        );
    }

    /**
     * Handle el evento "restored" del tour (si usa SoftDeletes)
     */
    public function restored(Product $tour): void
    {
        TourAuditLog::logAction(
            action: 'restored',
            tourId: $tour->product_id,
            description: "Tour '{$tour->name}' fue restaurado",
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
