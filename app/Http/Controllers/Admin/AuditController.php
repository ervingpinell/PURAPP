<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAuditLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AuditController
 *
 * Handles audit operations.
 */
class AuditController extends Controller
{
    /**
     * Mostrar el panel de auditoría con filtros
     */
    public function index(Request $request)
    {
        $query = ProductAuditLog::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        // FILTROS

        // Por producto específico
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Por acción
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Por contexto (wizard, admin, api)
        if ($request->filled('context')) {
            $query->where('context', $request->context);
        }

        // Por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Búsqueda en descripción
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        // Solo acciones del wizard
        if ($request->boolean('wizard_only')) {
            $query->where('context', 'wizard');
        }

        // Solo borradores
        if ($request->boolean('drafts_only')) {
            $query->whereIn('action', ['draft_created', 'draft_continued', 'draft_deleted']);
        }

        // Paginación
        $perPage = $request->input('per_page', 50);
        $logs = $query->paginate($perPage)->withQueryString();

        // Datos para filtros
        $locale = app()->getLocale();
        $products = Product::select('product_id', 'name')
            ->orderByRaw("name->>'$locale' ASC")
            ->get();

        $users = User::select('user_id', 'first_name', 'last_name')
            ->orderByRaw("CONCAT(first_name, ' ', last_name)")
            ->get();

        $actions = ProductAuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $contexts = ProductAuditLog::select('context')
            ->distinct()
            ->whereNotNull('context')
            ->orderBy('context')
            ->pluck('context');

        // Estadísticas rápidas
        $stats = $this->getQuickStats($request);

        return view('admin.audit.index', compact(
            'logs',
            'products',
            'users',
            'actions',
            'contexts',
            'stats'
        ));
    }

