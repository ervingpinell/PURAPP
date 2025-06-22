<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class ItineraryItemController extends Controller
{
    public function index()
    {
        $items = ItineraryItem::where('is_active', true)
        ->orderBy('title')
        ->get();
        return view('admin.tours.itinerary.items.crud', compact('items'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255|unique:itinerary_items,title',
            'description' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            ItineraryItem::create([
                'title'       => $request->title,
                'description' => $request->description,
                'is_active'   => true,
            ]);

            return redirect()->back()->with('success', 'Ítem de itinerario creado exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al crear ítem de itinerario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo crear el ítem.');
        }
    }

    public function update(Request $request, ItineraryItem $itinerary_item)
    {
        $item = $itinerary_item;

        $validator = Validator::make($request->all(), [
            'title'       => [
                'required',
                'string',
                'max:255',
                Rule::unique('itinerary_items', 'title')->ignore($item->item_id, 'item_id'),
            ],
            'description' => 'required|string|max:2000',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $item->update([
                'title'       => $request->title,
                'description' => $request->description,
                'is_active'   => $request->has('is_active') ? (bool) $request->is_active : $item->is_active,
            ]);

            return redirect()->back()->with('success', 'Ítem actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar ítem de itinerario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo actualizar el ítem.');
        }
    }

    public function destroy(ItineraryItem $itinerary_item)
    {
        try {
            // 1. Cambiamos el estado
            $itinerary_item->update([
                'is_active' => ! $itinerary_item->is_active,
            ]);
            $itinerary_item->refresh();

            // 2. Si quedó inactivo, lo desvinculamos de todos los itinerarios
            if (! $itinerary_item->is_active) {
                $itinerary_item->itineraries()->detach();
            }

            $mensaje = $itinerary_item->is_active
                ? 'Ítem activado exitosamente.'
                : 'Ítem desactivado exitosamente.';

            return redirect()->back()->with('success', $mensaje);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del ítem de itinerario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado del ítem.');
        }
    }

}
