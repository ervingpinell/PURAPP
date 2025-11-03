<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Tour;
use App\Models\Schedule;
use App\Models\TourAvailability;
use App\Services\LoggerHelper;
use Illuminate\Http\Request;
use App\Http\Requests\Tour\TourAvailability\StoreTourAvailabilityRequest;
use App\Http\Requests\Tour\TourAvailability\UpdateTourAvailabilityRequest;

class TourAvailabilityController extends Controller
{
    protected string $controller = 'TourAvailabilityController';

    /**
     * Vista principal con tabs: Global, Por Tour+Horario, Día+Horario
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'global');

        // Tab 1: Capacidades globales de tours (Tour.max_capacity)
        $tours = Tour::with(['tourType', 'schedules' => function($q) {
            $q->orderBy('start_time');
        }])->orderBy('name')->get();

        // Schedules para referencia
        $schedules = Schedule::orderBy('start_time')->get();

        // Tab 3: Overrides por día + horario (TourAvailability con schedule_id)
        $dayScheduleOverrides = TourAvailability::whereNotNull('schedule_id')
            ->with(['tour', 'schedule'])
            ->orderBy('date', 'desc')
            ->orderBy('tour_id')
            ->orderBy('schedule_id')
            ->paginate(20, ['*'], 'day_schedule_page');

        return view('admin.tours.capacity.index', compact(
            'tab',
            'tours',
            'schedules',
            'dayScheduleOverrides'
        ));
    }

    /**
     * Actualizar capacidad global de un tour
     */
    public function updateTourCapacity(Request $request, Tour $tour)
    {
        $request->validate([
            'max_capacity' => 'required|integer|min:1|max:999',
        ]);

        try {
            $tour->update([
                'max_capacity' => $request->max_capacity
            ]);

            LoggerHelper::mutated($this->controller, 'updateTourCapacity', 'tour', $tour->tour_id, [
                'max_capacity' => $request->max_capacity,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Capacidad global del tour actualizada correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updateTourCapacity', 'tour', $tour->tour_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar capacidad global.');
        }
    }

    /**
     * Crear o actualizar override de capacidad (día+horario)
     * CRÍTICO: Usa updateOrCreate para evitar duplicados
     */
    public function store(StoreTourAvailabilityRequest $request)
    {
        try {
            $data = $request->validated();

            // Condiciones de búsqueda (unique constraint)
            $conditions = [
                'tour_id'     => $data['tour_id'],
                'schedule_id' => $data['schedule_id'],
                'date'        => $data['date'],
            ];

            // Valores a actualizar/crear
            $attributes = [];

            // Si viene is_blocked=true, bloquear (capacidad null)
            if (!empty($data['is_blocked'])) {
                $attributes['max_capacity'] = null;
                $attributes['is_blocked'] = true;
            }
            // Si viene max_capacity, es un override de capacidad
            else if (isset($data['max_capacity']) && $data['max_capacity'] !== '') {
                $attributes['max_capacity'] = (int) $data['max_capacity'];
                $attributes['is_blocked'] = false;
            }
            // Si no viene nada específico, limpiar override
            else {
                $attributes['max_capacity'] = null;
                $attributes['is_blocked'] = false;
            }

            // updateOrCreate: busca por tour_id+schedule_id+date, actualiza si existe o crea si no
            $availability = TourAvailability::updateOrCreate(
                $conditions,
                $attributes
            );

            LoggerHelper::mutated($this->controller, 'store', 'tour_availability', $availability->getKey(), [
                'was_recently_created' => $availability->wasRecentlyCreated,
                'tour_id' => $data['tour_id'],
                'schedule_id' => $data['schedule_id'],
                'date' => $data['date'],
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            $message = $availability->wasRecentlyCreated
                ? 'Override de capacidad creado correctamente.'
                : 'Override de capacidad actualizado correctamente.';

            return back()->with('success', $message);

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_availability', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al guardar el override de capacidad.');
        }
    }

    /**
     * Actualizar override existente
     */
    public function update(UpdateTourAvailabilityRequest $request, TourAvailability $availability)
    {
        try {
            $data = $request->validated();

            $updateData = [];

            // Si viene is_blocked, actualizar
            if (isset($data['is_blocked'])) {
                $updateData['is_blocked'] = (bool) $data['is_blocked'];
                if ($updateData['is_blocked']) {
                    $updateData['max_capacity'] = null;
                }
            }

            // Si viene max_capacity y no está bloqueado, actualizar
            if (isset($data['max_capacity']) && !($updateData['is_blocked'] ?? false)) {
                $updateData['max_capacity'] = $data['max_capacity'];
            }

            if (!empty($updateData)) {
                $availability->update($updateData);
            }

            LoggerHelper::mutated($this->controller, 'update', 'tour_availability', $availability->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Override de capacidad actualizado.');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_availability', $availability->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al actualizar override.');
        }
    }

    /**
     * Eliminar override
     */
    public function destroy(TourAvailability $availability)
    {
        try {
            $id = $availability->getKey();
            $availability->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_availability', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Override eliminado correctamente.');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_availability', $availability->getKey(), $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al eliminar override.');
        }
    }
}
