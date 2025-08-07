<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Services\ItineraryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleTranslationService;

class ItineraryController extends Controller
{
public function index(ItineraryService $service)
{
    $itineraries = Itinerary::with('items')
        ->orderBy('name')
        ->get();

    $items = $service->getAvailableItems();

    return view('admin.tours.itinerary.index', compact('itineraries', 'items'));
}

public function store(Request $request, GoogleTranslationService $translator)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:itineraries,name',
        'description' => 'nullable|string|max:1000',
    ]);

    try {
        $itinerary = Itinerary::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
        ]);

        // ✅ Traducción automática al crear
        foreach (['en', 'pt', 'fr', 'de'] as $lang) {
            \App\Models\ItineraryTranslation::create([
                'itinerary_id' => $itinerary->itinerary_id,
                'locale'       => $lang,
                'name'         => $translator->translate($itinerary->name, $lang),
                'description'  => $translator->translate($itinerary->description, $lang),
            ]);
        }

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
            Rule::unique('itineraries', 'name')->ignore($id, 'itinerary_id'),
        ],
        'description' => 'nullable|string|max:1000',
    ]);

    try {
        $itinerary = Itinerary::findOrFail($id);
        $itinerary->update([
            'name' => $request->name,
            'description' => $request->description ?? '',
        ]);

        return redirect()->route('admin.tours.itinerary.index')->with('success', 'Itinerario actualizado correctamente.');
    } catch (Exception $e) {
        Log::error('Error al actualizar itinerario: ' . $e->getMessage());
        return back()->with('error', 'No se pudo actualizar el itinerario.');
    }
}

public function destroy($id)
{
    try {
        $itinerary = Itinerary::findOrFail($id);

        // Si está activo, desvincula y desactiva
        if ($itinerary->is_active) {
            $itinerary->items()->detach();
            $itinerary->update(['is_active' => false]);

            return redirect()->back()->with('success', 'Itinerario desactivado correctamente.');
        } else {
            // Si está inactivo, solo activa
            $itinerary->update(['is_active' => true]);

            return redirect()->back()->with('success', 'Itinerario activado correctamente.');
        }

    } catch (Exception $e) {
        Log::error('Error al cambiar estado del itinerario: ' . $e->getMessage());
        return back()->with('error', 'No se pudo cambiar el estado del itinerario.');
    }
}



public function assignItems(Request $request, $id)
{
    $itemIds = collect($request->input('item_ids', []))
        ->keys()
        ->filter(fn($id) => $id !== 'dummy') // ignorar dummy
        ->map(fn($id) => (int)$id)
        ->toArray();

    if (count($itemIds) === 0) {
        return back()
            ->withErrors(['item_ids' => 'Tienes que seleccionar al menos un ítem.'])
            ->withInput()
            ->with('showAssignModal', $id);
    }

    // Verifica si todos existen y están activos
    $activos = \App\Models\ItineraryItem::whereIn('item_id', $itemIds)
        ->where('is_active', true)
        ->pluck('item_id')
        ->toArray();

    $faltantes = array_diff($itemIds, $activos);

    if (count($faltantes)) {
        return back()
            ->withErrors(['item_ids' => 'No puedes asignar un ítem inactivo.'])
            ->withInput()
            ->with('showAssignModal', $id);
    }

    // Asignar
    $itinerary = \App\Models\Itinerary::findOrFail($id);
    $syncData = [];
    foreach ($request->input('item_ids', []) as $itemId => $order) {
        if ($itemId === 'dummy') continue;
        $syncData[$itemId] = [
            'item_order' => $order,
            'is_active' => true,
        ];
    }

    $itinerary->items()->sync($syncData);

    return redirect()
        ->route('admin.tours.itinerary.index')
        ->with('success', 'Ítems asignados correctamente.');
}


}
