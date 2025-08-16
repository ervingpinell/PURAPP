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
        $generalSchedules = Schedule::orderBy('start_time')->get();

        $tours = Tour::with([
            'schedules' => function ($q) {
                $q->orderBy('schedules.start_time');
            }
        ])->orderBy('name')->get();

        return view('admin.tours.schedule.index', compact('generalSchedules', 'tours'));
    }

    /** Normaliza entradas de hora (e.g., "3:15 pm") a H:i */
    private function parseTime(?string $input): ?string
    {
        if (!$input) return null;

        $input = trim($input);

        // Si ya viene con segundos válidos, lo pasamos a H:i
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $input)) {
            return \DateTime::createFromFormat('H:i:s', $input)?->format('H:i') ?? null;
        }

        // Formatos comunes
        $candidates = [
            'H:i',
            'g:i a', 'g:iA', 'g:ia', 'g:i A',
            'g a', 'gA', 'ga', 'g A', // 3 pm
            'H:i \h',                  // 13:00 h
        ];

        foreach ($candidates as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, strtolower($input));
            if ($dt !== false) {
                return $dt->format('H:i');
            }
        }

        return null;
    }

    /** Mensajes de validación en ES */
    private function validationMessages(): array
    {
        return [
            'tour_id.exists'          => 'El tour seleccionado no existe.',
            'start_time.required'     => 'El campo "Inicio" es obligatorio.',
            'start_time.date_format'  => 'El campo "Inicio" debe tener el formato HH:MM (24h).',
            'end_time.required'       => 'El campo "Fin" es obligatorio.',
            'end_time.date_format'    => 'El campo "Fin" debe tener el formato HH:MM (24h).',
            'end_time.after'          => 'El campo "Fin" debe ser posterior al campo "Inicio".',
            'label.string'            => 'La etiqueta debe ser texto.',
            'label.max'               => 'La etiqueta no puede superar 255 caracteres.',
            'max_capacity.required'   => 'La capacidad máxima es obligatoria.',
            'max_capacity.integer'    => 'La capacidad máxima debe ser un número entero.',
            'max_capacity.min'        => 'La capacidad máxima debe ser al menos 1.',
            'is_active.boolean'       => 'El estado debe ser verdadero o falso.',
        ];
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
            'is_active'    => ['nullable', 'boolean'],
        ], $this->validationMessages());

        // Checkbox: por defecto true si no viene (crear nuevo)
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;

        try {
            $schedule = Schedule::create([
                'start_time'   => $validated['start_time'],  // el mutator añadirá :00 si hace falta
                'end_time'     => $validated['end_time'],
                'label'        => $validated['label'] ?? null,
                'max_capacity' => $validated['max_capacity'],
                'is_active'    => $isActive,                 // GLOBAL
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
            Log::error('Error al crear horario: '.$e->getMessage(), ['exception' => $e]);
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
            'is_active'    => ['nullable', 'boolean'],
        ], $this->validationMessages());

        try {
            // si el checkbox viene desmarcado, guardamos false
            $isActive = $request->boolean('is_active');

            $schedule->update([
                'start_time'   => $validated['start_time'],
                'end_time'     => $validated['end_time'],
                'label'        => $validated['label'] ?? null,
                'max_capacity' => $validated['max_capacity'],
                'is_active'    => $isActive,
            ]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar horario #'.$schedule->getKey().': '.$e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Hubo un problema al actualizar el horario.')->withInput();
        }
    }

    /** Toggle GLOBAL (schedules.is_active) */
    public function toggle(Schedule $schedule)
    {
        try {
            $schedule->is_active = ! $schedule->is_active;
            $schedule->save();

            $msg = $schedule->is_active
                ? 'Horario activado correctamente (global).'
                : 'Horario desactivado correctamente (global).';

            return back()->with('success', $msg);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado global del horario #'.$schedule->getKey().': '.$e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'No se pudo cambiar el estado global del horario.');
        }
    }

    /**
     * Toggle de la ASIGNACIÓN (pivote) — NO afecta global
     */
    public function toggleAssignment(Tour $tour, Schedule $schedule)
    {
        try {
            $rel = $tour->schedules()->where('schedules.schedule_id', $schedule->getKey())->first();

            if (!$rel) {
                return back()->with('error', 'El horario no está asignado a este tour.');
            }

            $current = (bool) ($rel->pivot->is_active ?? true);
            $tour->schedules()->updateExistingPivot($schedule->getKey(), ['is_active' => ! $current]);

            return back()->with('success', ! $current
                ? 'Asignación activada para este tour.'
                : 'Asignación desactivada para este tour.');
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de la asignación tour#'.$tour->getKey().' schedule#'.$schedule->getKey().': '.$e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'No se pudo cambiar el estado de la asignación.');
        }
    }

    /** Adjuntar horario existente a un tour */
    public function attach(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,schedule_id'],
        ], [
            'schedule_id.required' => 'Debes seleccionar un horario.',
            'schedule_id.exists'   => 'El horario seleccionado no existe.',
        ]);

        try {
            $tour->schedules()->syncWithoutDetaching([
                $data['schedule_id'] => ['is_active' => true],
            ]);

            return back()->with('success', 'Horario asignado al tour.');
        } catch (Exception $e) {
            Log::error('Error al asignar horario al tour#'.$tour->getKey().': '.$e->getMessage(), ['exception' => $e]);
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
            Log::error('Error al desasignar horario del tour#'.$tour->getKey().' schedule#'.$schedule->getKey().': '.$e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'No se pudo desasignar el horario del tour.');
        }
    }

    /** Eliminar horario (hard delete, global) */
    public function destroy(Schedule $schedule)
    {
        try {
            // $schedule->tours()->detach(); // si no tienes ON DELETE CASCADE en pivot
            $schedule->delete();

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario eliminado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar horario #'.$schedule->getKey().': '.$e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Hubo un problema al eliminar el horario.');
        }
    }
}