    /**
     * Ver detalles de un log específico
     */
    public function show(ProductAuditLog $log)
    {
        $log->load(['product', 'user']);

        // Obtener logs relacionados del mismo producto (contexto)
        $relatedLogs = ProductAuditLog::where('product_id', $log->product_id)
            ->where('audit_id', '!=', $log->audit_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.audit.show', compact('log', 'relatedLogs'));
    }

    /**
     * Ver historial completo de un producto
     */
    public function productHistory(Product $product)
    {
        $logs = ProductAuditLog::where('product_id', $product->product_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        // Timeline visual agrupado por día
        $timeline = $this->buildTimeline($logs);

        return view('admin.audit.product-history', compact('product', 'logs', 'timeline'));
    }

    /**
     * Ver actividad de un usuario
     */
    public function userActivity(User $user)
    {
        $logs = ProductAuditLog::where('user_id', $user->user_id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        // Estadísticas del usuario
        $userStats = [
            'total_actions' => ProductAuditLog::where('user_id', $user->user_id)->count(),
            'products_created' => ProductAuditLog::where('user_id', $user->user_id)
                ->where('action', 'created')
                ->count(),
            'drafts_created' => ProductAuditLog::where('user_id', $user->user_id)
                ->where('action', 'draft_created')
                ->count(),
            'actions_today' => ProductAuditLog::where('user_id', $user->user_id)
                ->whereDate('created_at', today())
                ->count(),
            'most_common_action' => ProductAuditLog::where('user_id', $user->user_id)
                ->select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->orderBy('total', 'desc')
                ->first(),
        ];

        return view('admin.audit.user-activity', compact('user', 'logs', 'userStats'));
    }

    /**
     * Dashboard de estadísticas de auditoría
     */
    public function dashboard(Request $request)
    {
        $days = $request->input('days', 30);

        $stats = [
            // Totales
            'total_logs' => ProductAuditLog::count(),
            'logs_period' => ProductAuditLog::where('created_at', '>=', now()->subDays($days))->count(),

            // Por acción
            'by_action' => ProductAuditLog::getStatsByAction($days),

            // Usuarios más activos
            'most_active_users' => ProductAuditLog::getMostActiveUsers(10, $days),

            // Productos más modificados
            'most_modified_products' => $this->getMostModifiedProducts($days),

            // Actividad por día (últimos 30 días)
            'activity_by_day' => $this->getActivityByDay($days),

            // Contextos
            'by_context' => ProductAuditLog::where('created_at', '>=', now()->subDays($days))
                ->select('context', DB::raw('count(*) as total'))
                ->groupBy('context')
                ->pluck('total', 'context')
                ->toArray(),

            // Drafts
            'drafts_created' => ProductAuditLog::where('created_at', '>=', now()->subDays($days))
                ->where('action', 'draft_created')
                ->count(),
            'drafts_completed' => ProductAuditLog::where('created_at', '>=', now()->subDays($days))
                ->where('action', 'published')
                ->count(),
            'drafts_deleted' => ProductAuditLog::where('created_at', '>=', now()->subDays($days))
                ->where('action', 'draft_deleted')
                ->count(),

            // Actividad reciente
            'recent_activity' => ProductAuditLog::with(['product', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(),
        ];

        return view('admin.audit.dashboard', compact('stats', 'days'));
    }

    /**
     * Exportar logs a CSV
     */
    public function export(Request $request)
    {
        $query = ProductAuditLog::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        // Aplicar los mismos filtros que en index
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Limitar para no sobrecargar
        $logs = $query->limit(10000)->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Encabezados
            fputcsv($file, [
                'ID',
                'Fecha',
                'Producto',
                'Usuario',
                'Acción',
                'Contexto',
                'Paso Wizard',
                'Descripción',
                'IP',
            ]);

            // Datos
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->audit_id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->product?->name ?? 'N/A',
                    $log->user_display_name,
                    $log->action_label,
                    $log->context ?? 'N/A',
                    $log->wizard_step ?? 'N/A',
                    $log->description,
                    $log->ip_address,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Limpiar logs antiguos (solo administradores)
     */
    public function purge(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30',
            'confirm' => 'required|accepted',
        ]);

        $days = $request->days;
        $cutoffDate = now()->subDays($days);

        $deletedCount = ProductAuditLog::where('created_at', '<', $cutoffDate)->delete();

        return redirect()
            ->route('admin.audit.index')
            ->with('success', "Se eliminaron {$deletedCount} registros de auditoría más antiguos que {$days} días.");
    }

    /**
     * MÉTODOS PRIVADOS - HELPERS
     */

    /**
     * Obtener estadísticas rápidas para el header
     */
    private function getQuickStats(Request $request): array
    {
        $baseQuery = ProductAuditLog::query();

        // Aplicar filtros actuales a las estadísticas
        if ($request->filled('product_id')) {
            $baseQuery->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'this_week' => (clone $baseQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'unique_products' => (clone $baseQuery)->distinct('product_id')->count('product_id'),
            'unique_users' => (clone $baseQuery)->distinct('user_id')->count('user_id'),
        ];
    }

    /**
     * Obtener productos más modificados
     */
    private function getMostModifiedProducts(int $days): array
    {
        return ProductAuditLog::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('product_id')
            ->select('product_id', DB::raw('count(*) as modifications_count'))
            ->groupBy('product_id')
            ->orderBy('modifications_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'product' => Product::find($item->product_id),
                    'modifications_count' => $item->modifications_count,
                ];
            })
            ->toArray();
    }

    /**
     * Obtener actividad agrupada por día
     */
    private function getActivityByDay(int $days): array
    {
        return ProductAuditLog::where('created_at', '>=', now()->subDays($days))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('total', 'date')
            ->toArray();
    }

    /**
     * Construir timeline visual de logs
     */
    private function buildTimeline($logs): array
    {
        $timeline = [];

        foreach ($logs as $log) {
            $date = $log->created_at->format('Y-m-d');

            if (!isset($timeline[$date])) {
                $timeline[$date] = [
                    'date' => $log->created_at,
                    'logs' => [],
                ];
            }

            $timeline[$date]['logs'][] = $log;
        }

        return $timeline;
    }
}
