<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\Log;
use Exception;

class ItineraryController extends Controller
{
    public function index()
    {
            $itineraries = Itinerary::withCount('items')->get(); // Lista de itinerarios
           $items = ItineraryItem::with('itineraries')->get(); // Todos los ítems
    return view('admin.tours.itinerary.index', compact('itineraries', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            Itinerary::create(['name' => $request->name]);
            return redirect()->back()->with('success', 'Itinerario creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al crear itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear el itinerario.');
        }
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $itinerary->update(['name' => $request->name]);
        return redirect()->back()->with('success', 'Itinerario actualizado.');
    }

    public function destroy(Itinerary $itinerary)
    {
        $itinerary->delete();
        return redirect()->back()->with('success', 'Itinerario eliminado.');
    }

public function assignItems(Request $request, Itinerary $itinerary)
{
    $request->validate([
        'item_ids' => 'required|array',
        'item_ids.*' => 'exists:itinerary_items,item_id',
    ]);

    foreach ($request->item_ids as $itemId) {
        $itinerary->items()->syncWithoutDetaching([
            $itemId => ['is_active' => true]
        ]);
    }
    session()->flash('success', 'Ítems asignados correctamente.');
    return redirect()->route('admin.tours.itinerary.index');

}

}
