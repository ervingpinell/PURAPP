<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourAvailability;
use App\Models\Tour;
use Exception;
use Illuminate\Support\Facades\Log;

class TourAvailabilityController extends Controller
{
    public function index()
    {
    $availabilities = TourAvailability::with('tour')->paginate(10);
  return view('admin.tours.availability.index', compact('availabilities'));
    }

    public function create()
    {
        $tours = Tour::all();
        return view('admin.tours.availabilities.create', compact('tours'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'available' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $validated['available'] = $request->has('available') ? $validated['available'] : true;
            $validated['is_active'] = $request->has('is_active') ? $validated['is_active'] : true;

            TourAvailability::create($validated);

            return redirect()->route('admin.tours.availabilities.index')->with('success', 'Disponibilidad agregada correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear disponibilidad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al agregar la disponibilidad.');
        }
    }

    public function edit(TourAvailability $availability)
    {
        $tours = Tour::all();
        return view('admin.tours.availabilities.edit', compact('availability', 'tours'));
    }

    public function update(Request $request, TourAvailability $availability)
    {
        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'available' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $availability->update($validated);
            return redirect()->route('admin.tours.availabilities.index')->with('success', 'Disponibilidad actualizada correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar disponibilidad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al actualizar la disponibilidad.');
        }
    }

public function destroy(TourAvailability $availability)
{
    try {
        $availability->update(['is_active' => false]);
        return redirect()->route('admin.tours.availabilities.index')->with('success', 'Disponibilidad desactivada correctamente.');
    } catch (Exception $e) {
        Log::error('Error al desactivar disponibilidad: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Hubo un problema al desactivar la disponibilidad.');
    }
}
}
