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
use App\Services\ItineraryService; // (ya no se usa para crear itinerario, pero puedes quitarlo si no lo necesitas en otros lados)
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

    // 👇 NUEVO: todos los horarios (solo activos) para el select "Usar horarios existentes"
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
        'allSchedules' // 👈 pásalo a la vista
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
        // Normaliza horarios NUEVOS (start/end a HH:MM) y opcional max_capacity en c/u
        $schedulesNew = collect($request->input('schedules_new', []))
            ->map(function ($s) {
                $start = $this->parseTime($s['start_time'] ?? null);
                $end   = $this->parseTime($s['end_time'] ?? null);
                $label = trim($s['label'] ?? '') ?: null;
                $cap   = isset($s['max_capacity']) && $s['max_capacity'] !== ''
                    ? (int) $s['max_capacity']
                    : null;
                return ['start' => $start, 'end' => $end, 'label' => $label, 'cap' => $cap];
            })
            ->filter(fn($s) => $s['start'] && $s['end'])
            ->values();

        $request->merge([
            'schedules_existing' => $request->input('schedules_existing', []),
            'schedules_new_norm' => $schedulesNew->toArray(),
        ]);

        // Reglas
        $rules = [
            'name'            => 'required|string|max:255',
            'overview'        => 'nullable|string',
            'adult_price'     => 'required|numeric|min:0',
            'kid_price'       => 'nullable|numeric|min:0',
            'max_capacity'    => 'required|integer|min:1', // default de horarios nuevos
            'length'          => 'required|numeric|min:1',
            'tour_type_id'    => 'required|exists:tour_types,tour_type_id',
            'languages'       => 'array|min:1',
            'languages.*'     => 'exists:tour_languages,tour_language_id',
            'amenities'       => 'nullable|array',
            'amenities.*'     => 'exists:amenities,amenity_id',
            'excluded_amenities'   => 'nullable|array',
            'excluded_amenities.*' => 'exists:amenities,amenity_id',

            // Itinerario SOLO asignación
            'itinerary_id'    => 'required|exists:itineraries,itinerary_id',

            // Horarios existentes (opcional)
            'schedules_existing'   => 'array',
            'schedules_existing.*' => 'exists:schedules,schedule_id',

            // Horarios nuevos normalizados (opcional)
            'schedules_new_norm'               => 'array',
            'schedules_new_norm.*.start'       => 'required|date_format:H:i',
            'schedules_new_norm.*.end'         => 'required|date_format:H:i',
            'schedules_new_norm.*.label'       => 'nullable|string|max:255',
            'schedules_new_norm.*.cap'         => 'nullable|integer|min:1',

            'viator_code'      => 'nullable|string|max:255',
            'color'            => 'nullable|string|max:16',
        ];

        // Mensajes de validación
        $messages = [
            'name.required'           => 'El nombre del tour es obligatorio.',
            'name.max'                => 'El nombre no puede exceder 255 caracteres.',
            'adult_price.required'    => 'Debes indicar el precio de adulto.',
            'adult_price.numeric'     => 'El precio de adulto debe ser numérico.',
            'kid_price.numeric'       => 'El precio de niño debe ser numérico.',
            'max_capacity.required'   => 'Debes indicar el cupo por defecto.',
            'max_capacity.integer'    => 'El cupo por defecto debe ser un número entero.',
            'max_capacity.min'        => 'El cupo por defecto debe ser al menos 1.',
            'length.required'         => 'Debes indicar la duración del tour.',
            'length.numeric'          => 'La duración debe ser numérica.',
            'tour_type_id.required'   => 'Debes seleccionar un tipo de tour.',
            'tour_type_id.exists'     => 'El tipo de tour seleccionado no es válido.',
            'languages.min'           => 'Debes seleccionar al menos un idioma.',
            'languages.*.exists'      => 'Alguno de los idiomas seleccionados no es válido.',
            'amenities.*.exists'      => 'Alguna amenidad seleccionada no es válida.',
            'excluded_amenities.*.exists' => 'Alguna amenidad no incluida no es válida.',
            'itinerary_id.required'   => 'Debes elegir un itinerario existente.',
            'itinerary_id.exists'     => 'El itinerario seleccionado no es válido.',

            'schedules_existing.array'    => 'El listado de horarios existentes no es válido.',
            'schedules_existing.*.exists' => 'Algún horario existente seleccionado no es válido.',

            'schedules_new_norm.array'             => 'El bloque de nuevos horarios no es válido.',
            'schedules_new_norm.*.start.required'  => 'Cada nuevo horario debe tener hora de inicio.',
            'schedules_new_norm.*.start.date_format' => 'El inicio debe tener formato HH:MM (ej. 08:00).',
            'schedules_new_norm.*.end.required'    => 'Cada nuevo horario debe tener hora de fin.',
            'schedules_new_norm.*.end.date_format' => 'El fin debe tener formato HH:MM (ej. 12:00).',
            'schedules_new_norm.*.label.max'       => 'La etiqueta del horario no puede exceder 255 caracteres.',
            'schedules_new_norm.*.cap.integer'     => 'El cupo del horario debe ser un número entero.',
            'schedules_new_norm.*.cap.min'         => 'El cupo del horario debe ser al menos 1.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showCreateModal', true);
        }

        $v = $validator->validated();

        // Debe haber al menos 1 horario (existente o nuevo)
        $hasExisting = !empty($v['schedules_existing']);
        $hasNew      = !empty($v['schedules_new_norm']);
        if (!$hasExisting && !$hasNew) {
            return back()
                ->withErrors(['schedules' => 'Debes seleccionar o crear al menos un horario.'])
                ->withInput()
                ->with('showCreateModal', true);
        }

        try {
            DB::transaction(function () use ($v, $request, $translator) {
                // Crear Tour con max_capacity como DEFAULT (para nuevos horarios)
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

                // Reunir schedule_ids (existentes + nuevos a reutilizar/crear)
                $scheduleIds = [];

                // Existentes
                foreach ($v['schedules_existing'] ?? [] as $sid) {
                    $scheduleIds[] = (int) $sid;
                }

                // Nuevos: intentar reutilizar por (start,end,label,capacidad)
                foreach ($v['schedules_new_norm'] ?? [] as $s) {
                    $cap = $s['cap'] ?? (int) $v['max_capacity']; // fallback
                    $found = Schedule::where('start_time', $s['start'].':00')
                        ->where('end_time', $s['end'].':00')
                        ->when($s['label'], fn($q) => $q->where('label', $s['label']))
                        ->when(!$s['label'], fn($q) => $q->whereNull('label'))
                        ->where('max_capacity', $cap)
                        ->first();

                    if (!$found) {
                        $found = Schedule::create([
                            'start_time'   => $s['start'], // mutator Schedule lo deja HH:MM:SS
                            'end_time'     => $s['end'],
                            'label'        => $s['label'],
                            'max_capacity' => $cap,
                            'is_active'    => true,
                        ]);
                    }

                    $scheduleIds[] = $found->schedule_id;
                }

                // Pivot con is_active=true (únicos)
                $pivot = collect($scheduleIds)->unique()->values()->mapWithKeys(
                    fn($id) => [(int)$id => ['is_active' => true]]
                )->all();
                $tour->schedules()->sync($pivot);

                // Traducciones automáticas base
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
            Log::error('Error al crear tour: ' . $e->getMessage());
            return back()
                ->with('error', 'Hubo un problema al crear el tour.')
                ->withInput()
                ->with('showCreateModal', true);
        }
    }

    public function update(Request $request, Tour $tour)
    {
        // Normaliza los NUEVOS horarios
        $schedulesNew = collect($request->input('schedules_new', []))
            ->map(function ($s) {
                $start = $this->parseTime($s['start_time'] ?? null);
                $end   = $this->parseTime($s['end_time'] ?? null);
                $label = trim($s['label'] ?? '') ?: null;
                $cap   = isset($s['max_capacity']) && $s['max_capacity'] !== ''
                    ? (int) $s['max_capacity']
                    : null;
                return ['start' => $start, 'end' => $end, 'label' => $label, 'cap' => $cap];
            })
            ->filter(fn($s) => $s['start'] && $s['end'])
            ->values();

        $request->merge([
            'schedules_existing' => $request->input('schedules_existing', []),
            'schedules_new_norm' => $schedulesNew->toArray(),
        ]);

        // Reglas
        $rules = [
            'name'            => 'required|string|max:255',
            'overview'        => 'nullable|string',
            'adult_price'     => 'required|numeric|min:0',
            'kid_price'       => 'nullable|numeric|min:0',
            'max_capacity'    => 'required|integer|min:1', // default para nuevos horarios
            'length'          => 'required|numeric|min:1',
            'tour_type_id'    => 'required|exists:tour_types,tour_type_id',
            'languages'       => 'array|min:1',
            'languages.*'     => 'exists:tour_languages,tour_language_id',
            'amenities'       => 'nullable|array',
            'amenities.*'     => 'exists:amenities,amenity_id',
            'excluded_amenities'   => 'nullable|array',
            'excluded_amenities.*' => 'exists:amenities,amenity_id',

            // Itinerario SOLO asignación
            'itinerary_id'    => 'required|exists:itineraries,itinerary_id',

            // Horarios existentes (opcional)
            'schedules_existing'   => 'array',
            'schedules_existing.*' => 'exists:schedules,schedule_id',

            // Horarios nuevos normalizados (opcional)
            'schedules_new_norm'               => 'array',
            'schedules_new_norm.*.start'       => 'required|date_format:H:i',
            'schedules_new_norm.*.end'         => 'required|date_format:H:i',
            'schedules_new_norm.*.label'       => 'nullable|string|max:255',
            'schedules_new_norm.*.cap'         => 'nullable|integer|min:1',

            'viator_code'      => 'nullable|string|max:255',
            'color'            => 'nullable|string|max:16',
        ];

        // Mensajes
        $messages = [
            'name.required'           => 'El nombre del tour es obligatorio.',
            'name.max'                => 'El nombre no puede exceder 255 caracteres.',
            'adult_price.required'    => 'Debes indicar el precio de adulto.',
            'adult_price.numeric'     => 'El precio de adulto debe ser numérico.',
            'kid_price.numeric'       => 'El precio de niño debe ser numérico.',
            'max_capacity.required'   => 'Debes indicar el cupo por defecto.',
            'max_capacity.integer'    => 'El cupo por defecto debe ser un número entero.',
            'max_capacity.min'        => 'El cupo por defecto debe ser al menos 1.',
            'length.required'         => 'Debes indicar la duración del tour.',
            'length.numeric'          => 'La duración debe ser numérica.',
            'tour_type_id.required'   => 'Debes seleccionar un tipo de tour.',
            'tour_type_id.exists'     => 'El tipo de tour seleccionado no es válido.',
            'languages.min'           => 'Debes seleccionar al menos un idioma.',
            'languages.*.exists'      => 'Alguno de los idiomas seleccionados no es válido.',
            'amenities.*.exists'      => 'Alguna amenidad seleccionada no es válida.',
            'excluded_amenities.*.exists' => 'Alguna amenidad no incluida no es válida.',
            'itinerary_id.required'   => 'Debes elegir un itinerario existente.',
            'itinerary_id.exists'     => 'El itinerario seleccionado no es válido.',

            'schedules_existing.array'    => 'El listado de horarios existentes no es válido.',
            'schedules_existing.*.exists' => 'Algún horario existente seleccionado no es válido.',

            'schedules_new_norm.array'             => 'El bloque de nuevos horarios no es válido.',
            'schedules_new_norm.*.start.required'  => 'Cada nuevo horario debe tener hora de inicio.',
            'schedules_new_norm.*.start.date_format' => 'El inicio debe tener formato HH:MM (ej. 08:00).',
            'schedules_new_norm.*.end.required'    => 'Cada nuevo horario debe tener hora de fin.',
            'schedules_new_norm.*.end.date_format' => 'El fin debe tener formato HH:MM (ej. 12:00).',
            'schedules_new_norm.*.label.max'       => 'La etiqueta del horario no puede exceder 255 caracteres.',
            'schedules_new_norm.*.cap.integer'     => 'El cupo del horario debe ser un número entero.',
            'schedules_new_norm.*.cap.min'         => 'El cupo del horario debe ser al menos 1.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }

        $v = $validator->validated();

        // Debe haber al menos 1 horario (existente o nuevo)
        $hasExisting = !empty($v['schedules_existing']);
        $hasNew      = !empty($v['schedules_new_norm']);
        if (!$hasExisting && !$hasNew) {
            return back()
                ->withErrors(['schedules' => 'Debes seleccionar o crear al menos un horario.'])
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }

        try {
            DB::transaction(function () use ($tour, $v, $request) {
                // Actualizar tour (itinerario solo asignación)
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

                // Relaciones
                $tour->languages()->sync($v['languages'] ?? []);
                $tour->amenities()->sync($v['amenities'] ?? []);
                $tour->excludedAmenities()->sync($v['excluded_amenities'] ?? []);

                // Reunir schedule_ids (existentes + nuevos reusados/creados)
                $scheduleIds = [];

                // Existentes
                foreach ($v['schedules_existing'] ?? [] as $sid) {
                    $scheduleIds[] = (int) $sid;
                }

                // Nuevos: reutilizar/crear
                foreach ($v['schedules_new_norm'] ?? [] as $s) {
                    $cap = $s['cap'] ?? (int) $v['max_capacity']; // fallback al default del tour
                    $found = Schedule::where('start_time', $s['start'].':00')
                        ->where('end_time', $s['end'].':00')
                        ->when($s['label'], fn($q) => $q->where('label', $s['label']))
                        ->when(!$s['label'], fn($q) => $q->whereNull('label'))
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

                // Sincronizar pivote (is_active=true)
                $pivot = collect($scheduleIds)->unique()->values()->mapWithKeys(
                    fn($id) => [(int)$id => ['is_active' => true]]
                )->all();
                $tour->schedules()->sync($pivot);

                // Mantén la traducción principal ES al actualizar nombre/overview (opcional)
                TourTranslation::updateOrCreate(
                    ['tour_id' => $tour->tour_id, 'locale' => 'es'],
                    ['name' => $v['name'], 'overview' => $v['overview'] ?? '']
                );
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
            $tour->is_active = !$tour->is_active;
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
