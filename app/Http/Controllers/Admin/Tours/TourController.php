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

class TourController extends Controller
{
    public function index()
    {
        $tours        = Tour::with(['tourType', 'languages', 'amenities', 'schedules', 'itinerary.items'])->get();
        $tourtypes    = TourType::all();
        $itineraries  = Itinerary::all();
        $languages    = TourLanguage::all();
        $amenities    = Amenity::all();

        return view('admin.tours.index', compact(
            'tours','tourtypes','itineraries','languages','amenities'
        ));
    }

    public function store(Request $request)
    {
        // armamos el validador manualmente para capturar el fallo y "flaggear" el modal
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'overview'           => 'nullable|string',
            'description'        => 'nullable|string',
            'adult_price'        => 'required|numeric|min:0',
            'kid_price'          => 'nullable|numeric|min:0',
            'length'             => 'required|numeric|min:1',
            'tour_type_id'       => 'required|exists:tour_types,tour_type_id',
            'itinerary_id'       => 'nullable|exists:itineraries,itinerary_id',
            'languages'          => 'required|array|min:1',
            'languages.*'        => 'exists:tour_languages,tour_language_id',
            'amenities'          => 'nullable|array',
            'amenities.*'        => 'exists:amenities,amenity_id',
            'schedule_am_start'  => 'nullable|date_format:H:i',
            'schedule_am_end'    => 'nullable|date_format:H:i',
            'schedule_pm_start'  => 'nullable|date_format:H:i',
            'schedule_pm_end'    => 'nullable|date_format:H:i',
            'itinerary'          => 'nullable|array',
            'itinerary.*.title'       => 'required_with:itinerary|string|max:255',
            'itinerary.*.description' => 'required_with:itinerary|string',
            'new_itinerary_name'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showCreateModal', true);
        }

        $validated = $validator->validated();

        // validación extra: full day
        $tourType = TourType::find($validated['tour_type_id']);
        if ($tourType->name === 'Full Day') {
            if (empty($validated['schedule_am_start']) || empty($validated['schedule_am_end'])) {
                return back()
                    ->withErrors(['schedule_am_start' => 'Un tour Full Day requiere horario AM completo.'])
                    ->withInput()
                    ->with('showCreateModal', true);
            }
            if (!empty($validated['schedule_pm_start']) || !empty($validated['schedule_pm_end'])) {
                return back()
                    ->withErrors(['schedule_pm_start' => 'Un tour Full Day no puede tener horario PM.'])
                    ->withInput()
                    ->with('showCreateModal', true);
            }
        }

