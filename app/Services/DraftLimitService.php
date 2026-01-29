<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;

/**
 * DraftLimitService
 *
 * Servicio para gestionar límites de drafts por usuario
 * y proporcionar sugerencias de limpieza.
 */
class DraftLimitService
{
    /**
     * Límite máximo de drafts por usuario (configurable)
     */
    private int $maxDrafts;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Obtener de config o usar valor por defecto
        $this->maxDrafts = config('products.max_drafts_per_user', 5);
    }

    /**
     * Verificar si un usuario ha alcanzado el límite de drafts
     */
    public function hasReachedLimit(int $userId): bool
    {
        $count = $this->getUserDraftsCount($userId);
        return $count >= $this->maxDrafts;
    }

    /**
     * Obtener cantidad de drafts de un usuario
     */
    public function getUserDraftsCount(int $userId): int
    {
        return Product::where('is_draft', true)
            ->where('created_by', $userId)
            ->count();
    }

    /**
     * Obtener drafts de un usuario
     */
    public function getUserDrafts(int $userId)
    {
        return Product::where('is_draft', true)
            ->where('created_by', $userId)
            ->with(['productType', 'languages'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Verificar cuántos drafts más puede crear el usuario
     */
    public function getRemainingSlots(int $userId): int
    {
        $current = $this->getUserDraftsCount($userId);
        $remaining = $this->maxDrafts - $current;

        return max(0, $remaining);
    }

    /**
     * Obtener el límite máximo configurado
     */
    public function getMaxDrafts(): int
    {
        return $this->maxDrafts;
    }

    /**
     * Obtener drafts sugeridos para eliminar (los más antiguos)
     */
    public function getSuggestedDraftsToDelete(int $userId, int $count = 3)
    {
        return Product::where('is_draft', true)
            ->where('created_by', $userId)
            ->orderBy('updated_at', 'asc')  // Más antiguos primero
            ->limit($count)
            ->get();
    }

    /**
     * Verificar si puede crear un nuevo draft
     * Retorna true si puede, o un array con info de error si no puede
     */
    public function canCreateDraft(int $userId): bool|array
    {
        if (!$this->hasReachedLimit($userId)) {
            return true;
        }

        $drafts = $this->getUserDrafts($userId);
        $suggested = $this->getSuggestedDraftsToDelete($userId);

        return [
            'error' => true,
            'message' => "Has alcanzado el límite de {$this->maxDrafts} borradores.",
            'current_count' => $drafts->count(),
            'max_allowed' => $this->maxDrafts,
            'suggested_to_delete' => $suggested,
            'all_drafts' => $drafts,
        ];
    }

    /**
     * Obtener mensaje de advertencia cuando se está cerca del límite
     */
    public function getWarningMessage(int $userId): ?string
    {
        $remaining = $this->getRemainingSlots($userId);

        if ($remaining === 0) {
            return "Has alcanzado el límite de {$this->maxDrafts} borradores. Elimina algunos para crear nuevos.";
        }

        if ($remaining <= 2) {
            return "Te quedan solo {$remaining} espacio(s) para borradores. Considera completar o eliminar algunos.";
        }

        return null;
    }

    /**
     * Obtener estadísticas de drafts por usuario
     */
    public function getUserStats(int $userId): array
    {
        $drafts = $this->getUserDrafts($userId);

        return [
            'total_drafts' => $drafts->count(),
            'max_allowed' => $this->maxDrafts,
            'remaining_slots' => $this->getRemainingSlots($userId),
            'oldest_draft' => $drafts->sortBy('updated_at')->first(),
            'newest_draft' => $drafts->sortByDesc('updated_at')->first(),
            'by_step' => $drafts->groupBy('current_step')->map->count()->toArray(),
            'average_age_days' => $drafts->avg(fn($d) => $d->updated_at->diffInDays(now())),
        ];
    }

    /**
     * Verificar si el usuario debería recibir una advertencia
     */
    public function shouldShowWarning(int $userId): bool
    {
        $remaining = $this->getRemainingSlots($userId);
        return $remaining <= 2 && $remaining > 0;
    }

    /**
     * Obtener usuarios que han alcanzado el límite
     */
    public function getUsersAtLimit(): array
    {
        $usersWithDrafts = Product::where('is_draft', true)
            ->whereNotNull('created_by')
            ->select('created_by')
            ->selectRaw('count(*) as drafts_count')
            ->groupBy('created_by')
            ->having('drafts_count', '>=', $this->maxDrafts)
            ->get();

        $result = [];

        foreach ($usersWithDrafts as $item) {
            $user = User::find($item->created_by);
            if ($user) {
                $result[] = [
                    'user' => $user,
                    'drafts_count' => $item->drafts_count,
                    'over_limit' => $item->drafts_count - $this->maxDrafts,
                ];
            }
        }

        return $result;
    }

    /**
     * Limpiar automáticamente drafts muy antiguos de un usuario
     * (solo los que tengan más de X días configurados)
     */
    public function autoCleanOldDrafts(int $userId, int $daysOld = 60): int
    {
        $cutoffDate = now()->subDays($daysOld);

        $oldDrafts = Product::where('is_draft', true)
            ->where('created_by', $userId)
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        $deletedCount = 0;

        foreach ($oldDrafts as $draft) {
            // Eliminar relaciones
            $draft->languages()->detach();
            $draft->amenities()->detach();
            $draft->schedules()->detach();
            $draft->prices()->delete();

            if ($draft->itinerary_id && $draft->itinerary) {
                $draft->itinerary->delete();
            }

            $draft->forceDelete();
            $deletedCount++;
        }

        return $deletedCount;
    }

    /**
     * Verificar la salud general del sistema de drafts
     */
    public function getSystemHealth(): array
    {
        $totalDrafts = Product::where('is_draft', true)->count();
        $totalUsers = User::count();
        $usersWithDrafts = Product::where('is_draft', true)
            ->whereNotNull('created_by')
            ->distinct('created_by')
            ->count('created_by');

        $oldDrafts = Product::where('is_draft', true)
            ->where('updated_at', '<', now()->subDays(30))
            ->count();

        $usersAtLimit = count($this->getUsersAtLimit());

        return [
            'total_drafts' => $totalDrafts,
            'users_with_drafts' => $usersWithDrafts,
            'users_at_limit' => $usersAtLimit,
            'old_drafts_30d' => $oldDrafts,
            'average_per_user' => $usersWithDrafts > 0 ? round($totalDrafts / $usersWithDrafts, 2) : 0,
            'max_allowed' => $this->maxDrafts,
            'health_status' => $this->calculateHealthStatus($totalDrafts, $usersAtLimit, $oldDrafts),
        ];
    }

    /**
     * Calcular el estado de salud del sistema
     */
    private function calculateHealthStatus(int $totalDrafts, int $usersAtLimit, int $oldDrafts): string
    {
        if ($usersAtLimit > 10 || $oldDrafts > 50) {
            return 'critical';
        }

        if ($usersAtLimit > 5 || $oldDrafts > 20) {
            return 'warning';
        }

        return 'healthy';
    }
}
