<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Services\ItineraryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class ItineraryController extends Controller
{
    public function index(ItineraryService $service)
    {
        $itineraries = Itinerary::with('items')->orderBy('name')->get();
        $items = $service->getAvailableItems();

        return view('admin.tours.itinerary.index', compact('itineraries', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:itineraries,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Itinerary::create([
                'name' => $request->name,
                'description' => $request->description ?? '',
            ]);

            return redirect()->back()->with('success', 'Itinerario creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear el itinerario.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('itineraries', 'name')->ignore($id),
            ],
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $itinerary = Itinerary::findOrFail($id);
            $itinerary->update([
                'name' => $request->name,
                'description' => $request->description ?? '',
            ]);

            return redirect()->back()->with('success', 'Itinerario actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo actualizar el itinerario.');
        }
    }

    public function destroy($id)
    {
        try {
            $itinerary = Itinerary::findOrFail($id);
            $itinerary->items()->detach();
            $itinerary->delete();

            return redirect()->back()->with('success', 'Itinerario eliminado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo eliminar el itinerario.');
        }
    }

    public function assignItems(Request $request, $id)
    {
        try {
            $itinerary = Itinerary::findOrFail($id);
            $itemIds = $request->input('item_ids', []);

            $syncData = [];
            foreach ($itemIds as $index => $itemId) {
                $syncData[$itemId] = [
                    'item_order' => $index,
                    'is_active' => true
                ];
            }

            $itinerary->items()->sync($syncData);

            return redirect()->back()->with('success', 'Ítems asignados correctamente.');
        } catch (Exception $e) {
            Log::error("Error al asignar ítems al itinerario: " . $e->getMessage());
            return back()->with('error', 'No se pudieron asignar los ítems.');
        }
    }
}
