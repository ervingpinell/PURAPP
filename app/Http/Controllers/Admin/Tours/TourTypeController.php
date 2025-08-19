<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use App\Models\TourType;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\TourType\StoreTourTypeRequest;
use App\Http\Requests\Tour\TourType\UpdateTourTypeRequest;
use App\Http\Requests\Tour\TourType\ToggleTourTypeRequest; // 👈 NUEVO

class TourTypeController extends Controller
{
    protected string $controller = 'TourTypeController';

    public function index()
    {
        $tourTypes = TourType::orderByDesc('created_at')->get();

        return view('admin.tourtypes.index', compact('tourTypes'));
    }

    public function store(StoreTourTypeRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $tourType = TourType::create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'duration'    => $data['duration'] ?? null,
                'is_active'   => true,
            ]);

            LoggerHelper::mutated($this->controller, 'store', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', 'Tipo de tour creado correctamente.')
                ->with('alert_type', 'creado');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_type', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'No se pudo crear el tipo de tour.');
        }
    }

    public function update(UpdateTourTypeRequest $request, TourType $tourType): RedirectResponse
    {
        try {
            $data = $request->validated();

            $tourType->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'duration'    => $data['duration'] ?? null,
            ]);

            LoggerHelper::mutated($this->controller, 'update', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', 'Tipo de tour actualizado correctamente.')
                ->with('alert_type', 'actualizado');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'Ocurrió un error inesperado al actualizar el tipo de tour.')
                ->withInput()
                ->with('edit_modal', $tourType->getKey());
        }
    }

    public function toggle(ToggleTourTypeRequest $request, TourType $tourType): RedirectResponse
    {
        try {
            $tourType->is_active = ! $tourType->is_active;
            $tourType->save();

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_type', $tourType->getKey(), [
                'is_active' => $tourType->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $accion = $tourType->is_active ? 'activado' : 'desactivado';

            return redirect()
                ->route('admin.tourtypes.index')
                ->with('success', "Tipo de tour {$accion} correctamente.")
                ->with('alert_type', $accion);

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'No se pudo cambiar el estado del tipo de tour.');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $tourType = TourType::findOrFail($id);

        try {
            $tourType->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_type', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with([
                'success'    => 'Tipo de tour eliminado correctamente.',
                'alert_type' => 'eliminado',
            ]);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_type', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with([
                'success'    => 'No se pudo eliminar: este tipo de tour está en uso.',
                'alert_type' => 'error',
            ]);
        }
    }
}
