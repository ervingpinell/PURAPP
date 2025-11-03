<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourPrice;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\LoggerHelper;

class TourPriceController extends Controller
{
    protected string $controller = 'TourPriceController';

    /**
     * Muestra y gestiona los precios de un tour
     */
    public function index(Tour $tour)
    {
        $tour->load(['prices.category' => function ($q) {
            $q->orderBy('order');
        }]);

        $availableCategories = CustomerCategory::active()
            ->ordered()
            ->whereNotIn('category_id', $tour->prices()->pluck('category_id'))
            ->get();

        return view('admin.tours.prices.index', compact('tour', 'availableCategories'));
    }

    /**
     * Agrega o actualiza múltiples precios de una vez
     */
    public function bulkUpdate(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'prices'                    => 'required|array',
            'prices.*.category_id'      => 'required|exists:customer_categories,category_id',
            'prices.*.price'            => 'required|numeric|min:0',
            'prices.*.min_quantity'     => 'required|integer|min:0|max:255',
            'prices.*.max_quantity'     => 'required|integer|min:0|max:255',
            'prices.*.is_active'        => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($validated, $tour, $request) {
                foreach ($validated['prices'] as $priceData) {
                    // Validar que min <= max
                    if ($priceData['min_quantity'] > $priceData['max_quantity']) {
                        throw new Exception("Min quantity cannot be greater than max quantity for category ID {$priceData['category_id']}");
                    }

                    // Si el precio es 0, desactivar automáticamente
                    if ($priceData['price'] == 0) {
                        $priceData['is_active'] = false;
                    }

                    TourPrice::updateOrCreate(
                        [
                            'tour_id'     => $tour->tour_id,
                            'category_id' => $priceData['category_id'],
                        ],
                        [
                            'price'        => $priceData['price'],
                            'min_quantity' => $priceData['min_quantity'],
                            'max_quantity' => $priceData['max_quantity'],
                            'is_active'    => $priceData['is_active'] ?? true,
                        ]
                    );
                }

                LoggerHelper::mutated($this->controller, 'bulkUpdate', 'tour_prices', $tour->tour_id, [
                    'tour_id' => $tour->tour_id,
                    'count'   => count($validated['prices']),
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
            });

            return back()->with('success', 'Precios actualizados exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'bulkUpdate', 'tour_prices', $tour->tour_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar precios: ' . $e->getMessage());
        }
    }

    /**
     * Agrega una nueva categoría al tour
     */
    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:customer_categories,category_id',
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0|max:255',
            'max_quantity' => 'required|integer|min:0|max:255',
        ]);

        try {
            // Validar que no exista ya
            $exists = TourPrice::where('tour_id', $tour->tour_id)
                ->where('category_id', $validated['category_id'])
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Esta categoría ya está asignada a este tour.');
            }

            // Validar que min <= max
            if ($validated['min_quantity'] > $validated['max_quantity']) {
                return back()
                    ->withInput()
                    ->with('error', 'La cantidad mínima no puede ser mayor que la máxima.');
            }

            // Si el precio es 0, desactivar automáticamente
            $isActive = $validated['price'] > 0;

            $tourPrice = TourPrice::create([
                'tour_id'      => $tour->tour_id,
                'category_id'  => $validated['category_id'],
                'price'        => $validated['price'],
                'min_quantity' => $validated['min_quantity'],
                'max_quantity' => $validated['max_quantity'],
                'is_active'    => $isActive,
            ]);

            LoggerHelper::mutated($this->controller, 'store', 'tour_prices', $tourPrice->tour_price_id, [
                'tour_id'     => $tour->tour_id,
                'category_id' => $validated['category_id'],
                'user_id'     => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Categoría agregada exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_prices', null, $e, [
                'tour_id' => $tour->tour_id,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al agregar categoría: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un precio específico
     */
    public function update(Request $request, Tour $tour, TourPrice $price)
    {
        $validated = $request->validate([
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0|max:255',
            'max_quantity' => 'required|integer|min:0|max:255',
            'is_active'    => 'nullable|boolean',
        ]);

        try {
            // Validar que min <= max
            if ($validated['min_quantity'] > $validated['max_quantity']) {
                return back()
                    ->withInput()
                    ->with('error', 'La cantidad mínima no puede ser mayor que la máxima.');
            }

            // Si el precio es 0, desactivar automáticamente
            if ($validated['price'] == 0) {
                $validated['is_active'] = false;
            }

            $price->update($validated);

            LoggerHelper::mutated($this->controller, 'update', 'tour_prices', $price->tour_price_id, [
                'tour_id' => $tour->tour_id,
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
    public function toggle(Tour $tour, TourPrice $price)
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
    public function destroy(Tour $tour, TourPrice $price)
    {
        try {
            $categoryName = $price->category->name;
            $price->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_prices', $price->tour_price_id, [
                'tour_id' => $tour->tour_id,
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
}
