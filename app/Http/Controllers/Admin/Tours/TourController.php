<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Category;
use App\Models\TourLanguage;
use App\Models\Amenity;
use App\Models\TourSchedule;
use Illuminate\Support\Facades\Log;
use Exception;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with(['category', 'language', 'amenities', 'schedules'])->get();
        $categories = Category::all();
        $languages = TourLanguage::all();
        $amenities = Amenity::all();

        return view('admin.tours.index', compact('tours', 'categories', 'languages', 'amenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'nullable|numeric|min:0',
            'length' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenity_id',
            'schedules' => 'nullable|array',
            'schedules.*.day' => 'required_with:schedules|string',
            'schedules.*.start_time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time' => 'required_with:schedules|date_format:H:i',
        ]);

        // Validar que end_time > start_time manualmente
        foreach ($request->schedules ?? [] as $i => $schedule) {
            if (isset($schedule['start_time'], $schedule['end_time']) &&
                $schedule['end_time'] <= $schedule['start_time']) {
                return back()->withErrors([
                    "schedules.$i.end_time" => "La hora de fin debe ser mayor que la de inicio."
                ])->withInput();
            }
        }

        try {
            $validated['is_active'] = true;
            $tour = Tour::create($validated);

            if (!empty($validated['amenities'])) {
                $tour->amenities()->sync($validated['amenities']);
            }

            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $scheduleData) {
                    $tour->schedules()->create([
                        'day' => $scheduleData['day'],
                        'start_time' => $scheduleData['start_time'],
                        'end_time' => $scheduleData['end_time'],
                    ]);
                }
            }

            return redirect()->route('admin.tours.index')->with('success', 'Tour agregado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear tour: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al agregar el tour.');
        }
    }

    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'nullable|numeric|min:0',
            'length' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenity_id',
            'schedules' => 'nullable|array',
            'schedules.*.day' => 'required_with:schedules|string',
            'schedules.*.start_time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time' => 'required_with:schedules|date_format:H:i',
        ]);

        foreach ($request->schedules ?? [] as $i => $schedule) {
            if (isset($schedule['start_time'], $schedule['end_time']) &&
                $schedule['end_time'] <= $schedule['start_time']) {
                return back()->withErrors([
                    "schedules.$i.end_time" => "La hora de fin debe ser mayor que la de inicio."
                ])->withInput();
            }
        }

        try {
            $tour->update($validated);

            $tour->amenities()->sync($validated['amenities'] ?? []);

            $tour->schedules()->delete();
            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $scheduleData) {
                    $tour->schedules()->create([
                        'day' => $scheduleData['day'],
                        'start_time' => $scheduleData['start_time'],
                        'end_time' => $scheduleData['end_time'],
                    ]);
                }
            }

            return redirect()->route('admin.tours.index')->with('success', 'Tour actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar tour: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al actualizar el tour.');
        }
    }

    public function destroy(Tour $tour)
    {
        try {
            $tour->delete();
            return redirect()->route('admin.tours.index')->with('success', 'Tour eliminado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar tour: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al eliminar el tour.');
        }
    }
}
