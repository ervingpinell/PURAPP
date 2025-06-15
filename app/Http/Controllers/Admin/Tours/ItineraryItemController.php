<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItineraryItem;
use App\Models\Tour;
use Exception;
use Illuminate\Support\Facades\Log;

class ItineraryItemController extends Controller
{
    public function index()
    {
        $items = ItineraryItem::with('tour')->orderBy('order')->get();
        $tours = Tour::all();
        return view('admin.tours.itinerary.index', compact('items', 'tours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        try {
            ItineraryItem::create([
                'tour_id' => $request->tour_id,
                'title' => $request->title,
                'description' => $request->description,
                'order' => $request->order ?? 0,
                'is_active' => true,
            ]);

            return redirect()->route('admin.tours.itinerary.index')
                ->with('success', 'Ítem agregado correctamente.')
                ->with('alert_type', 'creado');
        } catch (Exception $e) {
            Log::error('Error al crear ítem del itinerario: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al registrar el ítem.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $item = ItineraryItem::findOrFail($id);

        try {
            $item->update([
                'tour_id' => $request->tour_id,
                'title' => $request->title,
                'description' => $request->description,
                'order' => $request->order ?? 0,
            ]);

            return redirect()->route('admin.tours.itinerary.index')
                ->with('success', 'Ítem actualizado correctamente.')
                ->with('alert_type', 'actualizado');
        } catch (Exception $e) {
            Log::error('Error al actualizar ítem: ' . $e->getMessage());
            return back()->with('error', 'No se pudo actualizar el ítem.');
        }
    }

    public function destroy($id)
    {
        $item = ItineraryItem::findOrFail($id);

        try {
            $item->is_active = !$item->is_active;
            $item->save();

            $accion = $item->is_active ? 'activado' : 'desactivado';

            return redirect()->route('admin.tours.itinerary.index')
                ->with('success', "Ítem {$accion} correctamente.")
                ->with('alert_type', $accion);
        } catch (Exception $e) {
            Log::error('Error al desactivar ítem: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado del ítem.');
        }
    }
}
