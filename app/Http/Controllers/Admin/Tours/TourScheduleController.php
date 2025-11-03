<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Tour;
use App\Models\Schedule;
use App\Services\LoggerHelper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Tour\Schedule\StoreScheduleRequest;
use App\Http\Requests\Tour\Schedule\UpdateScheduleRequest;
use App\Http\Requests\Tour\Schedule\AttachScheduleToTourRequest;
use App\Http\Requests\Tour\Schedule\ToggleScheduleRequest;
use App\Http\Requests\Tour\Schedule\ToggleScheduleAssignmentRequest;

class TourScheduleController extends Controller
{
    protected string $controller = 'TourScheduleController';

    public function index()
    {
        $generalSchedules = Schedule::orderBy('start_time')->get();

        $tours = Tour::with([
            'schedules' => function ($q) {
                $q->orderBy('schedules.start_time');
            }
        ])->orderBy('name')->get();

        return view('admin.tours.schedule.index', compact('generalSchedules', 'tours'));
    }

    /**
     * Crear horario (general o para un tour)
     * - Si viene `tour_id`, se adjunta al tour con pivote is_active = true y opcional base_capacity
     * - Si NO, queda como horario general (sin capacidad en la tabla schedules)
     */
    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Crear schedule SIN max_capacity (ya no existe en la tabla)
            $schedule = Schedule::create([
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'label'      => $data['label'] ?? null,
                'is_active'  => $request->has('is_active') ? $request->boolean('is_active') : true,
            ]);

            // Si se adjunta a un tour, crear pivote con base_capacity opcional
            if (!empty($data['tour_id'])) {
                $pivotData = ['is_active' => true];

                if (!empty($data['base_capacity'])) {
                    $pivotData['base_capacity'] = (int) $data['base_capacity'];
                }

                $schedule->tours()->syncWithoutDetaching([
                    $data['tour_id'] => $pivotData
                ]);
            }

            LoggerHelper::mutated($this->controller, 'store', 'schedule', $schedule->getKey(), [
                'tour_id_attached' => $data['tour_id'] ?? null,
                'base_capacity'    => $data['base_capacity'] ?? null,
                'user_id'          => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', __('m_tours.schedule.success.created'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'schedule', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()
                ->with('error', __('m_tours.schedule.error.create'))
                ->withInput();
        }
    }

    public function edit(Schedule $schedule)
    {
        return view('admin.tours.schedule.edit', compact('schedule'));
    }

    /**
     * Actualizar horario general (SIN capacidad)
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Actualizar schedule SIN max_capacity
            $schedule->update([
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'label'      => $data['label'] ?? null,
                'is_active'  => $request->boolean('is_active'),
            ]);

            LoggerHelper::mutated($this->controller, 'update', 'schedule', $schedule->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', __('m_tours.schedule.success.updated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()
                ->with('error', __('m_tours.schedule.error.update'))
                ->withInput();
        }
    }

    /**
     * Toggle estado activo del schedule (global)
     */
    public function toggle(ToggleScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        try {
            $schedule->is_active = !$schedule->is_active;
            $schedule->save();

            LoggerHelper::mutated($this->controller, 'toggle', 'schedule', $schedule->getKey(), [
                'is_active' => $schedule->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $msg = $schedule->is_active
                ? __('m_tours.schedule.success.activated_global')
                : __('m_tours.schedule.success.deactivated_global');

            return back()->with('success', $msg);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.schedule.error.toggle'));
        }
    }

    /**
     * Toggle asignación de schedule a tour (pivote)
     */
    public function toggleAssignment(ToggleScheduleAssignmentRequest $request, Tour $tour, Schedule $schedule): RedirectResponse
    {
        try {
            $rel = $tour->schedules()->where('schedules.schedule_id', $schedule->getKey())->first();

            if (!$rel) {
                return back()->with('error', __('m_tours.schedule.error.not_assigned_to_tour'));
            }

            $current = (bool) ($rel->pivot->is_active ?? true);
            $tour->schedules()->updateExistingPivot($schedule->getKey(), ['is_active' => !$current]);

            LoggerHelper::mutated($this->controller, 'toggleAssignment', 'tour_schedule_pivot', $schedule->getKey(), [
                'tour_id'         => $tour->getKey(),
                'schedule_id'     => $schedule->getKey(),
                'pivot_is_active' => !$current,
                'user_id'         => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', !$current
                ? __('m_tours.schedule.success.assignment_activated')
                : __('m_tours.schedule.success.assignment_deactivated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggleAssignment', 'tour_schedule_pivot', $schedule->getKey(), $e, [
                'tour_id' => $tour->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.schedule.error.assignment_toggle'));
        }
    }

    /**
     * Asignar schedule existente a un tour (con capacidad opcional)
     */
    public function attach(AttachScheduleToTourRequest $request, Tour $tour): RedirectResponse
    {
        try {
            $data = $request->validated();

            $pivotData = ['is_active' => true];

            if (!empty($data['base_capacity'])) {
                $pivotData['base_capacity'] = (int) $data['base_capacity'];
            }

            $tour->schedules()->syncWithoutDetaching([
                $data['schedule_id'] => $pivotData
            ]);

            LoggerHelper::mutated($this->controller, 'attach', 'tour_schedule_pivot', $data['schedule_id'], [
                'tour_id'       => $tour->getKey(),
                'base_capacity' => $data['base_capacity'] ?? null,
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.schedule.success.attached'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'attach', 'tour_schedule_pivot', null, $e, [
                'tour_id' => $tour->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.schedule.error.attach'));
        }
    }

    /**
     * Actualizar capacidad del pivote (base_capacity)
     */
    public function updatePivotCapacity(Request $request, Tour $tour, Schedule $schedule): RedirectResponse
    {
        $request->validate([
            'base_capacity' => 'nullable|integer|min:1|max:999',
        ]);

        try {
            $tour->schedules()->updateExistingPivot($schedule->getKey(), [
                'base_capacity' => $request->base_capacity
            ]);

            LoggerHelper::mutated($this->controller, 'updatePivotCapacity', 'tour_schedule_pivot', $schedule->getKey(), [
                'tour_id'       => $tour->getKey(),
                'base_capacity' => $request->base_capacity,
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Capacidad del horario actualizada correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updatePivotCapacity', 'tour_schedule_pivot', $schedule->getKey(), $e, [
                'tour_id' => $tour->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al actualizar capacidad.');
        }
    }

    /**
     * Desasignar schedule de un tour
     */
    public function detach(Tour $tour, Schedule $schedule): RedirectResponse
    {
        try {
            $tour->schedules()->detach($schedule->getKey());

            LoggerHelper::mutated($this->controller, 'detach', 'tour_schedule_pivot', $schedule->getKey(), [
                'tour_id' => $tour->getKey(),
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.schedule.success.detached'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'detach', 'tour_schedule_pivot', $schedule->getKey(), $e, [
                'tour_id' => $tour->getKey(),
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.schedule.error.detach'));
        }
    }

    /**
     * Eliminar schedule general completamente
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        try {
            $id = $schedule->getKey();
            $schedule->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'schedule', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', __('m_tours.schedule.success.deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.schedule.error.delete'));
        }
    }
}
