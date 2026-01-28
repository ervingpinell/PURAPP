<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LoggerHelper;

/**
 * TourOrderController
 *
 * Handles tourorder operations.
 */
class ProductOrderController extends Controller
{
    protected string $controller = 'TourOrderController';

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        
        // Lista para el <select> de categorías
        $types = ProductType::active()
            ->get(['product_type_id', 'name'])
            ->sortBy('name');

        // Allow both parameters for compatibility
        $selectedId = $request->get('product_type_id') ?? $request->get('tour_type_id');

        $selected = null;
        $tours    = collect();

        if ($selectedId) {
            $selected = ProductType::findOrFail($selectedId);

            // 1) Tours ya ordenados por la relación orderedProducts()
            $ordered = $selected->orderedProducts()
                ->select(
                    'product2.product_id', 
                    'product2.name',
                    'product2.is_active',
                    'tour_type_tour_order.position'
                )
                ->get();

            // 2) Detectar tours sin fila en la tabla de orden
            $orderedIds = $ordered->pluck('product_id')->all();

            $missing = Product::where('product_type_id', $selected->product_type_id)
                ->whereNotIn('product_id', $orderedIds)
                ->orderByRaw("name->>'$locale' ASC") // quedarán al final en la vista
                ->get(['product_id', 'name', 'is_active'])
                ->map(function ($t) {
                    $t->position = null; // marca visual para "sin posición"
                    return $t;
                });

            // 3) Unir: primero ordenados (con position), luego faltantes
            $tours = $ordered->concat($missing);
        }

        // rename tours to products for view compatibility if needed, but view uses products
        // Wait, the view says @foreach ($products ...). 
        // The original controller returned compact('tours'). 
        // I need to correct the variable name in compact or in the view.
        // The view I just edited has @foreach ($products ...). 
        // Let's pass 'products' instead of 'tours'.
        $products = $tours;

        return view('admin.products.order.index', compact('types', 'selected', 'products'));
    }

    public function save(Request $request, ProductType $tourType)
    {
        try {
            $data = $request->validate([
                'order'   => ['required', 'array', 'min:1'],
                'order.*' => ['integer', 'distinct'], // IDs de tour en el orden deseado
            ]);

            $order = $data['order'];

            DB::transaction(function () use ($order, $tourType) {
                // Limitar a tours que realmente pertenecen a esa categoría
                $validIds = Product::where('product_type_id', $tourType->product_type_id)
                    ->whereIn('product_id', $order)
                    ->pluck('product_id')
                    ->all();

                // Reasignar posiciones secuenciales
                foreach ($order as $idx => $tourId) {
                    if (!in_array($tourId, $validIds, true)) {
                        continue;
                    }

                    // Use correct column names: tour_type_id and tour_id
                    DB::table('tour_type_tour_order')->updateOrInsert(
                        [
                            'tour_type_id' => $tourType->product_type_id,
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
                    ->where('tour_type_id', $tourType->product_type_id)
                    ->whereNotIn('tour_id', $validIds)
                    ->delete();
            });

            LoggerHelper::mutated($this->controller, 'save', 'ProductType', $tourType->product_type_id, [
                'order_count' => count($order),
                'user_id'     => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            LoggerHelper::exception($this->controller, 'save', 'ProductType', $tourType->product_type_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
