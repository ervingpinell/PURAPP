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
use App\Services\Contracts\TranslatorInterface;
use App\Models\TourTranslation;

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
            // Importante: calificar la columna y filtrar también por el pivote
            'schedules' => function ($q) {
                $q->where('schedules.is_active', true)
                  ->wherePivot('is_active', true)
                  ->orderBy('schedules.start_time');
            },
        ])->orderBy('tour_id')->get();

        $tourtypes      = TourType::where('is_active', true)->orderBy('name')->get();
        $itineraries    = Itinerary::where('is_active', true)->orderBy('name')->get();
        $languages      = TourLanguage::where('is_active', true)->orderBy('name')->get();
        $amenities      = Amenity::where('is_active', true)->orderBy('name')->get();
        $availableItems = collect((new ItineraryService)->getAvailableItems())
            ->where('is_active', true)
            ->values();
        $hotels         = HotelList::where('is_active', true)->orderBy('name')->get();

        // Para el select de “usar horarios existentes”
        $allSchedules = Schedule::where('is_active', true)
            ->orderBy('start_time')
            ->orderBy('label')
            ->get();

        return view('admin.tours.index', compact(
            'tours',
            'tourtypes',
            'itineraries',
            'languages',
            'amenities',
            'availableItems',
            'hotels',
            'allSchedules'
        ));
    }

    public function edit(Tour $tour)
    {
        $tourtypes      = TourType::where('is_active', true)->orderBy('name')->get();
        $itineraries    = Itinerary::where('is_active', true)
            ->with(['items' => fn ($q) => $q->wherePivot('is_active', true)])
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
        if (!$input) return null;
        $input   = trim(strtolower($input));
        $formats = ['H:i', 'g:i a', 'g:iA', 'g:ia', 'g:i A', 'g:i'];
        foreach ($formats as $fmt) {
            if ($dt = \DateTime::createFromFormat($fmt, $input)) {
                return $dt->format('H:i');
            }
        }
        return null;
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        // Normaliza NUEVOS horarios
        $schedulesNew = collect($request->input('schedules_new', []))
            ->map(function ($s) {
                $start = $this->parseTime($s['start_time'] ?? null);
                $end   = $this->parseTime($s['end_time'] ?? null);
                $label = trim($s['label'] ?? '') ?: null;
                $cap   = isset($s['max_capacity']) && $s['max_capacity'] !== ''
                        ? (int) $s['max_capacity'] : null;
                return ['start' => $start, 'end' => $end, 'label' => $label, 'cap' => $cap];
            })
            ->filter(fn ($s) => $s['start'] && $s['end'])
            ->values();

        $request->merge([
            'schedules_existing' => $request->input('schedules_existing', []),
            'schedules_new_norm' => $schedulesNew->toArray(),
        ]);

        $rules = [
            'name'                         => 'required|string|max:255',
            'overview'                     => 'nullable|string',
            'adult_price'                  => 'required|numeric|min:0',
            'kid_price'                    => 'nullable|numeric|min:0',
            'max_capacity'                 => 'required|integer|min:1',
            'length'                       => 'required|numeric|min:1',
            'tour_type_id'                 => 'required|exists:tour_types,tour_type_id',
            'languages'                    => 'array|min:1',
            'languages.*'                  => 'exists:tour_languages,tour_language_id',
            'amenities'                    => 'nullable|array',
            'amenities.*'                  => 'exists:amenities,amenity_id',
            'excluded_amenities'           => 'nullable|array',
            'excluded_amenities.*'         => 'exists:amenities,amenity_id',

            // Asignación de itinerario existente
            'itinerary_id'                 => 'required|exists:itineraries,itinerary_id',

            // Horarios existentes
            'schedules_existing'           => 'array',
            'schedules_existing.*'         => 'exists:schedules,schedule_id',

            // Horarios nuevos (normalizados)
            'schedules_new_norm'               => 'array',
            'schedules_new_norm.*.start'       => 'required|date_format:H:i',
            'schedules_new_norm.*.end'         => 'required|date_format:H:i',
            'schedules_new_norm.*.label'       => 'nullable|string|max:255',
            'schedules_new_norm.*.cap'         => 'nullable|integer|min:1',

            'viator_code'                  => 'nullable|string|max:255',
            'color'                        => 'nullable|string|max:16',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'languages.min' => 'Debes seleccionar al menos un idioma.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('showCreateModal', true);
        }

        $v = $validator->validated();

        if (empty($v['schedules_existing']) && empty($v['schedules_new_norm'])) {
            return back()
                ->withErrors(['schedules' => 'Debes seleccionar o crear al menos un horario.'])
                ->withInput()
                ->with('showCreateModal', true);
        }

        try {
            DB::transaction(function () use ($v, $request, $translator) {
                // Crear Tour
                $tour = Tour::create([
                    'name'         => trim($v['name']),
                    'overview'     => $v['overview'] ?? '',
                    'adult_price'  => $v['adult_price'],
                    'kid_price'    => $v['kid_price'] ?? 0,
                    'max_capacity' => (int) $v['max_capacity'],
                    'length'       => $v['length'],
                    'tour_type_id' => $v['tour_type_id'],
                    'itinerary_id' => $v['itinerary_id'],
                    'is_active'    => true,
                    'color'        => $request->input('color', '#5cb85c'),
                    'viator_code'  => $request->input('viator_code'),
                ]);

                // Relaciones
                $tour->languages()->sync($v['languages'] ?? []);
                $tour->amenities()->sync($v['amenities'] ?? []);
                $tour->excludedAmenities()->sync($v['excluded_amenities'] ?? []);

                // Reunir schedule_ids (existentes + nuevos)
                $scheduleIds = [];

                foreach ($v['schedules_existing'] ?? [] as $sid) {
                    $scheduleIds[] = (int) $sid;
                }

                foreach ($v['schedules_new_norm'] ?? [] as $s) {
                    $cap = $s['cap'] ?? (int) $v['max_capacity'];
                    $found = Schedule::where('start_time', $s['start'].':00')
                        ->where('end_time', $s['end'].':00')
                        ->when($s['label'], fn ($q) => $q->where('label', $s['label']))
                        ->when(!$s['label'], fn ($q) => $q->whereNull('label'))
                        ->where('max_capacity', $cap)
                        ->first();

                    if (!$found) {
                        $found = Schedule::create([
                            'start_time'   => $s['start'], // el cast/DB lo lleva a HH:MM:SS
                            'end_time'     => $s['end'],
                            'label'        => $s['label'],
                            'max_capacity' => $cap,
                            'is_active'    => true,
                        ]);
                    }

                    $scheduleIds[] = $found->schedule_id;
                }

                // Sync con pivote is_active=true
                $pivot = collect($scheduleIds)->unique()->values()->mapWithKeys(
                    fn ($id) => [(int) $id => ['is_active' => true]]
                )->all();
                $tour->schedules()->sync($pivot);

                // Traducciones
                $nameTr     = $translator->translateAll($v['name'] ?? '');
                $overviewTr = $translator->translateAll($v['overview'] ?? '');
                foreach (['es','en','fr','pt','de'] as $lang) {
                    TourTranslation::create([
                        'tour_id'  => $tour->tour_id,
                        'locale'   => $lang,
                        'name'     => $nameTr[$lang] ?? ($v['name'] ?? ''),
                        'overview' => $overviewTr[$lang] ?? ($v['overview'] ?? ''),
                    ]);
                }
            });

            return redirect()->route('admin.tours.index')
                ->with('success', 'Tour creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear tour: '.$e->getMessage());
            return back()->with('error', 'Hubo un problema al crear el tour.')
                         ->withInput()->with('showCreateModal', true);
        }
    }

    public function update(Request $request, Tour $tour)
    {
        // Normaliza NUEVOS horarios
        $schedulesNew = collect($request->input('schedules_new', []))
            ->map(function ($s) {
                $start = $this->parseTime($s['start_time'] ?? null);
                $end   = $this->parseTime($s['end_time'] ?? null);
                $label = trim($s['label'] ?? '') ?: null;
                $cap   = isset($s['max_capacity']) && $s['max_capacity'] !== ''
                        ? (int) $s['max_capacity'] : null;
                return ['start' => $start, 'end' => $end, 'label' => $label, 'cap' => $cap];
            })
            ->filter(fn ($s) => $s['start'] && $s['end'])
            ->values();

        $request->merge([
            'schedules_existing' => $request->input('schedules_existing', []),
            'schedules_new_norm' => $schedulesNew->toArray(),
        ]);

        $rules = [
            'name'                         => 'required|string|max:255',
            'overview'                     => 'nullable|string',
            'adult_price'                  => 'required|numeric|min:0',
            'kid_price'                    => 'nullable|numeric|min:0',
            'max_capacity'                 => 'required|integer|min:1',
            'length'                       => 'required|numeric|min:1',
            'tour_type_id'                 => 'required|exists:tour_types,tour_type_id',
            'languages'                    => 'array|min:1',
            'languages.*'                  => 'exists:tour_languages,tour_language_id',
            'amenities'                    => 'nullable|array',
            'amenities.*'                  => 'exists:amenities,amenity_id',
            'excluded_amenities'           => 'nullable|array',
            'excluded_amenities.*'         => 'exists:amenities,amenity_id',

            'itinerary_id'                 => 'required|exists:itineraries,itinerary_id',

            'schedules_existing'           => 'array',
            'schedules_existing.*'         => 'exists:schedules,schedule_id',

            'schedules_new_norm'               => 'array',
            'schedules_new_norm.*.start'       => 'required|date_format:H:i',
            'schedules_new_norm.*.end'         => 'required|date_format:H:i',
            'schedules_new_norm.*.label'       => 'nullable|string|max:255',
            'schedules_new_norm.*.cap'         => 'nullable|integer|min:1',

            'viator_code'                  => 'nullable|string|max:255',
            'color'                        => 'nullable|string|max:16',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('showEditModal', $tour->tour_id);
        }

        $v = $validator->validated();

        if (empty($v['schedules_existing']) && empty($v['schedules_new_norm'])) {
            return back()
                ->withErrors(['schedules' => 'Debes seleccionar o crear al menos un horario.'])
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }

        try {
            DB::transaction(function () use ($tour, $v, $request) {
                // Actualizar tour
                $tour->update([
                    'name'         => trim($v['name']),
                    'overview'     => $v['overview'] ?? '',
                    'adult_price'  => $v['adult_price'],
                    'kid_price'    => $v['kid_price'] ?? 0,
                    'max_capacity' => (int) $v['max_capacity'],
                    'length'       => $v['length'],
                    'tour_type_id' => $v['tour_type_id'],
                    'itinerary_id' => $v['itinerary_id'],
                    'color'        => $request->input('color', '#5cb85c'),
                    'viator_code'  => $request->input('viator_code'),
                ]);

                $tour->languages()->sync($v['languages'] ?? []);
                $tour->amenities()->sync($v['amenities'] ?? []);
                $tour->excludedAmenities()->sync($v['excluded_amenities'] ?? []);

                $scheduleIds = [];

                foreach ($v['schedules_existing'] ?? [] as $sid) {
                    $scheduleIds[] = (int) $sid;
                }

                foreach ($v['schedules_new_norm'] ?? [] as $s) {
                    $cap = $s['cap'] ?? (int) $v['max_capacity'];
                    $found = Schedule::where('start_time', $s['start'].':00')
                        ->where('end_time', $s['end'].':00')
                        ->when($s['label'], fn ($q) => $q->where('label', $s['label']))
                        ->when(!$s['label'], fn ($q) => $q->whereNull('label'))
                        ->where('max_capacity', $cap)
                        ->first();

                    if (!$found) {
                        $found = Schedule::create([
                            'start_time'   => $s['start'],
                            'end_time'     => $s['end'],
                            'label'        => $s['label'],
                            'max_capacity' => $cap,
                            'is_active'    => true,
                        ]);
                    }

                    $scheduleIds[] = $found->schedule_id;
                }

                $pivot = collect($scheduleIds)->unique()->values()->mapWithKeys(
                    fn ($id) => [(int) $id => ['is_active' => true]]
                )->all();
                $tour->schedules()->sync($pivot);

                // Mantener ES actualizada
                TourTranslation::updateOrCreate(
                    ['tour_id' => $tour->tour_id, 'locale' => 'es'],
                    ['name' => $v['name'], 'overview' => $v['overview'] ?? '']
                );
            });

            return redirect()->route('admin.tours.index')
                ->with('success', 'Tour actualizado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar tour: '.$e->getMessage());
            return back()->with('error', 'Hubo un problema al actualizar el tour.')
                         ->withInput()->with('showEditModal', $tour->tour_id);
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
            Log::error('Error al cambiar estado del tour: '.$e->getMessage());
            return back()->with('error', 'Hubo un problema al cambiar el estado del tour.');
        }
    }
}
