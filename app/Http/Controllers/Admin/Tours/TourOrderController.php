<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\TourType;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * TourOrderController
 *
 * Handles tourorder operations.
 */
class TourOrderController extends Controller
{
    public function index(Request $request)
    {
        // Lista para el <select> de categorías
        $types = TourType::active()
            ->withTranslation()
            ->get(['tour_type_id'])
            ->sortBy('name');

        $selectedId = $request->get('tour_type_id');

        $selected = null;
        $tours    = collect();

        if ($selectedId) {
            $selected = TourType::with('translations')->findOrFail($selectedId);

            // 1) Tours ya ordenados por la relación orderedTours()
            $ordered = $selected->orderedTours()
                ->select(
                    'tours.tour_id',
                    'tours.name',
                    'tours.is_active',
                    'tour_type_tour_order.position'
                )
                ->get();

            // 2) Detectar tours sin fila en la tabla de orden
            $orderedIds = $ordered->pluck('tour_id')->all();

            $missing = Tour::where('tour_type_id', $selected->tour_type_id)
                ->whereNotIn('tour_id', $orderedIds)
                ->orderBy('name') // quedarán al final en la vista
                ->get(['tour_id', 'name', 'is_active'])
                ->map(function ($t) {
                    $t->position = null; // marca visual para "sin posición"
                    return $t;
                });

            // 3) Unir: primero ordenados (con position), luego faltantes
            $tours = $ordered->concat($missing);
        }

        return view('admin.tours.order.index', compact('types', 'selected', 'tours'));
    }

    public function save(Request $request, TourType $tourType)
    {
        $data = $request->validate([
            'order'   => ['required', 'array', 'min:1'],
            'order.*' => ['integer', 'distinct'], // IDs de tour en el orden deseado
        ]);

        $order = $data['order'];

        DB::transaction(function () use ($order, $tourType) {
            // Limitar a tours que realmente pertenecen a esa categoría
            $validIds = Tour::where('tour_type_id', $tourType->tour_type_id)
                ->whereIn('tour_id', $order)
                ->pluck('tour_id')
                ->all();

            // Reasignar posiciones secuenciales
            foreach ($order as $idx => $tourId) {
                if (!in_array($tourId, $validIds, true)) {
                    continue;
                }

                DB::table('tour_type_tour_order')->updateOrInsert(
                    [
                        'tour_type_id' => $tourType->tour_type_id,
                        'tour_id'      => $tourId,
                    ],
                    [
                        'position'   => $idx + 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            // (Opcional) Limpiar filas de tours que ya no están en esta categoría
            DB::table('tour_type_tour_order')
                ->where('tour_type_id', $tourType->tour_type_id)
                ->whereNotIn('tour_id', $validIds)
                ->delete();
        });

        return response()->json(['ok' => true]);
    }
}
