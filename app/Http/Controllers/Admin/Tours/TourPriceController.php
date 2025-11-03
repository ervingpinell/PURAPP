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
        $request->validate([
            'prices'                    => 'required|array',
            'prices.*.category_id'      => 'required|exists:customer_categories,category_id',
            'prices.*.price'            => 'required|numeric|min:0',
            'prices.*.min_quantity'     => 'required|integer|min:0|max:255',
            'prices.*.max_quantity'     => 'required|integer|min:0|max:255|gte:prices.*.min_quantity',
            'prices.*.is_active'        => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($request, $tour) {
                foreach ($request->prices as $priceData) {
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
            });

            LoggerHelper::mutated($this->controller, 'bulkUpdate', 'tour_prices', $tour->tour_id, [
                'tour_id' => $tour->tour_id,
                'count'   => count($request->prices),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

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
        $request->validate([
            'category_id'  => 'required|exists:customer_categories,category_id|unique:tour_prices,category_id,NULL,tour_price_id,tour_id,' . $tour->tour_id,
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0|max:255',
            'max_quantity' => 'required|integer|min:0|max:255|gte:min_quantity',
        ]);

        try {
            $tourPrice = TourPrice::create([
                'tour_id'      => $tour->tour_id,
                'category_id'  => $request->category_id,
                'price'        => $request->price,
                'min_quantity' => $request->min_quantity,
                'max_quantity' => $request->max_quantity,
                'is_active'    => true,
            ]);

            LoggerHelper::mutated($this->controller, 'store', 'tour_prices', $tourPrice->tour_price_id, [
                'tour_id'     => $tour->tour_id,
                'category_id' => $request->category_id,
                'user_id'     => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Categoría agregada exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_prices', null, $e, [
                'tour_id' => $tour->tour_id,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al agregar categoría.');
        }
    }

    /**
     * Actualiza un precio específico
     */
    public function update(Request $request, Tour $tour, TourPrice $price)
    {
        $request->validate([
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0|max:255',
            'max_quantity' => 'required|integer|min:0|max:255|gte:min_quantity',
            'is_active'    => 'boolean',
        ]);

        try {
            $price->update($request->only(['price', 'min_quantity', 'max_quantity', 'is_active']));

            LoggerHelper::mutated($this->controller, 'update', 'tour_prices', $price->tour_price_id, [
                'tour_id' => $tour->tour_id,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Precio actualizado exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_prices', $price->tour_price_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar precio.');
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
