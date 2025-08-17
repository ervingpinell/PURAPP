<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\ItineraryTranslation;
use App\Services\ItineraryService;
use App\Services\Contracts\TranslatorInterface;

class ItineraryController extends Controller
{
    public function index(ItineraryService $service)
    {
        $itineraries = Itinerary::with('items')->orderBy('name')->get();
        $items = $service->getAvailableItems();

        return view('admin.tours.itinerary.index', compact('itineraries', 'items'));
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        $request->validate([
            'name'        => ['required','string','max:255', Rule::unique('itineraries','name')],
            'description' => ['nullable','string','max:1000'],
        ], [
            'name.required'   => 'El nombre del itinerario es obligatorio.',
            'name.unique'     => 'Ya existe un itinerario con ese nombre.',
            'name.max'        => 'El nombre no puede exceder 255 caracteres.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
        ]);

        try {
            DB::transaction(function () use ($request, $translator) {
                $name = $request->string('name')->trim();
                $description = $request->filled('description') ? $request->string('description')->trim() : '';

                $itinerary = Itinerary::create([
                    'name'        => $name,
                    'description' => $description,
                    'is_active'   => true,
                ]);

                // Traducciones automáticas (ES, EN, FR, PT, DE)
                $nameTranslations = $translator->translateAll($name);
                $descriptionTranslations = $translator->translateAll($description);

                foreach (['es', 'en', 'fr', 'pt', 'de'] as $lang) {
                    ItineraryTranslation::create([
                        'itinerary_id' => $itinerary->itinerary_id,
                        'locale'       => $lang,
                        'name'         => $nameTranslations[$lang] ?? $name,
                        'description'  => $descriptionTranslations[$lang] ?? $description,
                    ]);
                }
            });

            return back()->with('success', 'Itinerario creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error creando itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear el itinerario.');
        }
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $request->validate([
            'name' => [
                'required','string','max:255',
                Rule::unique('itineraries','name')->ignore($itinerary->itinerary_id, 'itinerary_id'),
            ],
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required'   => 'El nombre del itinerario es obligatorio.',
            'name.unique'     => 'Ya existe un itinerario con ese nombre.',
            'name.max'        => 'El nombre no puede exceder 255 caracteres.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
        ]);

        try {
            $name = $request->string('name')->trim();
            $description = $request->filled('description') ? $request->string('description')->trim() : '';

            $itinerary->update([
                'name'        => $name,
                'description' => $description,
            ]);

            return redirect()
                ->route('admin.tours.itinerary.index')
                ->with('success', 'Itinerario actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error actualizando itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo actualizar el itinerario.');
        }
    }

    /**
     * Activa/Desactiva un itinerario conservando sus ítems.
     * - Al desactivar: NO se desvinculan los ítems. (Opcional: podrías marcar el pivote como inactivo)
     * - Al activar: solo cambia el flag del itinerario.
     */
    public function destroy(Itinerary $itinerary)
    {
        try {
            if ($itinerary->is_active) {
                // Solo desactivar el itinerario — NO detach
                $itinerary->update(['is_active' => false]);

                // OPCIONAL: si quieres que no aparezcan los ítems en listados que miran el pivote,
                // puedes desactivarlos en el pivote sin perderlos:
                // $itinerary->items()->updateExistingPivot(
                //     $itinerary->items->pluck('item_id')->all(),
                //     ['is_active' => false]
                // );

                return back()->with('success', 'Itinerario desactivado correctamente (ítems conservados).');
            }

            // Reactivar itinerario
            $itinerary->update(['is_active' => true]);

            // OPCIONAL: reactivar ítems del pivote si los desactivaste al apagar:
            // $itinerary->items()->updateExistingPivot(
            //     $itinerary->items->pluck('item_id')->all(),
            //     ['is_active' => true]
            // );

            return back()->with('success', 'Itinerario activado correctamente.');
        } catch (Exception $e) {
            Log::error('Error cambiando estado de itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado del itinerario.');
        }
    }

    public function assignItems(Request $request, Itinerary $itinerary)
    {
        // item_ids llega como array asociativo: [item_id => order, ...]
        $rawData = $request->input('item_ids', []);

        $itemIds = collect($rawData)
            ->keys()
            ->filter(fn ($id) => $id !== 'dummy')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (count($itemIds) === 0) {
            return back()
                ->withErrors(['item_ids' => 'Debes seleccionar al menos un ítem.'])
                ->withInput()
                ->with('showAssignModal', $itinerary->itinerary_id);
        }

        // Verificar que los ítems existan y estén activos
        $activeItems = ItineraryItem::whereIn('item_id', $itemIds)
            ->where('is_active', true)
            ->pluck('item_id')
            ->all();

        $inactiveItems = array_diff($itemIds, $activeItems);
        if (count($inactiveItems)) {
            return back()
                ->withErrors(['item_ids' => 'No puedes asignar un ítem inactivo.'])
                ->withInput()
                ->with('showAssignModal', $itinerary->itinerary_id);
        }

        // Construir datos del pivote
        $syncData = [];
        foreach ($rawData as $itemId => $order) {
            if ($itemId === 'dummy') continue;
            $syncData[(int)$itemId] = [
                'item_order' => (int)$order,
                'is_active'  => true,
            ];
        }

        $itinerary->items()->sync($syncData);

        return redirect()
            ->route('admin.tours.itinerary.index')
            ->with('success', 'Ítems asignados correctamente.');
    }
}
