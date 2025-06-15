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
        $schedules = TourSchedule::with('tour')->paginate(10);
        return view('admin.tours.schedule.index', compact('schedules'));
    }

    public function create()
    {
        $tours = Tour::all();
        return view('admin.tours.schedules.create', compact('tours'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'start_time' => 'required|date_format:H:i',
            'label' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $validated['is_active'] = $request->has('is_active') ? $validated['is_active'] : true;
            TourSchedule::create($validated);

            return redirect()->route('admin.tours.schedules.index')->with('success', 'Horario agregado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear horario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al agregar el horario.');
        }
    }

    public function edit(TourSchedule $schedule)
    {
    $tour = $schedule->tour; // Accede al Tour relacionado

    $amenities = Amenity::all();
    $tourAmenities = $tour->amenities->pluck('id')->toArray();

    return view('admin.tours.edit', compact('tour', 'amenities', 'tourAmenities'));
    }

    public function update(Request $request, TourSchedule $schedule)
    {
        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'start_time' => 'required|date_format:H:i',
            'label' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $schedule->update($validated);
            return redirect()->route('admin.tours.schedules.index')->with('success', 'Horario actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar horario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al actualizar el horario.');
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

public function destroy(TourSchedule $schedule)
{
    try {
        $schedule->update(['is_active' => false]);
        return redirect()->route('admin.tours.schedules.index')->with('success', 'Horario desactivado correctamente.');
    } catch (Exception $e) {
        Log::error('Error al desactivar horario: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Hubo un problema al desactivar el horario.');
    }
}

}
