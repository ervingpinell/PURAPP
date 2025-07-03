<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Tour;
use App\Models\TourType;
use App\Models\Itinerary;
use App\Models\TourLanguage;
use App\Models\Amenity;
use App\Services\ItineraryService;
use App\Models\HotelList;
use App\Models\Schedule;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with([
            'tourType',
            'languages' => function ($q) {
                $q->wherePivot('is_active', true)
                    ->where('tour_languages.is_active', true);
            },
            'amenities' => function ($q) {
                $q->wherePivot('is_active', true)
                    ->where('amenities.is_active', true);
            },
            'itinerary.items' => function ($q) {
                $q->wherePivot('is_active', true)
                    ->where('itinerary_items.is_active', true);
            },
            'schedules'  => fn($q) => $q->where('is_active', true),
        ])->orderBy('tour_id')->get();

        $tourtypes      = TourType::where('is_active', true)->orderBy('name')->get();
        $itineraries    = Itinerary::where('is_active', true)->orderBy('name')->get();
        $languages      = TourLanguage::where('is_active', true)->orderBy('name')->get();
        $amenities      = Amenity::where('is_active', true)->orderBy('name')->get();
        $availableItems = collect((new ItineraryService)->getAvailableItems())
            ->where('is_active', true)
            ->values();

        // <-- Añade esta línea:
        $hotels = HotelList::where('is_active', true)->orderBy('name')->get();

        // Y pásala al compact:
        return view('admin.tours.index', compact(
            'tours',
            'tourtypes',
            'itineraries',
            'languages',
            'amenities',
            'availableItems',
            'hotels'        // <-- aquí
        ));
    }


    public function edit(Tour $tour)
    {
        $tourtypes      = TourType::where('is_active', true)->orderBy('name')->get();
        $itineraries    = Itinerary::where('is_active', true)
            ->with(['items' => fn($q) => $q->wherePivot('is_active', true)])
            ->orderBy('name')
            ->get();
        $languages      = TourLanguage::where('is_active', true)->orderBy('name')->get();
        $amenities      = Amenity::where('is_active', true)->orderBy('name')->get();
        $availableItems = collect((new ItineraryService)->getAvailableItems())
            ->where('is_active', true)
            ->values();

        return view('admin.tours.edit', compact(
            'tour',
            'tourtypes',
            'itineraries',
            'languages',
            'amenities',
            'availableItems'
        ));
    }

    private function parseTime(?string $input): ?string
    {
        if (! $input) return null;
        $input   = trim(strtolower($input));
        $formats = ['H:i', 'g:i a', 'g:iA', 'g:ia', 'g:i A', 'g:i'];
        foreach ($formats as $fmt) {
            if ($dt = \DateTime::createFromFormat($fmt, $input)) {
                return $dt->format('H:i');
            }
        }
        return null;
    }

    public function store(Request $request)
    {
        // Normalizar horarios
        $request->merge([
            'schedule_am_start' => $this->parseTime($request->input('schedule_am_start')),
            'schedule_am_end'   => $this->parseTime($request->input('schedule_am_end')),
            'schedule_pm_start' => $this->parseTime($request->input('schedule_pm_start')),
            'schedule_pm_end'   => $this->parseTime($request->input('schedule_pm_end')),
        ]);

        // Combinar IDs de ítems existentes + nuevos
        $request->merge([
            'itinerary_combined' => array_merge(
                $request->input('existing_item_ids', []),
                array_values(array_filter(
                    $request->input('itinerary', []),
                    fn($i) => is_array($i) && ! empty($i['title'])
                ))
            ),
        ]);

        // Reglas de validación
        $rules = [
            'name'                     => 'required|string|max:255',
            'overview'                 => 'nullable|string',
            'adult_price'              => 'required|numeric|min:0',
            'kid_price'                => 'nullable|numeric|min:0',
            'max_capacity'             => 'required|integer|min:1',
            'length'                   => 'required|numeric|min:1',
            'tour_type_id'             => 'exists:tour_types,tour_type_id',
            'languages'                => 'array|min:1',
            'languages.*'              => 'exists:tour_languages,tour_language_id',
            'amenities'                => 'nullable|array',
            'amenities.*'              => 'exists:amenities,amenity_id',
            'excluded_amenities'       => 'nullable|array',
            'excluded_amenities.*'     => 'exists:amenities,amenity_id',
            'schedule_am_start'        => 'nullable|date_format:H:i',
            'schedule_am_end'          => 'nullable|date_format:H:i',
            'schedule_pm_start'        => 'nullable|date_format:H:i',
            'schedule_pm_end'          => 'nullable|date_format:H:i',
            'itinerary_id'             => 'nullable',
            'new_itinerary_name'       => 'nullable|string|max:255',
            'new_itinerary_description' => 'nullable|string|max:1000',
        ];

        // Si se elige crear nuevo itinerario
        if ($request->input('itinerary_id') === 'new') {
            $rules['itinerary_combined']   = 'required|array|min:1';
            $rules['itinerary_combined.*'] = [
                Rule::exists('itinerary_items', 'item_id')
                    ->where('is_active', true),
            ];
            $rules['new_itinerary_name']        = 'required|string|max:255';
            $rules['new_itinerary_description'] = 'required|string|max:1000';
            $rules['itinerary.*.title']       = 'nullable|string|required_with:itinerary.*.description';
            $rules['itinerary.*.description'] = 'nullable|string|required_with:itinerary.*.title';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showCreateModal', true);
        }

        $v = $validator->validated();

        $errores = [];

        // Reglas para AM
        if (!empty($v['schedule_am_start']) && empty($v['schedule_am_end'])) {
            $errores['schedule_am_end'] = 'Debe completar el campo Horario AM (Fin) si se ingresó el inicio.';
        }
        if (empty($v['schedule_am_start']) && !empty($v['schedule_am_end'])) {
            $errores['schedule_am_start'] = 'Debe completar el campo Horario AM (Inicio) si se ingresó el fin.';
        }

        // Reglas para PM
        if (!empty($v['schedule_pm_start']) && empty($v['schedule_pm_end'])) {
            $errores['schedule_pm_end'] = 'Debe completar el campo Horario PM (Fin) si se ingresó el inicio.';
        }
        if (empty($v['schedule_pm_start']) && !empty($v['schedule_pm_end'])) {
            $errores['schedule_pm_start'] = 'Debe completar el campo Horario PM (Inicio) si se ingresó el fin.';
        }

        // Validación cruzada (AM completo + PM incompleto)
        $amCompleto = !empty($v['schedule_am_start']) && !empty($v['schedule_am_end']);
        $pmIncompleto = (!empty($v['schedule_pm_start']) && empty($v['schedule_pm_end']))
            || (empty($v['schedule_pm_start']) && !empty($v['schedule_pm_end']));

        if ($amCompleto && $pmIncompleto) {
            if (!empty($v['schedule_pm_start']) && empty($v['schedule_pm_end'])) {
                $errores['schedule_pm_end'] = 'Ingresó el inicio del horario PM pero no el fin.';
            }
            if (empty($v['schedule_pm_start']) && !empty($v['schedule_pm_end'])) {
                $errores['schedule_pm_start'] = 'Ingresó el fin del horario PM pero no el inicio.';
            }
        }
        if (!empty($errores)) {
            return back()
                ->withErrors($errores)
                ->withInput()
                ->with('showEditModal', true);
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showCreateModal', true);
        }

        $v = $validator->validated();
        try {
            DB::transaction(function () use ($v) {
                $itService = new ItineraryService;
                $itinerary = $itService->handleCreationOrAssignment($v);
                if (! $itinerary) {
                    throw new Exception('No se pudo generar o asignar el itinerario.');
                }

                // Construcción de horarios
                $newSchedules = collect();

                if (!empty($v['schedule_am_start']) && !empty($v['schedule_am_end'])) {
                    $newSchedules->push([
                        'start_time' => $v['schedule_am_start'],
                        'end_time'   => $v['schedule_am_end'],
                    ]);
                }

                if (!empty($v['schedule_pm_start']) && !empty($v['schedule_pm_end'])) {
                    $newSchedules->push([
                        'start_time' => $v['schedule_pm_start'],
                        'end_time'   => $v['schedule_pm_end'],
                    ]);
                }

                if ($newSchedules->isEmpty()) {
                    throw new \Exception('Debe ingresar al menos un horario válido (AM o PM).');
                }

                // Crear el tour
                $tour = Tour::create([
                    'name'         => $v['name'],
                    'overview'     => $v['overview'] ?? '',
                    'adult_price'  => $v['adult_price'],
                    'kid_price'    => $v['kid_price'] ?? 0,
                    'max_capacity'   => $v['max_capacity'],
                    'length'       => $v['length'],
                    'tour_type_id' => $v['tour_type_id'],
                    'itinerary_id' => $itinerary->itinerary_id,
                    'is_active'    => true,
                ]);

                // Relaciones
                $tour->languages()->sync($v['languages']);
                $tour->amenities()->sync($v['amenities'] ?? []);
                $tour->excludedAmenities()->sync($v['excluded_amenities'] ?? []);

                // Guardar horarios: crear y asociar
                $scheduleIds = [];
                foreach ($newSchedules as $sched) {
                    $schedule = Schedule::create([
                        'start_time' => $sched['start_time'],
                        'end_time'   => $sched['end_time'],
                        'label'      => null, // O lo que uses
                        'is_active'  => true,
                    ]);
                    $scheduleIds[] = $schedule->schedule_id;
                }

                $tour->schedules()->sync($scheduleIds);

            });

            return redirect()
                ->route('admin.tours.index')
                ->with('success', 'Tour creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear tour: ' . $e->getMessage());
            return back()
                ->with('error', 'Hubo un problema al crear el tour.')
                ->withInput()
                ->with('showCreateModal', true);
        }
    }

    public function update(Request $request, Tour $tour)
    {
        

        // Normalizar horarios
        $request->merge([
            'schedule_am_start' => $this->parseTime($request->input('schedule_am_start')),
            'schedule_am_end'   => $this->parseTime($request->input('schedule_am_end')),
            'schedule_pm_start' => $this->parseTime($request->input('schedule_pm_start')),
            'schedule_pm_end'   => $this->parseTime($request->input('schedule_pm_end')),
        ]);

        // Combinar ítems
        $request->merge([
            'itinerary_combined' => array_merge(
                $request->input('existing_item_ids', []),
                array_values(array_filter(
                    $request->input('itinerary', []),
                    fn($i) => is_array($i) && ! empty($i['title'])
                ))
            ),
        ]);

        $rules = [
            'name'                     => 'required|string|max:255',
            'overview'                 => 'nullable|string',
            'adult_price'              => 'required|numeric|min:0',
            'kid_price'                => 'nullable|numeric|min:0',
            'max_capacity'             => 'required|integer|min:1',
            'length'                   => 'required|numeric|min:1',
            'tour_type_id'             => 'exists:tour_types,tour_type_id',
            'languages'                => 'array|min:1',
            'languages.*'              => 'exists:tour_languages,tour_language_id',
            'amenities'                => 'nullable|array',
            'amenities.*'              => 'exists:amenities,amenity_id',
            'excluded_amenities'       => 'nullable|array',
            'excluded_amenities.*'     => 'exists:amenities,amenity_id',
            'schedule_am_start'        => 'nullable|date_format:H:i',
            'schedule_am_end'          => 'nullable|date_format:H:i',
            'schedule_pm_start'        => 'nullable|date_format:H:i',
            'schedule_pm_end'          => 'nullable|date_format:H:i',
            'itinerary_id'             => 'nullable',
            'new_itinerary_name'       => 'nullable|string|max:255',
            'new_itinerary_description'=> 'nullable|string|max:1000',
        ];

        if ($request->input('itinerary_id') === 'new') {
            $rules['itinerary_combined']   = 'required|array|min:1';
            $rules['itinerary_combined.*'] = [
                Rule::exists('itinerary_items', 'item_id')->where('is_active', true),
            ];
            $rules['new_itinerary_name']        = 'required|string|max:255';
            $rules['new_itinerary_description'] = 'required|string|max:1000';
            $rules['itinerary.*.title']         = 'nullable|string|required_with:itinerary.*.description';
            $rules['itinerary.*.description']   = 'nullable|string|required_with:itinerary.*.title';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }

        $v = $validator->validated();

        // Validaciones cruzadas
        $errores = [];

        if (!empty($v['schedule_am_start']) && empty($v['schedule_am_end'])) {
            $errores['schedule_am_end'] = 'Debe completar el campo Horario AM (Fin) si se ingresó el inicio.';
        }
        if (empty($v['schedule_am_start']) && !empty($v['schedule_am_end'])) {
            $errores['schedule_am_start'] = 'Debe completar el campo Horario AM (Inicio) si se ingresó el fin.';
        }
        if (!empty($v['schedule_pm_start']) && empty($v['schedule_pm_end'])) {
            $errores['schedule_pm_end'] = 'Debe completar el campo Horario PM (Fin) si se ingresó el inicio.';
        }
        if (empty($v['schedule_pm_start']) && !empty($v['schedule_pm_end'])) {
            $errores['schedule_pm_start'] = 'Debe completar el campo Horario PM (Inicio) si se ingresó el fin.';
        }

        if (!empty($errores)) {
            return back()
                ->withErrors($errores)
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }

        try {
            DB::transaction(function () use ($tour, $v) {
                // Actualiza Tour
                $tour->update([
                    'name'         => $v['name'],
                    'overview'     => $v['overview'] ?? '',
                    'adult_price'  => $v['adult_price'],
                    'kid_price'    => $v['kid_price'] ?? 0,
                    'max_capacity' => $v['max_capacity'],
                    'length'       => $v['length'],
                    'tour_type_id' => $v['tour_type_id'],
                ]);

                // Itinerario
                $itService = new ItineraryService;
                $itinerary = $itService->handleCreationOrAssignment($v);
                if ($itinerary) {
                    $tour->itinerary()->associate($itinerary);
                    $tour->save();
                }

                // Relaciones pivot
                $tour->languages()->sync($v['languages']);
                $tour->amenities()->sync($v['amenities'] ?? []);
                $tour->excludedAmenities()->sync($v['excluded_amenities'] ?? []);

                // Horarios: limpia pivot, crea nuevos y sync
                $scheduleIds = [];

                if (!empty($v['schedule_am_start']) && !empty($v['schedule_am_end'])) {
                    $schedule = Schedule::create([
                        'start_time' => $v['schedule_am_start'],
                        'end_time'   => $v['schedule_am_end'],
                        'is_active'  => true,
                    ]);
                    $scheduleIds[] = $schedule->schedule_id;
                }

                if (!empty($v['schedule_pm_start']) && !empty($v['schedule_pm_end'])) {
                    $schedule = Schedule::create([
                        'start_time' => $v['schedule_pm_start'],
                        'end_time'   => $v['schedule_pm_end'],
                        'is_active'  => true,
                    ]);
                    $scheduleIds[] = $schedule->schedule_id;
                }

                $tour->schedules()->sync($scheduleIds);
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
            Log::error('Error al cambiar estado del tour: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al cambiar el estado del tour.');
        }
    }
}
