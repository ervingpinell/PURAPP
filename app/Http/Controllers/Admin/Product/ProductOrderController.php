<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LoggerHelper;

/**
 * ProductOrderController
 *
 * Handles product order operations.
 */
class ProductOrderController extends Controller
{
    protected string $controller = 'ProductOrderController';

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        
        // Lista para el <select> de categorías
        $types = ProductType::active()
            ->get(['product_type_id', 'name'])
            ->sortBy('name');

        // Allow both parameters for compatibility
        $selectedId = $request->get('product_type_id') ?? $request->get('product_type_id');

        $selected = null;
        $products = collect();

        if ($selectedId) {
            $selected = ProductType::findOrFail($selectedId);

            // 1) Products ya ordenados por la relación orderedProducts()
            $ordered = $selected->orderedProducts()
                ->select(
                    'product2.product_id', 
                    'product2.name',
                    'product2.is_active',
                    'product_type_product_order.position'
                )
                ->get();

            // 2) Detectar products sin fila en la tabla de orden
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
            $products = $ordered->concat($missing);
        }

        return view('admin.products.order.index', compact('types', 'selected', 'products'));
    }

    public function save(Request $request, ProductType $productType)
    {
        try {
            $data = $request->validate([
                'order'   => ['required', 'array', 'min:1'],
                'order.*' => ['integer', 'distinct'], // IDs de product en el orden deseado
            ]);

            $order = $data['order'];

            DB::transaction(function () use ($order, $productType) {
                // Limitar a products que realmente pertenecen a esa categoría
                $validIds = Product::where('product_type_id', $productType->product_type_id)
                    ->whereIn('product_id', $order)
                    ->pluck('product_id')
                    ->all();

                // Reasignar posiciones secuenciales
                foreach ($order as $idx => $productId) {
                    if (!in_array($productId, $validIds, true)) {
                        continue;
                    }

                    // Use correct column names: product_type_id and product_id
                    DB::table('product_type_product_order')->updateOrInsert(
                        [
                            'product_type_id' => $productType->product_type_id,
                            'product_id'      => $productId,
                        ],
                        [
                            'position'   => $idx + 1,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }

                // (Opcional) Limpiar filas de products que ya no están en esta categoría
                DB::table('product_type_product_order')
                    ->where('product_type_id', $productType->product_type_id)
                    ->whereNotIn('product_id', $validIds)
                    ->delete();
            });

            LoggerHelper::mutated($this->controller, 'save', 'ProductType', $productType->product_type_id, [
                'order_count' => count($order),
                'user_id'     => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            LoggerHelper::exception($this->controller, 'save', 'ProductType', $productType->product_type_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
