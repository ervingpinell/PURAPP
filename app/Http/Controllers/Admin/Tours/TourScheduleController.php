<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourSchedule;
use App\Models\Tour;
use App\Models\Amenity;
use Exception;
use Illuminate\Support\Facades\Log;

class TourScheduleController extends Controller
{
    public function index()
    {
        // Cargamos todos los tours con sus horarios ordenados por tour_id
        $schedules = Tour::with('schedules')->orderBy('tour_id')->get();

        return view('admin.tours.schedule.index', compact('schedules'));
    }

    public function create()
    {
        $tours = Tour::orderBy('tour_id')->get();
        return view('admin.tours.schedules.create', compact('tours'));
    }
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

   public function store(Request $request)
{
    // Normalizar formatos flexibles de hora
    $request->merge([
        'start_time' => $this->parseTime($request->input('start_time')),
        'end_time' => $this->parseTime($request->input('end_time')),
    ]);

    $validated = $request->validate([
        'tour_id' => 'required|exists:tours,tour_id',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'label' => 'nullable|string|max:255',
        'is_active' => 'sometimes|boolean',
    ]);

    $tour = Tour::with('schedules')->findOrFail($validated['tour_id']);

    $startHour = intval(date('H', strtotime($validated['start_time'])));
    $isAM = $startHour < 12;

    $conflicting = $tour->schedules->filter(function ($s) use ($isAM) {
        $h = intval(date('H', strtotime($s->start_time)));
        return $isAM ? $h < 12 : $h >= 12;
    });

    if ($conflicting->count()) {
        return back()->withErrors(['start_time' => 'Ya existe un horario ' . ($isAM ? 'AM' : 'PM') . ' para este tour.']);
    }

    try {
        $validated['is_active'] = $request->has('is_active') ? $validated['is_active'] : true;
        TourSchedule::create($validated);

        return redirect()->route('admin.tours.schedule.index')->with('success', 'Horario agregado correctamente.');
    } catch (Exception $e) {
        Log::error('Error al crear horario: ' . $e->getMessage());
        return back()->with('error', 'Hubo un problema al agregar el horario.');
    }
}

    public function edit(TourSchedule $schedule)
    {
        $tour = $schedule->tour;
        $amenities = Amenity::all();
        $tourAmenities = $tour->amenities->pluck('id')->toArray();

        return view('admin.tours.edit', compact('tour', 'amenities', 'tourAmenities'));
    }

    public function update(Request $request, TourSchedule $schedule)
    {
        $request->merge([
    'start_time' => $this->parseTime($request->input('start_time')),
    'end_time' => $this->parseTime($request->input('end_time')),
]);
        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'label' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $tour = Tour::with('schedules')->findOrFail($validated['tour_id']);

        $startHour = intval(date('H', strtotime($validated['start_time'])));
        $isAM = $startHour < 12;

        // Evita conflictos con el mismo bloque horario
        $conflicting = $tour->schedules->filter(function ($s) use ($isAM, $schedule) {
            $h = intval(date('H', strtotime($s->start_time)));
            return $s->tour_schedule_id !== $schedule->tour_schedule_id && ($isAM ? $h < 12 : $h >= 12);
        });

        if ($conflicting->count()) {
            return back()->withErrors(['start_time' => 'Ya existe un horario ' . ($isAM ? 'AM' : 'PM') . ' para este tour.']);
        }

        try {
            $schedule->update($validated);
            return redirect()->route('admin.tours.schedule.index')->with('success', 'Horario actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar horario: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al actualizar el horario.');
        }
    }

    public function toggle(TourSchedule $schedule)
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

    public function destroy(TourSchedule $schedule)
    {
        try {
            $schedule->update(['is_active' => false]);
            return redirect()->route('admin.tours.schedule.index')->with('success', 'Horario desactivado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al desactivar horario: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al desactivar el horario.');
        }
    }

    public function syncAmenities(Request $request, Tour $tour)
    {
        $request->validate([
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $tour->amenities()->sync($request->input('amenities', []));

        return redirect()->route('admin.tours.edit', $tour->id)
            ->with('success', 'Amenidades actualizadas correctamente.');
    }



}
