<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\CustomerCategory;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\LoggerHelper;

/**
 * TourPriceController
 *
 * Handles tourprice operations.
 */
class ProductPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-tour-prices'])->only(['index']);
        $this->middleware(['can:create-tour-prices'])->only(['store']);
        $this->middleware(['can:edit-tour-prices'])->only(['update', 'bulkUpdate', 'updateTaxes']);
        $this->middleware(['can:publish-tour-prices'])->only(['toggle']);
        $this->middleware(['can:delete-tour-prices'])->only(['destroy']);
    }

    protected string $controller = 'TourPriceController';

    /**
     * Muestra y gestiona los precios de un tour
     */
    public function index(Product $product)
    {
        $product->load(['prices.category']);

        // Agrupar precios por periodos de fechas
        $pricingPeriods = \App\Models\ProductPrice::groupByPeriods($product->prices);

        // TODAS las categorías disponibles (la misma categoría puede tener múltiples precios con diferentes fechas)
        $availableCategories = CustomerCategory::active()
            ->ordered()
            ->get();

        $taxes = Tax::active()->orderBy('sort_order')->get();

        return view('admin.products.prices.index', compact('product', 'pricingPeriods', 'availableCategories', 'taxes'));
    }

    /**
     * Agrega o actualiza múltiples precios de una vez
     * Soporta dos modos:
     * 1. Actualización completa de precios (modo original)
     * 2. Actualización de fechas de periodo (nuevo)
     */
    public function bulkUpdate(Request $request, Product $product)
    {
        // Modo 1: Actualizar fechas de periodo (nuevo)
        if ($request->has('price_ids')) {
            $validated = $request->validate([
                'price_ids'    => 'required|array',
                'price_ids.*'  => 'exists:tour_prices,tour_price_id',
                'valid_from'   => 'nullable|date',
                'valid_until'  => 'nullable|date|after_or_equal:valid_from',
                'label'        => 'nullable|string|max:255',
            ]);

            try {
                DB::transaction(function () use ($validated, $product, $request) {
                    ProductPrice::whereIn('tour_price_id', $validated['price_ids'])
                        ->where('product_id', $product->product_id)
                        ->update([
                            'valid_from'  => $validated['valid_from'] ?? null,
                            'valid_until' => $validated['valid_until'] ?? null,
                            'label'       => $validated['label'] ?? null,
                        ]);

                    LoggerHelper::mutated($this->controller, 'bulkUpdate', 'tour_prices', $product->product_id, [
                        'product_id'    => $product->product_id,
                        'price_ids'  => $validated['price_ids'],
                        'valid_from' => $validated['valid_from'] ?? null,
                        'valid_until' => $validated['valid_until'] ?? null,
                        'user_id'    => optional($request->user())->getAuthIdentifier(),
                    ]);
                });

                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => 'Fechas actualizadas exitosamente.']);
                }

                return back()->with('success', 'Fechas del periodo actualizadas exitosamente.');
            } catch (Exception $e) {
                LoggerHelper::exception($this->controller, 'bulkUpdate', 'tour_prices', $product->product_id, $e, [
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }

                return back()->with('error', 'Error al actualizar fechas: ' . $e->getMessage());
            }
        }

        // Modo 2: Actualización completa de precios (original)
        $validated = $request->validate([
            'prices'                    => 'required|array',
            'prices.*.category_id'      => 'required|exists:customer_categories,category_id',
            'prices.*.price'            => 'required|numeric|min:0',
            'prices.*.min_quantity'     => 'required|integer|min:0|max:255',
            'prices.*.max_quantity'     => 'required|integer|min:0|max:255',
            'prices.*.is_active'        => 'nullable|boolean',
            'prices.*.valid_from'       => 'nullable|date',
            'prices.*.valid_until'      => 'nullable|date|after_or_equal:prices.*.valid_from',
            'prices.*.label'            => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $product, $request) {
                foreach ($validated['prices'] as $priceData) {
                    // Validar que min <= max
                    if ($priceData['min_quantity'] > $priceData['max_quantity']) {
                        throw new Exception("Min quantity cannot be greater than max quantity for category ID {$priceData['category_id']}");
                    }

                    // Validar fechas
                    if (isset($priceData['valid_from']) && isset($priceData['valid_until'])) {
                        if ($priceData['valid_from'] > $priceData['valid_until']) {
                            throw new Exception(__("m_tours.tour.pricing.invalid_date_range"));
                        }
                    }

                    // Si el precio es 0, desactivar automáticamente
                    if ($priceData['price'] == 0) {
                        $priceData['is_active'] = false;
                    }

                    ProductPrice::updateOrCreate(
                        [
                            'product_id'     => $product->product_id,
                            'category_id' => $priceData['category_id'],
                        ],
                        [
                            'price'        => $priceData['price'],
                            'min_quantity' => $priceData['min_quantity'],
                            'max_quantity' => $priceData['max_quantity'],
                            'is_active'    => $priceData['is_active'] ?? true,
                            'valid_from'   => $priceData['valid_from'] ?? null,
                            'valid_until'  => $priceData['valid_until'] ?? null,
                            'label'        => $priceData['label'] ?? null,
                        ]
                    );
                }

                LoggerHelper::mutated($this->controller, 'bulkUpdate', 'tour_prices', $product->product_id, [
                    'product_id' => $product->product_id,
                    'count'   => count($validated['prices']),
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
            });

            return back()->with('success', 'Precios actualizados exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'bulkUpdate', 'tour_prices', $product->product_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar precios: ' . $e->getMessage());
        }
    }

    /**
     * Agrega una nueva categoría al tour
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:customer_categories,category_id',
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0|max:255',
            'max_quantity' => 'required|integer|min:0|max:255',
            'valid_from'   => 'nullable|date',
            'valid_until'  => 'nullable|date|after_or_equal:valid_from',
            'label'        => 'nullable|string|max:255',
        ]);

        try {
            // Validar fechas
            if (isset($validated['valid_from']) && isset($validated['valid_until'])) {
                if ($validated['valid_from'] > $validated['valid_until']) {
                    return back()
                        ->withInput()
                        ->with('error', __('m_tours.tour.pricing.invalid_date_range'));
                }
            }

            // Validar solapamiento de fechas
            $overlap = $this->validateDateOverlap(
                $product->product_id,
                $validated['category_id'],
                $validated['valid_from'] ?? null,
                $validated['valid_until'] ?? null
            );

            if ($overlap) {
                return back()
                    ->withInput()
                    ->with('error', __('m_tours.tour.pricing.date_overlap_warning'));
            }

            // Validar que no exista ya (solo si no tiene fechas)
            if (!isset($validated['valid_from']) && !isset($validated['valid_until'])) {
                $exists = ProductPrice::where('product_id', $product->product_id)
                    ->where('category_id', $validated['category_id'])
                    ->whereNull('valid_from')
                    ->whereNull('valid_until')
                    ->exists();

                if ($exists) {
                    return back()
                        ->withInput()
                        ->with('error', 'Esta categoría ya tiene un precio por defecto asignado.');
                }
            }

            // Validar que min <= max
            if ($validated['min_quantity'] > $validated['max_quantity']) {
                return back()
                    ->withInput()
                    ->with('error', 'La cantidad mínima no puede ser mayor que la máxima.');
            }

            // Si el precio es 0, desactivar automáticamente
            $isActive = $validated['price'] > 0;

            $tourPrice = ProductPrice::create([
                'product_id'      => $product->product_id,
                'category_id'  => $validated['category_id'],
                'price'        => $validated['price'],
                'min_quantity' => $validated['min_quantity'],
                'max_quantity' => $validated['max_quantity'],
                'is_active'    => $isActive,
                'valid_from'   => $validated['valid_from'] ?? null,
                'valid_until'  => $validated['valid_until'] ?? null,
                'label'        => $validated['label'] ?? null,
            ]);

            LoggerHelper::mutated($this->controller, 'store', 'tour_prices', $tourPrice->tour_price_id, [
                'product_id'     => $product->product_id,
                'category_id' => $validated['category_id'],
                'user_id'     => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Categoría agregada exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_prices', null, $e, [
                'product_id' => $product->product_id,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al agregar categoría: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un precio específico
     */
    public function update(Request $request, Product $product, ProductPrice $price)
    {
        $validated = $request->validate([
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0|max:255',
            'max_quantity' => 'required|integer|min:0|max:255',
            'is_active'    => 'nullable|boolean',
            'valid_from'   => 'nullable|date',
            'valid_until'  => 'nullable|date|after_or_equal:valid_from',
            'label'        => 'nullable|string|max:255',
        ]);

        try {
            // Validar que min <= max
            if ($validated['min_quantity'] > $validated['max_quantity']) {
                return back()
                    ->withInput()
                    ->with('error', 'La cantidad mínima no puede ser mayor que la máxima.');
            }

            // Validar fechas
            if (isset($validated['valid_from']) && isset($validated['valid_until'])) {
                if ($validated['valid_from'] > $validated['valid_until']) {
                    return back()
                        ->withInput()
                        ->with('error', __('m_tours.tour.pricing.invalid_date_range'));
                }
            }

            // Validar solapamiento de fechas (excluyendo el precio actual)
            $overlap = $this->validateDateOverlap(
                $price->product_id,
                $price->category_id,
                $validated['valid_from'] ?? null,
                $validated['valid_until'] ?? null,
                $price->tour_price_id
            );

            if ($overlap) {
                return back()
                    ->withInput()
                    ->with('error', __('m_tours.tour.pricing.date_overlap_warning'));
            }

            // Si el precio es 0, desactivar automáticamente
            if ($validated['price'] == 0) {
                $validated['is_active'] = false;
            }

            $price->update($validated);

            LoggerHelper::mutated($this->controller, 'update', 'tour_prices', $price->tour_price_id, [
                'product_id' => $product->product_id,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Precio actualizado exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_prices', $price->tour_price_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar precio: ' . $e->getMessage());
        }
    }

    /**
     * Toggle activar/desactivar precio
     */
    public function toggle(Product $product, ProductPrice $price)
    {
        try {
            $price->update(['is_active' => !$price->is_active]);

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_prices', $price->tour_price_id, [
                'is_active' => $price->is_active,
                'user_id'   => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Estado actualizado exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_prices', $price->tour_price_id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al cambiar estado.');
        }
    }

    /**
     * Elimina un precio (desvincula categoría del tour)
     */
    public function destroy(Product $product, ProductPrice $price)
    {
        try {
            $categoryName = $price->category->name;
            $price->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_prices', $price->tour_price_id, [
                'product_id' => $product->product_id,
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', "Categoría '{$categoryName}' eliminada del tour.");
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_prices', $price->tour_price_id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al eliminar categoría.');
        }
    }

    /**
     * Actualiza los impuestos asignados al tour
     */
    public function updateTaxes(Request $request, Product $product)
    {
        $validated = $request->validate([
            'taxes' => 'array',
            'taxes.*' => 'exists:taxes,tax_id',
        ]);

        try {
            $product->taxes()->sync($validated['taxes'] ?? []);

            LoggerHelper::mutated($this->controller, 'updateTaxes', 'tours', $product->product_id, [
                'taxes' => $validated['taxes'] ?? [],
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Impuestos actualizados exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updateTaxes', 'tours', $product->product_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar impuestos: ' . $e->getMessage());
        }
    }

    /**
     * Valida que no haya solapamiento de fechas para la misma categoría
     * 
     * @param int $tourId
     * @param int $categoryId
     * @param string|null $validFrom
     * @param string|null $validUntil
     * @param int|null $excludePriceId ID del precio a excluir de la validación
     * @return bool True si hay solapamiento, false si no
     */
    protected function validateDateOverlap(
        int $tourId,
        int $categoryId,
        ?string $validFrom,
        ?string $validUntil,
        ?int $excludePriceId = null
    ): bool {
        // Si no hay fechas, no puede haber solapamiento con precios temporales
        // (pero solo puede haber un precio sin fechas por categoría)
        if (!$validFrom && !$validUntil) {
            return false;
        }

        $query = ProductPrice::where('product_id', $tourId)
            ->where('category_id', $categoryId);

        if ($excludePriceId) {
            $query->where('tour_price_id', '!=', $excludePriceId);
        }

        // Buscar precios que se solapen
        $overlapping = $query->where(function ($q) use ($validFrom, $validUntil) {
            // Caso 1: El nuevo rango contiene el inicio de un rango existente
            $q->where(function ($subQ) use ($validFrom, $validUntil) {
                if ($validFrom) {
                    $subQ->where(function ($dateQ) use ($validFrom, $validUntil) {
                        $dateQ->where('valid_from', '>=', $validFrom);
                        if ($validUntil) {
                            $dateQ->where('valid_from', '<=', $validUntil);
                        }
                    });
                }
            })
                // Caso 2: El nuevo rango contiene el fin de un rango existente
                ->orWhere(function ($subQ) use ($validFrom, $validUntil) {
                    if ($validUntil) {
                        $subQ->where(function ($dateQ) use ($validFrom, $validUntil) {
                            if ($validFrom) {
                                $dateQ->where('valid_until', '>=', $validFrom);
                            }
                            $dateQ->where('valid_until', '<=', $validUntil);
                        });
                    }
                })
                // Caso 3: Un rango existente contiene completamente el nuevo rango
                ->orWhere(function ($subQ) use ($validFrom, $validUntil) {
                    if ($validFrom && $validUntil) {
                        $subQ->where('valid_from', '<=', $validFrom)
                            ->where('valid_until', '>=', $validUntil);
                    }
                });
        })->exists();

        return $overlapping;
    }
}
