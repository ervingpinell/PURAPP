<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Category;
use App\Models\TourLanguage;
use App\Models\Amenity;
use App\Models\TourSchedule;
use App\Models\ItineraryItem;
use Illuminate\Support\Facades\Log;

use Exception;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with(['category', 'languages', 'amenities', 'schedules'])->get();
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
            'languages' => 'required|array|min:1',
            'languages.*' => 'exists:tour_languages,tour_language_id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenity_id',
            'schedule_am_start' => 'nullable|date_format:H:i',
            'schedule_am_end' => 'nullable|date_format:H:i|after:schedule_am_start',
            'schedule_pm_start' => 'nullable|date_format:H:i',
            'schedule_pm_end' => 'nullable|date_format:H:i|after:schedule_pm_start',
            'itinerary' => 'nullable|array',
            'itinerary.*.title' => 'required|string|max:255',
            'itinerary.*.description' => 'required|string',
        ]);

        try {
            $tour = Tour::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'adult_price' => $validated['adult_price'],
                'kid_price' => $validated['kid_price'] ?? 0,
                'length' => $validated['length'],
                'category_id' => $validated['category_id'],
                'is_active' => true,
            ]);

            $tour->languages()->sync($validated['languages']);
            $tour->amenities()->sync($validated['amenities'] ?? []);

            if (!empty($validated['schedule_am_start']) && !empty($validated['schedule_am_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_am_start'],
                    'end_time' => $validated['schedule_am_end'],
                ]);
            }

            if (!empty($validated['schedule_pm_start']) && !empty($validated['schedule_pm_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_pm_start'],
                    'end_time' => $validated['schedule_pm_end'],
                ]);
            }

            if (!empty($validated['itinerary'])) {
                foreach ($validated['itinerary'] as $index => $item) {
                    $tour->itineraryItems()->create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'order' => $index,
                        'is_active' => true,
                    ]);
                }
            }

            return redirect()->route('admin.tours.index')->with('success', 'Tour agregado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al agregar el tour.')->withInput();
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
            'languages' => 'required|array|min:1',
            'languages.*' => 'exists:tour_languages,tour_language_id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenity_id',
            'schedule_am_start' => 'nullable|date_format:H:i',
            'schedule_am_end' => 'nullable|date_format:H:i|after:schedule_am_start',
            'schedule_pm_start' => 'nullable|date_format:H:i',
            'schedule_pm_end' => 'nullable|date_format:H:i|after:schedule_pm_start',
            'itinerary' => 'nullable|array',
            'itinerary.*.title' => 'required|string|max:255',
            'itinerary.*.description' => 'required|string',
        ]);

        try {
            $tour->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'adult_price' => $validated['adult_price'],
                'kid_price' => $validated['kid_price'] ?? 0,
                'length' => $validated['length'],
                'category_id' => $validated['category_id'],
            ]);

            $tour->languages()->sync($validated['languages']);
            $tour->amenities()->sync($validated['amenities'] ?? []);

            $tour->schedules()->delete();
            if (!empty($validated['schedule_am_start']) && !empty($validated['schedule_am_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_am_start'],
                    'end_time' => $validated['schedule_am_end'],
                ]);
            }
            if (!empty($validated['schedule_pm_start']) && !empty($validated['schedule_pm_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_pm_start'],
                    'end_time' => $validated['schedule_pm_end'],
                ]);
            }

            $tour->itineraryItems()->delete();
            if (!empty($validated['itinerary'])) {
                foreach ($validated['itinerary'] as $index => $item) {
                    $tour->itineraryItems()->create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'order' => $index,
                        'is_active' => true,
                    ]);
                }
            }

            return redirect()->route('admin.tours.index')->with('success', 'Tour actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al actualizar el tour.')->withInput();
        }
    }

    public function destroy(Tour $tour)
    {
        try {
            $tour->delete();
            return redirect()->route('admin.tours.index')->with('success', 'Tour eliminado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al eliminar el tour.');
        }
    }
}