        try {
            // si hay ítems nuevos, los creamos primero
            if (!empty($validated['itinerary'])) {
                $nuevoItinerario = Itinerary::create([
                    'name' => $validated['new_itinerary_name']
                              ?? ($validated['name'].' - Itinerario Generado'),
                ]);
                foreach ($validated['itinerary'] as $idx => $item) {
                    $nuevoItinerario->items()->create([
                        'title'       => $item['title'],
                        'description' => $item['description'],
                        'order'       => $idx,
                        'is_active'   => true,
                    ]);
                }
                $validated['itinerary_id'] = $nuevoItinerario->itinerary_id;
            }

            // creamos el Tour
            $tour = Tour::create([
                'name'         => $validated['name'],
                'overview'     => $validated['overview'] ?? '',
                'description'  => $validated['description'] ?? '',
                'adult_price'  => $validated['adult_price'],
                'kid_price'    => $validated['kid_price'] ?? 0,
                'length'       => $validated['length'],
                'tour_type_id' => $validated['tour_type_id'],
                'itinerary_id' => $validated['itinerary_id'],
                'is_active'    => true,
            ]);

            // relaciones
            $tour->languages()->sync($validated['languages']);
            $tour->amenities()->sync($validated['amenities'] ?? []);

            // horarios
            if ($validated['schedule_am_start'] && $validated['schedule_am_end']) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_am_start'],
                    'end_time'   => $validated['schedule_am_end'],
                ]);
            }
            if ($validated['schedule_pm_start'] && $validated['schedule_pm_end']) {
                $tour->schedules()->create([
                    'start_time' => $validated['schedule_pm_start'],
                    'end_time'   => $validated['schedule_pm_end'],
                ]);
            }

            return redirect()
                ->route('admin.tours.index')
                ->with('success', 'Tour creado correctamente.');

        } catch (Exception $e) {
            Log::error('Error al crear tour: '.$e->getMessage());
            return back()
                ->with('error', 'Hubo un problema al crear el tour.')
                ->withInput()
                ->with('showCreateModal', true);
        }
    }

    public function update(Request $request, Tour $tour)
    {
        // 1. Validación
        $validator = Validator::make($request->all(), [
            'name'                    => 'required|string|max:255',
            'overview'                => 'nullable|string',
            'description'             => 'nullable|string',
            'adult_price'             => 'required|numeric|min:0',
            'kid_price'               => 'nullable|numeric|min:0',
            'length'                  => 'required|numeric|min:1',
            'tour_type_id'            => 'required|exists:tour_types,tour_type_id',
            'languages'               => 'required|array|min:1',
            'languages.*'             => 'exists:tour_languages,tour_language_id',
            'amenities'               => 'nullable|array',
            'amenities.*'             => 'exists:amenities,amenity_id',
            'schedule_am_start'       => 'nullable|date_format:H:i',
            'schedule_am_end'         => 'nullable|date_format:H:i',
            'schedule_pm_start'       => 'nullable|date_format:H:i',
            'schedule_pm_end'         => 'nullable|date_format:H:i',
            'itinerary_id'            => 'nullable',
            'itinerary'               => 'nullable|array',
            'itinerary.*.title'       => 'required_with:itinerary|string|max:255',
            'itinerary.*.description' => 'required_with:itinerary|string',
            'new_itinerary_name'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }

        $validated = $validator->validated();

        // validación extra para Full Day
        $tourType = TourType::find($validated['tour_type_id']);
        if ($tourType && $tourType->name === 'Full Day') {
            if (!$validated['schedule_am_start'] || !$validated['schedule_am_end']) {
                return back()
                    ->withErrors(['schedule_am_start' => 'Un tour Full Day requiere horario AM completo.'])
                    ->withInput()
                    ->with('showEditModal', $tour->tour_id);
            }
            if ($validated['schedule_pm_start'] || $validated['schedule_pm_end']) {
                return back()
                    ->withErrors(['schedule_pm_start' => 'Un tour Full Day no puede tener horario PM.'])
                    ->withInput()
                    ->with('showEditModal', $tour->tour_id);
            }
        }

        try {
            DB::transaction(function() use ($request, $validated, $tour) {
                // 2. Actualizar datos básicos
                $tour->update([
                    'name'           => $validated['name'],
                    'overview'       => $validated['overview'] ?? '',
                    'description'    => $validated['description'] ?? '',
                    'adult_price'    => $validated['adult_price'],
                    'kid_price'      => $validated['kid_price'] ?? 0,
                    'length'         => $validated['length'],
                    'tour_type_id'   => $validated['tour_type_id'],
                ]);

                // 3. Gestionar Itinerario
                $itineraryId = $request->input('itinerary_id');
                if ($itineraryId === 'new') {
                    // Creo uno nuevo
                    $itinerary = Itinerary::create([
                        'name' => $validated['new_itinerary_name']
                                ?? ($tour->name . ' - Itinerario Actualizado'),
                    ]);
                } elseif ($itineraryId) {
                    // Cargo el existente y borro sus ítems
                    $itinerary = Itinerary::findOrFail($itineraryId);
                    $itinerary->items()->delete();
                } else {
                    // Ningún itinerario seleccionado
                    $itinerary = null;
                }

                // 4. (Re)crear ítems si corresponde
                if ($itinerary && ! empty($validated['itinerary'])) {
                    foreach ($validated['itinerary'] as $idx => $item) {
                        $itinerary->items()->create([
                            'title'       => $item['title'],
                            'description' => $item['description'],
                            'order'       => $idx,
                            'is_active'   => true,
                        ]);
                    }
                }

                // 5. Asociar el tour al itinerario
                if ($itinerary) {
                    $tour->itinerary()->associate($itinerary);
                    $tour->save();
                }

                // 6. Sincronizar relaciones
                $tour->languages()->sync($validated['languages']);
                $tour->amenities()->sync($validated['amenities'] ?? []);

                // 7. Reemplazar horarios
                $tour->schedules()->delete();
                if (!empty($validated['schedule_am_start'])) {
                    $tour->schedules()->create([
                        'start_time' => $validated['schedule_am_start'],
                        'end_time'   => $validated['schedule_am_end'],
                    ]);
                }
                if (!empty($validated['schedule_pm_start'])) {
                    $tour->schedules()->create([
                        'start_time' => $validated['schedule_pm_start'],
                        'end_time'   => $validated['schedule_pm_end'],
                    ]);
                }
            });

            return redirect()
                ->route('admin.tours.index')
                ->with('success', 'Tour actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar tour: ' . $e->getMessage());
            return back()
                ->with('error', 'Hubo un problema al actualizar el tour.')
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }
    }


    public function destroy(Tour $tour)
    {
        try {
            $tour->is_active = ! $tour->is_active;
            $tour->save();

            $msg = $tour->is_active
                ? 'Tour activado correctamente.'
                : 'Tour desactivado correctamente.';

            return redirect()
                ->route('admin.tours.index')
                ->with('success', $msg);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del tour: '.$e->getMessage());
            return back()->with('error','Hubo un problema al cambiar el estado del tour.');
        }
    }
}
