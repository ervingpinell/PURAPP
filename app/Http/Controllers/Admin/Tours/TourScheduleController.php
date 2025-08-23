<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Tour;
use App\Models\Schedule;
use App\Services\LoggerHelper;
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
     * - Si viene `tour_id`, se adjunta al tour con pivote is_active = true
     * - Si NO, queda como horario general
     */
    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $schedule = Schedule::create([
                'start_time'   => $data['start_time'],
                'end_time'     => $data['end_time'],
                'label'        => $data['label'] ?? null,
                'max_capacity' => $data['max_capacity'],
                'is_active'    => $request->has('is_active') ? $request->boolean('is_active') : true,
            ]);

            if (!empty($data['tour_id'])) {
                $schedule->tours()->syncWithoutDetaching([
                    $data['tour_id'] => ['is_active' => true],
                ]);
            }

            LoggerHelper::mutated($this->controller, 'store', 'schedule', $schedule->getKey(), [
                'tour_id_attached' => $data['tour_id'] ?? null,
                'user_id'          => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario creado correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'schedule', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Hubo un problema al crear el horario.')->withInput();
        }
    }


    public function edit(Schedule $schedule)
    {
        return view('admin.tours.schedule.edit', compact('schedule'));
    }


    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        try {
            $data = $request->validated();

            $schedule->update([
                'start_time'   => $data['start_time'],
                'end_time'     => $data['end_time'],
                'label'        => $data['label'] ?? null,
                'max_capacity' => $data['max_capacity'],
                'is_active'    => $request->boolean('is_active'),
            ]);

            LoggerHelper::mutated($this->controller, 'update', 'schedule', $schedule->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario actualizado correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Hubo un problema al actualizar el horario.')->withInput();
        }
    }

    public function toggle(ToggleScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        try {
            $schedule->is_active = ! $schedule->is_active;
            $schedule->save();

            LoggerHelper::mutated($this->controller, 'toggle', 'schedule', $schedule->getKey(), [
                'is_active' => $schedule->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $msg = $schedule->is_active
                ? 'Horario activado correctamente (global).'
                : 'Horario desactivado correctamente (global).';

            return back()->with('success', $msg);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'No se pudo cambiar el estado global del horario.');
        }
    }


    public function toggleAssignment(ToggleScheduleAssignmentRequest $request, Tour $tour, Schedule $schedule): RedirectResponse
    {
        try {
            $rel = $tour->schedules()->where('schedules.schedule_id', $schedule->getKey())->first();

            if (!$rel) {
                return back()->with('error', 'El horario no está asignado a este tour.');
            }

            $current = (bool) ($rel->pivot->is_active ?? true);
            $tour->schedules()->updateExistingPivot($schedule->getKey(), ['is_active' => ! $current]);

            LoggerHelper::mutated($this->controller, 'toggleAssignment', 'tour_schedule_pivot', $schedule->getKey(), [
                'tour_id'        => $tour->getKey(),
                'schedule_id'    => $schedule->getKey(),
                'pivot_is_active'=> ! $current,
                'user_id'        => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', ! $current
                ? 'Asignación activada para este tour.'
                : 'Asignación desactivada para este tour.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggleAssignment', 'tour_schedule_pivot', $schedule->getKey(), $e, [
                'tour_id'  => $tour->getKey(),
                'user_id'  => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'No se pudo cambiar el estado de la asignación.');
        }
    }

    public function attach(AttachScheduleToTourRequest $request, Tour $tour): RedirectResponse
    {
        try {
            $data = $request->validated();

            $tour->schedules()->syncWithoutDetaching([
                $data['schedule_id'] => ['is_active' => true],
            ]);

            LoggerHelper::mutated($this->controller, 'attach', 'tour_schedule_pivot', $data['schedule_id'], [
                'tour_id' => $tour->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Horario asignado al tour.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'attach', 'tour_schedule_pivot', null, $e, [
                'tour_id' => $tour->getKey(),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'No se pudo asignar el horario al tour.');
        }
    }

    public function detach(Tour $tour, Schedule $schedule): RedirectResponse
    {
        try {
            $tour->schedules()->detach($schedule->getKey());

            LoggerHelper::mutated($this->controller, 'detach', 'tour_schedule_pivot', $schedule->getKey(), [
                'tour_id' => $tour->getKey(),
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Horario eliminado del tour correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'detach', 'tour_schedule_pivot', $schedule->getKey(), $e, [
                'tour_id' => $tour->getKey(),
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'No se pudo desasignar el horario del tour.');
        }
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        try {
            $id = $schedule->getKey();
            $schedule->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'schedule', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario eliminado correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'schedule', $schedule->getKey(), $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Hubo un problema al eliminar el horario.');
        }
    }
}
