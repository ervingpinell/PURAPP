<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourType;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Http\RedirectResponse;

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
            'name' => 'required|string|max:255|unique:tour_types,name',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
        ]);

        try {
            TourType::create([
                'name' => $request->name,
                'description' => $request->description,
                'duration' => $request->duration,
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
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tour_types', 'name')->ignore($tourType->tour_type_id, 'tour_type_id'),
                ],
                'description' => 'nullable|string',
                'duration' => 'nullable|string|max:255',
            ]);

            $tourType->update([
                'name' => $request->name,
                'description' => $request->description,
                'duration' => $request->duration,
            ]);

            return redirect()->route('admin.tourtypes.index')
                ->with('success', 'Tipo de tour actualizado correctamente.')
                ->with('alert_type', 'actualizado');

        } catch (Exception $e) {
            Log::error('Error al actualizar tipo de tour: ' . $e->getMessage());

            return back()
                ->with('error', 'Ocurrió un error inesperado al actualizar el tipo de tour.')
                ->withInput()
                ->with('edit_modal', $tourType->tour_type_id);
        }
    }

    public function toggle(TourType $tourType)
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

public function destroy(int $id): RedirectResponse
{
    $tourType = TourType::findOrFail($id);

    try {
        $tourType->delete();
        return back()->with([
            'success'    => 'Tipo de tour eliminado correctamente.',
            'alert_type' => 'eliminado',
        ]);
    } catch (\Throwable $e) {
        // Si hay FK (tours que lo usan), puedes mostrar mensaje claro
        return back()->with([
            'success'    => 'No se pudo eliminar: este tipo de tour está en uso.',
            'alert_type' => 'error',
        ]);
    }
}
}
