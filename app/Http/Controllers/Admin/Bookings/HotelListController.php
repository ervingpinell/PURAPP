<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\HotelList;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Exception;

class HotelListController extends Controller
{
    /**
     * Muestra todos los hoteles activos e inactivos.
     */
    public function index(): RedirectResponse|\Illuminate\View\View
    {
        $hotels = HotelList::orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.hotels.index', compact('hotels'));
    }

    /**
     * Guarda un nuevo hotel en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // Normaliza/recorta antes de validar para evitar duplicados por espacios
        $name = trim((string) $request->input('name'));

        $request->merge(['name' => $name]);

        $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('hotels_list', 'name'),
                ],
            ],
            [
                'name.required' => __('pickups.hotels.validation.name_required'),
                'name.unique'   => __('pickups.hotels.validation.name_unique'),
                'name.max'      => __('pickups.hotels.validation.name_max'),
            ]
        );

        try {
            $nextOrder = (HotelList::max('sort_order') ?? 0) + 1;

            HotelList::create([
                'name'       => $name,
                'is_active'  => true,
                'sort_order' => $nextOrder,
            ]);

            return redirect()
                ->route('admin.hotels.index')
                ->with('success', __('pickups.hotels.created_success'));
        } catch (Exception $e) {
            Log::error('Hotel store error: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', __('pickups.hotels.unexpected_error'));
        }
    }

    /**
     * Actualiza un hotel existente.
     */
    public function update(Request $request, HotelList $hotel): RedirectResponse
    {
        $name = trim((string) $request->input('name'));
        $request->merge(['name' => $name]);

        $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('hotels_list', 'name')
                        ->ignore($hotel->hotel_id, 'hotel_id'),
                ],
                'is_active' => ['required','boolean'],
            ],
            [
                'name.required' => __('pickups.hotels.validation.name_required'),
                'name.unique'   => __('pickups.hotels.validation.name_unique'),
                'name.max'      => __('pickups.hotels.validation.name_max'),
            ]
        );

        try {
            $hotel->update([
                'name'      => $name,
                'is_active' => (bool) $request->boolean('is_active'),
            ]);

            return redirect()
                ->route('admin.hotels.index')
                ->with('success', __('pickups.hotels.updated_success'));
        } catch (Exception $e) {
            Log::error('Hotel update error: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', __('pickups.hotels.unexpected_error'));
        }
    }

    /**
     * Ordena los hoteles alfabéticamente y actualiza en la base de datos.
     */
    public function sort(): RedirectResponse
    {
        try {
            $hotels = HotelList::orderBy('name', 'asc')->get();

            $order = 1;
            foreach ($hotels as $hotel) {
                $hotel->update(['sort_order' => $order]);
                $order++;
            }

            return redirect()
                ->route('admin.hotels.index')
                ->with('success', __('pickups.hotels.sorted_success'));
        } catch (Exception $e) {
            Log::error('Hotel sort error: '.$e->getMessage());

            return back()->with('error', __('pickups.hotels.unexpected_error'));
        }
    }

    /**
     * Cambia el estado activo/inactivo del hotel.
     */
    public function toggle(HotelList $hotel): RedirectResponse
    {
        try {
            $hotel->is_active = ! $hotel->is_active;
            $hotel->save();

            $message = $hotel->is_active
                ? __('pickups.hotels.activated_success')
                : __('pickups.hotels.deactivated_success');

            return redirect()
                ->route('admin.hotels.index')
                ->with('success', $message);
        } catch (Exception $e) {
            Log::error('Hotel toggle error: '.$e->getMessage());

            return back()->with('error', __('pickups.hotels.unexpected_error'));
        }
    }

    /**
     * Elimina un hotel de la base de datos.
     */
    public function destroy(HotelList $hotel): RedirectResponse
    {
        try {
            $hotel->delete();

            return redirect()
                ->route('admin.hotels.index')
                ->with('success', __('pickups.hotels.deleted_success'));
        } catch (Exception $e) {
            Log::error('Hotel destroy error: '.$e->getMessage());

            return back()->with('error', __('pickups.hotels.unexpected_error'));
        }
    }
}
