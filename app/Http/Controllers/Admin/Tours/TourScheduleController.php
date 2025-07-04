<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Schedule;
use App\Models\Amenity;
use Exception;
use Illuminate\Support\Facades\Log;

class TourScheduleController extends Controller
{
    /** 📅 Mostrar todos los tours con sus horarios */
    public function index()
    {
        $schedules = Tour::with(['schedules' => function ($q) {
            $q->orderBy('start_time');
        }])->orderBy('tour_id')->get();

        return view('admin.tours.schedule.index', compact('schedules'));
    }

    /** 📌 Normaliza formatos flexibles */
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

    /** ➕ Registrar horario */
    public function store(Request $request)
    {
        $request->merge([
            'start_time' => $this->parseTime($request->input('start_time')),
            'end_time'   => $this->parseTime($request->input('end_time')),
        ]);

        $validated = $request->validate([
            'tour_id'     => 'required|exists:tours,tour_id',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'label'       => 'nullable|string|max:255',
            'is_active'   => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $validated['is_active'] : true;

        try {
            $schedule = Schedule::create($validated);

            // 👉 Relaciona el tour a través de la pivote
            $schedule->tours()->attach($request->tour_id);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario agregado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear horario: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al agregar el horario.');
        }
    }


    /** ✏️ Editar horario */
    public function edit(Schedule $schedule)
    {
        $tour = $schedule->tour;
        return view('admin.tours.schedule.edit', compact('schedule', 'tour'));
    }

    /** 🔄 Actualizar horario */
    public function update(Request $request, Schedule $schedule)
    {
        $request->merge([
            'start_time' => $this->parseTime($request->input('start_time')),
            'end_time'   => $this->parseTime($request->input('end_time')),
        ]);

        $validated = $request->validate([
            'tour_id'     => 'required|exists:tours,tour_id',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'label'       => 'nullable|string|max:255',
            'is_active'   => 'sometimes|boolean',
        ]);

        try {
            $schedule->update($validated);

            // 👉 Actualiza la relación en la tabla pivote
            $schedule->tours()->sync([$request->tour_id]);

            return redirect()->route('admin.tours.schedule.index')
                ->with('success', 'Horario actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar horario: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al actualizar el horario.');
        }
    }


    /** ✅ Cambiar estado activo/inactivo */
    public function toggle(Schedule $schedule)
    {
        try {
            $schedule->is_active = !$schedule->is_active;
            $schedule->save();

            $msg = $schedule->is_active ? 'Horario activado correctamente.' : 'Horario desactivado correctamente.';
            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del horario: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado del horario.');
        }
    }

    /** ❌ Desactivar horario */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->update(['is_active' => false]);
            return redirect()->route('admin.tours.schedule.index')->with('success', 'Horario desactivado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al desactivar horario: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al desactivar el horario.');
        }
    }

    /** 🔗 Sincronizar amenidades del tour */
    public function syncAmenities(Request $request, Tour $tour)
    {
        $request->validate([
            'amenities'   => 'array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $tour->amenities()->sync($request->input('amenities', []));

        return redirect()->route('admin.tours.edit', $tour->id)
            ->with('success', 'Amenidades actualizadas correctamente.');
    }
}
