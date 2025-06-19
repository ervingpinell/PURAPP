<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Tour;
use App\Models\TourType;
use App\Models\Itinerary;
use App\Models\TourLanguage;
use App\Models\Amenity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\ItineraryService;

class TourController extends Controller
{
    public function index()
    {
$tours = Tour::with([
    'tourType',
    'languages',
    'amenities',
    'itinerary.items',
    'schedules' => function ($query) {
        $query->where('is_active', true);
    },
])->orderBy('tour_id', 'asc')->get(); // 👈 Aquí agregas el orden
        $tourtypes = TourType::all();
        $itineraries = Itinerary::all();
        $languages = TourLanguage::all();
        $amenities = Amenity::all();
        $availableItems = (new ItineraryService)->getAvailableItems();

        return view('admin.tours.index', compact(
            'tours', 'tourtypes', 'itineraries', 'languages', 'amenities', 'availableItems',
        ));
    }


    public function edit(Tour $tour)
    {
        $tourtypes = TourType::all();
        $itineraries = Itinerary::with('items')->get();
        $languages = TourLanguage::all();
        $amenities = Amenity::all();
        $availableItems = (new ItineraryService)->getAvailableItems();

        return view('admin.tours.edit', compact(
            'tour', 'tourtypes', 'itineraries', 'languages', 'amenities', 'availableItems'
        ));
    }

    
    public function store(Request $request)
    {
        $request->merge([
            'itinerary_combined' => array_merge(
                $request->input('existing_item_ids', []),
                array_values(array_filter($request->input('itinerary', []), fn($i) => is_array($i) && !empty($i['title'])))
            )
        ]);

        $rules = [
            'name' => 'required|string|max:255',
            'overview' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'nullable|numeric|min:0',
            'length' => 'required|numeric|min:1',
            'tour_type_id' => 'required|exists:tour_types,tour_type_id',
            'itinerary_id' => 'nullable',
            'languages' => 'required|array|min:1',
            'languages.*' => 'exists:tour_languages,tour_language_id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenity_id',
            'schedule_am_start' => 'nullable|date_format:H:i',
            'schedule_am_end' => 'nullable|date_format:H:i',
            'schedule_pm_start' => 'nullable|date_format:H:i',
            'schedule_pm_end' => 'nullable|date_format:H:i',
            'new_itinerary_name' => 'nullable|string|max:255',
        ];

        if ($request->input('itinerary_id') === 'new') {
            $rules['itinerary_combined'] = 'required|array|min:1';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('showCreateModal', true);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use (&$validated) {
                $itineraryService = new ItineraryService();
                $itinerary = $itineraryService->handleCreationOrAssignment($validated);

                $tour = Tour::create([
                    'name' => $validated['name'],
                    'overview' => $validated['overview'] ?? '',
                    'adult_price' => $validated['adult_price'],
                    'kid_price' => $validated['kid_price'] ?? 0,
                    'length' => $validated['length'],
                    'tour_type_id' => $validated['tour_type_id'],
                    'itinerary_id' => $itinerary?->itinerary_id,
                    'is_active' => true,
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
            });

            return redirect()->route('admin.tours.index')->with('success', 'Tour creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al crear el tour.')->withInput()->with('showCreateModal', true);
        }
    }

    public function update(Request $request, Tour $tour)
    {
        $request->merge([
            'itinerary_combined' => array_merge(
                $request->input('existing_item_ids', []),
                array_values(array_filter($request->input('itinerary', []), fn($i) => is_array($i) && !empty($i['title'])))
            )
        ]);

        $rules = [
            'name' => 'required|string|max:255',
            'overview' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'nullable|numeric|min:0',
            'length' => 'required|numeric|min:1',
            'tour_type_id' => 'required|exists:tour_types,tour_type_id',
            'languages' => 'required|array|min:1',
            'languages.*' => 'exists:tour_languages,tour_language_id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenity_id',
            'schedule_am_start' => 'nullable|date_format:H:i',
            'schedule_am_end' => 'nullable|date_format:H:i',
            'schedule_pm_start' => 'nullable|date_format:H:i',
            'schedule_pm_end' => 'nullable|date_format:H:i',
            'itinerary_id' => 'nullable',
            'new_itinerary_name' => 'nullable|string|max:255',
        ];

        if ($request->input('itinerary_id') === 'new') {
            $rules['itinerary_combined'] = 'required|array|min:1';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('showEditModal', $tour->tour_id);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($tour, &$validated) {
                $tour->update([
                    'name' => $validated['name'],
                    'overview' => $validated['overview'] ?? '',
                    'adult_price' => $validated['adult_price'],
                    'kid_price' => $validated['kid_price'] ?? 0,
                    'length' => $validated['length'],
                    'tour_type_id' => $validated['tour_type_id'],
                ]);

                $itineraryService = new ItineraryService();
                $itinerary = $itineraryService->handleCreationOrAssignment($validated);

                if ($itinerary) {
                    $tour->itinerary()->associate($itinerary);
                    $tour->save();
                }

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
            });

            return redirect()->route('admin.tours.index')->with('success', 'Tour actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al actualizar el tour.')->withInput()->with('showEditModal', $tour->tour_id);
        }
    }

    public function destroy(Tour $tour)
    {
        try {
            $tour->is_active = !$tour->is_active;
            $tour->save();

            $msg = $tour->is_active
                ? 'Tour activado correctamente.'
                : 'Tour desactivado correctamente.';

            return redirect()->route('admin.tours.index')->with('success', $msg);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al cambiar el estado del tour.');
        }
    }
}
