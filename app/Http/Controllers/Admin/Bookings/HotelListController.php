<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\HotelList;
use Illuminate\Http\Request;

class HotelListController extends Controller
{
    /**
     * Muestra todos los hoteles activos e inactivos.
     */
public function index()
{
    $hotels = HotelList::orderByRaw('sort_order IS NULL, sort_order ASC')
        ->orderBy('name', 'asc')
        ->get();

    return view('admin.hotels.index', compact('hotels'));
}


    /**
     * Guarda un nuevo hotel en la base de datos.
     */
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:hotels_list,name',
    ]);

    $nextOrder = (HotelList::max('sort_order') ?? 0) + 1;

    HotelList::create([
        'name' => $request->name,
        'is_active' => true,
        'sort_order' => $nextOrder, // <- opcional
    ]);

    return redirect()->route('admin.hotels.index')
        ->with('success', 'Hotel creado exitosamente.');
}


    /**
     * Actualiza un hotel existente.
     */
    public function update(Request $request, HotelList $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:hotels_list,name,' . $hotel->hotel_id . ',hotel_id',
            'is_active' => 'required|boolean',
        ]);

        $hotel->update([
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel actualizado correctamente.');
    }

    /**
 * Ordena los hoteles alfabéticamente y actualiza en la base de datos.
 */
public function sort()
{
    $hotels = HotelList::orderBy('name', 'asc')->get();

    $order = 1;
    foreach ($hotels as $hotel) {
        $hotel->update(['sort_order' => $order]);
        $order++;
    }

    return redirect()->route('admin.hotels.index')->with('success', 'Hoteles ordenados alfabéticamente.');
}

/**
 * Cambia el estado activo/inactivo del hotel.
 */
public function destroy(HotelList $hotel)
{
    try {
        $hotel->is_active = ! $hotel->is_active;
        $hotel->save();

        $mensaje = $hotel->is_active
            ? 'Hotel activado correctamente.'
            : 'Hotel desactivado correctamente.';

        return redirect()->route('admin.hotels.index')->with('success', $mensaje);
    } catch (\Exception $e) {
        \Log::error('Error al cambiar estado del hotel: ' . $e->getMessage());
        return back()->with('error', 'Hubo un problema al cambiar el estado del hotel.');
    }
}

}
