<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\TourType;
use App\Models\Itinerary;
use App\Models\TourLanguage;
use App\Models\Amenity;
use Illuminate\Support\Facades\Log;
use Exception;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with(['tourType', 'languages', 'amenities', 'schedules', 'itinerary.items'])->get();
        $tourtypes = TourType::all();
        $itineraries = Itinerary::all();
        $languages = TourLanguage::all();
        $amenities = Amenity::all();

        return view('admin.tours.index', compact('tours', 'tourtypes', 'languages', 'amenities', 'itineraries'));
    }

    public function store(Request $request)
    {
        if ($request->itinerary_id === 'new') {
             $request->validate([
        'new_itinerary_name' => 'required|string|max:255',
        'itinerary.*.title' => 'required|string',
        'itinerary.*.description' => 'required|string',
    ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'overview' => 'nullable|string',
            'description' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'nullable|numeric|min:0',
            'length' => 'required|numeric|min:1',
            'tour_type_id' => 'required|exists:tour_types,tour_type_id',
            'itinerary_id' => 'nullable|exists:itineraries,itinerary_id',
            'languages' => 'required|array|min:1',
            'languages.*' => 'exists:tour_languages,tour_language_id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenity_id',
            'schedule_am_start' => 'nullable|date_format:H:i',
            'schedule_am_end' => 'nullable|date_format:H:i',
            'schedule_pm_start' => 'nullable|date_format:H:i',
            'schedule_pm_end' => 'nullable|date_format:H:i',
            'itinerary' => 'nullable|array',
            'itinerary.*.title' => 'required_with:itinerary|string|max:255',
            'itinerary.*.description' => 'required_with:itinerary|string',
            'new_itinerary_name' => 'nullable|string|max:255',
        ]);

        if (empty($validated['itinerary_id']) && empty($validated['itinerary'])) {
            return back()->withErrors(['itinerary_id' => 'Debes seleccionar un itinerario o crear uno nuevo.']);
        }

        $tourType = TourType::find($validated['tour_type_id']);

        if ($tourType->name === 'Full Day') {
            if (empty($validated['schedule_am_start']) || empty($validated['schedule_am_end'])) {
                return back()->withErrors(['schedule_am_start' => 'Un tour Full Day requiere horario AM completo.']);
            }
            if (!empty($validated['schedule_pm_start']) || !empty($validated['schedule_pm_end'])) {
                return back()->withErrors(['schedule_pm_start' => 'Un tour Full Day no puede tener horario PM.']);
            }
        }

        try {
          if (!empty($validated['itinerary'])) {
    $nuevoItinerario = Itinerary::create([
        'name' => $request->new_itinerary_name ?? ($validated['name'] . ' - Itinerario Generado'),
    ]);

                foreach ($validated['itinerary'] as $index => $item) {
                    $nuevoItinerario->items()->create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'order' => $index,
                        'is_active' => true,
                    ]);
                }

                $validated['itinerary_id'] = $nuevoItinerario->itinerary_id;
            }

            $tour = Tour::create([
                'name'          => $validated['name'],
                'description'   => $validated['description'] ?? '',
                'overview'      => $validated['overview'] ?? '',
                'adult_price'   => $validated['adult_price'],
                'kid_price'     => $validated['kid_price'] ?? 0,
                'length'        => $validated['length'],
                'tour_type_id'  => $validated['tour_type_id'],
                'itinerary_id'  => $validated['itinerary_id'],
                'is_active'     => true,
            ]);

            $tour->languages()->sync($validated['languages']);
            $tour->amenities()->sync($validated['amenities'] ?? []);

            if (!empty($validated['schedule_am_start']) && !empty($validated['schedule_am_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_am_start'],
                    'end_time'   => $validated['schedule_am_end'],
                ]);
            }
            if (!empty($validated['schedule_pm_start']) && !empty($validated['schedule_pm_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_pm_start'],
                    'end_time'   => $validated['schedule_pm_end'],
                ]);
            }

            return redirect()
                ->route('admin.tours.index')
                ->with('success', 'Tour creado correctamente.')
                ->with('alert_type', 'creado');

        } catch (Exception $e) {
            Log::error('Error al crear tour: ' . $e->getMessage());
            return back()
                ->with('error', 'Hubo un problema al crear el tour.')
                ->withInput();
        }
    }

    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'overview' => 'nullable|string',
            'description' => 'nullable|string',
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
        ]);

        try {
            $tour->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? '',
                'overview'    => $validated['overview'] ?? '',
                'adult_price' => $validated['adult_price'],
                'kid_price'   => $validated['kid_price'] ?? 0,
                'length'      => $validated['length'],
                'tour_type_id' => $validated['tour_type_id'],
            ]);

            $tour->languages()->sync($validated['languages']);
            $tour->amenities()->sync($validated['amenities'] ?? []);

            $tour->schedules()->delete();

            if (!empty($validated['schedule_am_start']) && !empty($validated['schedule_am_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_am_start'],
                    'end_time'   => $validated['schedule_am_end'],
                ]);
            }
            if (!empty($validated['schedule_pm_start']) && !empty($validated['schedule_pm_end'])) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_pm_start'],
                    'end_time'   => $validated['schedule_pm_end'],
                ]);
            }

            return redirect()
                ->route('admin.tours.index')
                ->with('success', 'Tour actualizado correctamente.')
                ->with('alert_type', 'actualizado');

        } catch (Exception $e) {
            Log::error('Error al actualizar tour: ' . $e->getMessage());
            return back()
                ->with('error', 'Hubo un problema al actualizar el tour.')
                ->withInput();
        }
    }

    public function destroy(Tour $tour)
    {
        try {
            $tour->is_active = !$tour->is_active;
            $tour->save();

            $message = $tour->is_active ? 'Tour activado correctamente.' : 'Tour desactivado correctamente.';
            $alertType = $tour->is_active ? 'activado' : 'desactivado';

            return redirect()
                ->route('admin.tours.index')
                ->with('success', $message)
                ->with('alert_type', $alertType);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al cambiar el estado del tour.');
        }
    }
}
