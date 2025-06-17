<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\Log;
use Exception;

class ItineraryItemController extends Controller
{
    public function index()
    {
        $items = ItineraryItem::orderBy('order')->get();
        return view('admin.tours.itinerary.items.index', compact('items'));
    }

    public function store(Request $request)
    {
       //   dd($request->all());
  $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        ]);

      try {
        $maxOrder = ItineraryItem::max('order') ?? 0;

        ItineraryItem::create([
    'title'       => $request->title,
    'description' => $request->description,
    'order'       => $maxOrder + 1,
    'is_active'   => true,
            ]);

   return redirect()->back()->with('success', 'Ítem de itinerario creado exitosamente.');
    } catch (Exception $e) {
        Log::error('Error al crear ítem de itinerario: ' . $e->getMessage());
        return redirect()->back()->with('error', 'No se pudo crear el ítem.');
    }
}

    public function update(Request $request, ItineraryItem $item)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'required|boolean',
        ]);

        try {
            $item->update([
                'title'       => $request->title,
                'description' => $request->description,
                'is_active'   => $request->is_active,
            ]);

            return redirect()->back()->with('success', 'Ítem actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar ítem de itinerario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo actualizar el ítem.');
        }
    }

    public function destroy(ItineraryItem $item)
    {
        try {
            $item->delete();
            return redirect()->back()->with('success', 'Ítem eliminado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar ítem de itinerario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo eliminar el ítem.');
        }
    }
}
