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
        // Lista para el <select> de categorías
        // withTranslation removal: Spatie auto-loads or we access via accessor. 
        // ProductType uses 'name' which is translatable.
        $types = ProductType::active()
            ->get(['product_type_id', 'name']) // Spatie translates 'name' accessor if we select it? Ideally yes if it's in JSON. 
            // Actually get(['name']) gets the JSON string.
            // Spatie accessors work on model instances.
            // We need to fetch all columns or just needed ones.
            ->sortBy('name');

        $selectedId = $request->get('product_type_id'); // Still using product_type_id param? Yes likely in view form.

        $selected = null;
        $tours    = collect();

        if ($selectedId) {
            $selected = ProductType::findOrFail($selectedId);

            // 1) Tours ya ordenados por la relación orderedProducts()
            $ordered = $selected->orderedProducts()
                ->select(
                    'product2.product_id', // Table is product2
                    'product2.name',
                    'product2.is_active',
                    'tour_type_tour_order.position'
                )
                ->get();

            // 2) Detectar tours sin fila en la tabla de orden
            $orderedIds = $ordered->pluck('product_id')->all();

            $missing = Product::where('product_type_id', $selected->product_type_id)
                ->whereNotIn('product_id', $orderedIds)
                ->orderBy('name') // quedarán al final en la vista
                ->get(['product_id', 'name', 'is_active'])
                ->map(function ($t) {
                    $t->position = null; // marca visual para "sin posición"
                    return $t;
                });

            // 3) Unir: primero ordenados (con position), luego faltantes
            $tours = $ordered->concat($missing);
        }

        return view('admin.products.order.index', compact('types', 'selected', 'tours'));
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

                    DB::table('tour_type_tour_order')->updateOrInsert(
                        [
                            'product_type_id' => $tourType->product_type_id,
                            'product_id'   => $tourId,
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
                    ->where('product_type_id', $tourType->product_type_id)
                    ->whereNotIn('product_id', $validIds)
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
