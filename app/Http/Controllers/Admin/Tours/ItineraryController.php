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
        $itineraries = Itinerary::where('is_active', true)
        ->with('items')
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

            // 1. Desvincular todos los items
            $itinerary->items()->detach();

            // 2. Marcar como inactivo
            $itinerary->update(['is_active' => false]);

            return redirect()->back()->with('success', 'Itinerario desactivado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al desactivar itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo desactivar el itinerario.');
        }
    }


public function assignItems(Request $request, $id)
    {
        // 1) creamos el validador
        $validator = Validator::make($request->all(), [
            // ahora obligamos al menos un checkbox
            'item_ids'   => 'required|array|min:1',
            // cada ID debe existir y además is_active = true
            'item_ids.*' => [
                Rule::exists('itinerary_items', 'item_id')
                    ->where('is_active', true)
            ],
        ],[
            'item_ids.required'   => 'Tienes que seleccionar al menos un ítem.',
            'item_ids.array'      => 'Formato inválido para los ítems.',
            'item_ids.min'        => 'Tienes que seleccionar al menos un ítem.',
            'item_ids.*.exists'   => 'No puedes asignar un ítem inactivo.',
        ]);

        // 2) si falla, redirigimos con un flag para reabrir el modal
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showAssignModal', $id);
        }

        // 3) si pasa, sincronizamos
        $itinerary = Itinerary::findOrFail($id);
        $syncData  = [];
        foreach ($request->input('item_ids', []) as $idx => $itemId) {
            $syncData[$itemId] = ['item_order' => $idx, 'is_active' => true];
        }
        $itinerary->items()->sync($syncData);

        return redirect()
            ->route('admin.tours.itinerary.index')
            ->with('success', 'Ítems asignados correctamente.');
    }

}
