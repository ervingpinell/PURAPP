<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\ItineraryService;
use Exception;

class ItineraryController extends Controller
{
    public function index()
    {
        
     $itineraries = Itinerary::with(['items' => function($q) {
        $q->orderBy('item_order');
    }])->get();

    $items = ItineraryItem::orderBy('title')->get();

    return view('admin.tours.itinerary.index', compact('itineraries', 'items'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:itineraries,name',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:itineraries,name,' . $itinerary->itinerary_id . ',itinerary_id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $itinerary->update(['name' => $request->name]);
            return redirect()->back()->with('success', 'Itinerario actualizado.');
        } catch (Exception $e) {
            Log::error('Error al actualizar itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo actualizar el itinerario.');
        }
    }

    public function destroy(Itinerary $itinerary)
    {
        try {
            $itinerary->delete();
            return redirect()->back()->with('success', 'Itinerario eliminado.');
        } catch (Exception $e) {
            Log::error('Error al eliminar itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo eliminar el itinerario.');
        }
    }

    public function assignItems(Request $request, Itinerary $itinerary)
    {
        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:itinerary_items,item_id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            (new ItineraryService())->replaceItemsWithOrder($itinerary, $request->item_ids);
            return redirect()->route('admin.tours.itinerary.index')->with('success', 'Ítems asignados correctamente.');
        } catch (Exception $e) {
            Log::error('Error al asignar ítems: ' . $e->getMessage());
            return back()->with('error', 'No se pudo asignar los ítems.');
        }
    }

    public function fetchAvailableItems()
    {
        $items = ItineraryItem::orderBy('title')->where('is_active', true)->get();
        return response()->json($items);
    }
}
