<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;
use Exception;

class TourScheduleController extends Controller
{
    /** Página principal: horarios generales + tours con sus horarios */
    public function index()
    {
        // Horarios generales (todos, para poder ver y editar/activar/desactivar/eliminar)
        $generalSchedules = Schedule::orderBy('start_time')->get();

        // Para el panel de gestión de horarios por tour, conviene ver TODOS los asignados (activos e inactivos)
        $tours = Tour::with(['schedules' => function ($q) {
            $q->orderBy('schedules.start_time');
        }])->orderBy('name')->get();

        return view('admin.tours.schedule.index', compact('generalSchedules', 'tours'));
    }

    /** Normaliza entradas de hora (e.g., "3:15 pm") a H:i */
    private function parseTime(?string $input): ?string
    {
        if (!$input) return null;

        $input = trim(strtolower($input));
        $formats = ['H:i', 'g:i a', 'g:iA', 'g:ia', 'g:i A', 'g:i'];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $input);
            if ($parsed !== false) {
                return $parsed->format('H:i');
            }
        }
        return null;
    }

    /**
     * Crear horario (general o para un tour)
     * - Si viene `tour_id`, se adjunta al tour con pivote is_active = true
     * - Si NO, queda como horario general
     */
    public function store(Request $request)
    {
        $request->merge([
            'start_time' => $this->parseTime($request->input('start_time')),
            'end_time'   => $this->parseTime($request->input('end_time')),
        ]);

        $validated = $request->validate([
            'tour_id'      => ['nullable', 'exists:tours,tour_id'],
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i', 'after:start_time'],
            'label'        => ['nullable', 'string', 'max:255'],
            'max_capacity' => ['required', 'integer', 'min:1'],
            'is_active'    => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$validated['is_active'] : true;

        try {
            $schedule = Schedule::create([
                'start_time'   => $validated['start_time'],
                'end_time'     => $validated['end_time'],
                'label'        => $validated['label'] ?? null,
                'max_capacity' => $validated['max_capacity'],
                'is_active'    => $validated['is_active'], // GLOBAL
            ]);

            if (!empty($validated['tour_id'])) {
                // asignación activa por defecto en la pivote
                $schedule->tours()->syncWithoutDetaching([
                    $validated['tour_id'] => ['is_active' => true],
                ]);
            }

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear horario: '.$e->getMessage());
            return back()->with('error', 'Hubo un problema al crear el horario.')->withInput();
        }
    }

    /** (Opcional) Vista de edición individual */
    public function edit(Schedule $schedule)
    {
        return view('admin.tours.schedule.edit', compact('schedule'));
    }

    /** Actualizar horario (global; no toca asociaciones) */
    public function update(Request $request, Schedule $schedule)
    {
        $request->merge([
            'start_time' => $this->parseTime($request->input('start_time')),
            'end_time'   => $this->parseTime($request->input('end_time')),
        ]);

        $validated = $request->validate([
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i', 'after:start_time'],
            'label'        => ['nullable', 'string', 'max:255'],
            'max_capacity' => ['required', 'integer', 'min:1'],
            'is_active'    => ['sometimes', 'boolean'],
        ]);

        try {
            $schedule->update([
                'start_time'   => $validated['start_time'],
                'end_time'     => $validated['end_time'],
                'label'        => $validated['label'] ?? null,
                'max_capacity' => $validated['max_capacity'],
                'is_active'    => $request->has('is_active') ? (bool)$validated['is_active'] : $schedule->is_active,
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar horario: '.$e->getMessage());
            return back()->with('error', 'Hubo un problema al actualizar el horario.')->withInput();
        }
    }

    /** Toggle GLOBAL (schedules.is_active) */
    public function toggle(Schedule $schedule)
    {
        try {
            $schedule->is_active = !$schedule->is_active;
            $schedule->save();

            $msg = $schedule->is_active
                ? 'Horario activado correctamente (global).'
                : 'Horario desactivado correctamente (global).';

            return back()->with('success', $msg);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado global del horario: '.$e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado global del horario.');
        }
    }

    /**
     * Toggle de la ASIGNACIÓN (pivote) — NO afecta global
     * Requiere schedule_tour.is_active (boolean)
     */
    public function toggleAssignment(Tour $tour, Schedule $schedule)
    {
        try {
            $rel = $tour->schedules()->where('schedules.schedule_id', $schedule->getKey())->first();
            if (!$rel) return back()->with('error', 'El horario no está asignado a este tour.');

            $current = (bool) ($rel->pivot->is_active ?? true);
            $tour->schedules()->updateExistingPivot($schedule->getKey(), ['is_active' => !$current]);

            return back()->with('success', !$current
                ? 'Asignación activada para este tour.'
                : 'Asignación desactivada para este tour.');
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de la asignación: '.$e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado de la asignación.');
        }
    }

    /** Adjuntar horario existente a un tour */
    public function attach(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,schedule_id'],
        ]);

        try {
            $tour->schedules()->syncWithoutDetaching([
                $data['schedule_id'] => ['is_active' => true],
            ]);

            return back()->with('success', 'Horario asignado al tour.');
        } catch (Exception $e) {
            Log::error('Error al asignar horario al tour: '.$e->getMessage());
            return back()->with('error', 'No se pudo asignar el horario al tour.');
        }
    }

    /** Quitar horario de un tour (DETACH) */
    public function detach(Tour $tour, Schedule $schedule)
    {
        try {
            $tour->schedules()->detach($schedule->getKey());
            return back()->with('success', 'Horario eliminado del tour correctamente.');
        } catch (Exception $e) {
            Log::error('Error al desasignar horario del tour: '.$e->getMessage());
            return back()->with('error', 'No se pudo desasignar el horario del tour.');
        }
    }

    /** Eliminar horario (hard delete, global) */
    public function destroy(Schedule $schedule)
    {
        try {
            // $schedule->tours()->detach(); // descomenta si NO tienes ON DELETE CASCADE en la pivote
            $schedule->delete();

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario eliminado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar horario: '.$e->getMessage());
            return back()->with('error', 'Hubo un problema al eliminar el horario.');
        }
    }
}
