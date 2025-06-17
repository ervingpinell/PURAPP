<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourType;
use Illuminate\Support\Facades\Log;
use Exception;

class TourTypeController extends Controller
{
    public function index()
    {
    $tourTypes = TourType::orderByDesc('created_at')->get();

    return view('admin.tourtypes.index', compact('tourTypes'));

    }

    public function store(Request $request)
    {


        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            TourType::create([
                 'name' => $request->name,
             'description' => $request->description,
            'is_active' => true,
            ]);

            return redirect()->route('admin.tourtypes.index')
                ->with('success', 'Tipo de tour creado correctamente.')
                ->with('alert_type', 'creado');
        } catch (Exception $e) {
            Log::error('Error al crear tipo de tour: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear el tipo de tour.');
        }
    }

    public function update(Request $request, TourType $tourType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $tourType->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.tourtypes.index')
                ->with('success', 'Tipo de tour actualizado correctamente.')
                ->with('alert_type', 'actualizado');
        } catch (Exception $e) {
            Log::error('Error al actualizar tipo de tour: ' . $e->getMessage());
            return back()->with('error', 'No se pudo actualizar el tipo de tour.');
        }
    }

    public function destroy(TourType $tourType)
    {
        try {
            $tourType->is_active = !$tourType->is_active;
            $tourType->save();

            $accion = $tourType->is_active ? 'activado' : 'desactivado';

            return redirect()->route('admin.tourtypes.index')
                ->with('success', "Tipo de tour {$accion} correctamente.")
                ->with('alert_type', $accion);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del tipo de tour: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado del tipo de tour.');
        }
    }
}