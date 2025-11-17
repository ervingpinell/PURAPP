<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

use App\Models\Tour;
use App\Models\TourType;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\TourLanguage;
use App\Models\Amenity;
use App\Models\HotelList;
use App\Models\Schedule;
use App\Models\TourTranslation;
use App\Models\CustomerCategory;
use App\Models\TourPrice;
use App\Models\TourAuditLog;

use App\Models\Booking;
use App\Models\BookingDetail;

use App\Services\ItineraryService;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;

use App\Http\Requests\Tour\Tour\StoreTourRequest;
use App\Http\Requests\Tour\Tour\UpdateTourRequest;
use App\Http\Requests\Tour\Tour\ToggleTourRequest;

class TourController extends Controller
{
    protected string $controller = 'TourController';

    /** =========================================================
     *  INDEX: lista con filtros (active | inactive | archived | all)
     *  ========================================================= */
public function index()
{
    $status = request('status', 'active');

    $base = Tour::query()
        ->with([
            'tourType',
            'translations',
            // Eager load de precios y traducciones de categorías para evitar N+1
            'prices' => function ($q) {
                $q->where('is_active', true)
                  ->with(['category' => function ($cq) {
                      $cq->where('is_active', true)
                         ->with('translations'); // <-- importante para getTranslatedName/fallbacks
                  }]);
            },
            'languages' => function ($q) {
                $q->wherePivot('is_active', true)
                  ->where('tour_languages.is_active', true);
            },
            'amenities' => function ($q) {
                $q->wherePivot('is_active', true)
                  ->where('amenities.is_active', true);
            },
            'itinerary.items' => function ($q) {
                $q->where('itinerary_items.is_active', true);
            },
            'schedules' => function ($q) {
                $q->where('schedules.is_active', true)
                  ->wherePivot('is_active', true)
                  ->orderBy('schedules.start_time');
            },
        ])
        ->withCount('bookings');

    if ($status === 'archived') {
        $base->onlyTrashed();
    } elseif ($status === 'all') {
        $base->withTrashed();
    } elseif ($status === 'inactive') {
        $base->where('is_active', false);
    } else {
        $base->where('is_active', true);
    }

    $tours = $base->orderBy('tour_id')->paginate(25)->withQueryString();

    // Para la tabla de listado y modales
    $tourTypes   = TourType::where('is_active', true)->orderBy('name')->get();
    $itineraries = Itinerary::where('is_active', true)->with('items')->orderBy('name')->get();
    $languages   = TourLanguage::where('is_active', true)->orderBy('name')->get();
    $amenities   = Amenity::where('is_active', true)->orderBy('name')->get();
    $schedules   = Schedule::where('is_active', true)->orderBy('start_time')->get();
    $hotels      = HotelList::where('is_active', true)->orderBy('name')->get();

    // JSON para itinerarios (vista rápida en modales)
    $itineraryJson = $itineraries->keyBy('itinerary_id')->map(function ($it) {
        return [
            'description' => $it->description,
            'items' => $it->items->map(function ($item) {
                return [
                    'title'       => $item->title,
                    'description' => $item->description,
                ];
            })->toArray()
        ];
    });

    return view('admin.tours.index', compact(
        'tours',
        'tourTypes',
        'itineraries',
        'itineraryJson',
        'languages',
        'amenities',
        'schedules',
        'hotels',
        'status'
    ));
}


    /** =========================================================
     *  CREATE
     *  ========================================================= */
    public function create()
    {
        $tourTypes   = TourType::where('is_active', true)->orderBy('name')->get();
        $itineraries = Itinerary::where('is_active', true)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();
        $schedules = Schedule::where('is_active', true)->orderBy('start_time')->get();
        $languages = TourLanguage::where('is_active', true)->orderBy('name')->get();
        $amenities = Amenity::where('is_active', true)->orderBy('name')->get();

        // JSON para el manejo dinámico de itinerarios
        $itineraryJson = $itineraries->keyBy('itinerary_id')->map(function ($it) {
            return [
                'description' => $it->description ?? '',
                'items' => $it->items->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'description' => $item->description ?? '',
                    ];
                })->toArray()
            ];
        });

        return view('admin.tours.create', compact(
            'tourTypes',
            'itineraries',
            'itineraryJson',
            'schedules',
            'languages',
            'amenities'
        ));
    }


    // /** =========================================================
    //  *  EDIT
    //  *  ========================================================= */
    // public function edit(Tour $tour)
    // {
    //     $tour->load([
    //         'prices.category',
    //         'schedules',
    //         'languages',
    //         'amenities',
    //         'excludedAmenities',
    //         'itinerary.items',
    //         'tourType',
    //         'translations'
    //     ]);

    //     $tourTypes   = TourType::where('is_active', true)->orderBy('name')->get();
    //     $itineraries = Itinerary::where('is_active', true)
    //         ->with('items')
    //         ->orderBy('created_at', 'desc')
    //         ->get();
    //     $schedules = Schedule::where('is_active', true)->orderBy('start_time')->get();
    //     $languages = TourLanguage::where('is_active', true)->orderBy('name')->get();
    //     $amenities = Amenity::where('is_active', true)->orderBy('name')->get();

    //     $itineraryJson = $itineraries->keyBy('itinerary_id')->map(function ($it) {
    //         return [
    //             'description' => $it->description ?? '',
    //             'items' => $it->items->map(function ($item) {
    //                 return [
    //                     'title' => $item->title,
    //                     'description' => $item->description ?? '',
    //                 ];
    //             })->toArray()
    //         ];
    //     });

    //     return view('admin.tours.edit', compact(
    //         'tour',
    //         'tourTypes',
    //         'itineraries',
    //         'itineraryJson',
    //         'schedules',
    //         'languages',
    //         'amenities'
    //     ));
    // }


    /** =========================================================
     *  STORE
     *  ========================================================= */
    public function store(Request $request, TranslatorInterface $translator)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:tours,slug',
            'overview'     => 'nullable|string',
            'max_capacity' => 'required|integer|min:1',
            'group_size'   => ['nullable','integer','min:1'], // <= FIX
            'length'       => 'nullable|numeric|min:0',
            'tour_type_id' => 'nullable|exists:tour_types,tour_type_id',
            'color'        => 'nullable|string|max:7',
            'is_active'    => 'boolean',

            // Precios por categoría
            'prices'                      => 'nullable|array',
            'prices.*.category_id'        => 'required|exists:customer_categories,category_id',
            'prices.*.price'              => 'required|numeric|min:0',
            'prices.*.min_quantity'       => 'required|integer|min:0',
            'prices.*.max_quantity'       => 'required|integer|min:0',
            'prices.*.is_active'          => 'boolean',

            // Itinerario
            'itinerary_id'                => 'nullable|exists:itineraries,itinerary_id',
            'new_itinerary.description'   => 'nullable|string',
            'new_itinerary.items'         => 'nullable|array',
            'new_itinerary.items.*.title' => 'required_with:new_itinerary.items|string|max:255',
            'new_itinerary.items.*.description' => 'nullable|string',

            // Horarios
            'schedules'                   => 'nullable|array',
            'schedules.*'                 => 'exists:schedules,schedule_id',
            'new_schedule.start_time'     => 'nullable|date_format:H:i',
            'new_schedule.end_time'       => 'nullable|date_format:H:i|after:new_schedule.start_time',
            'new_schedule.max_capacity'   => 'nullable|integer|min:1',
            'new_schedule.label'          => 'nullable|string|max:100',
            'new_schedule.create'         => 'boolean',

            // Idiomas y amenidades
            'languages'           => 'nullable|array',
            'languages.*'         => 'exists:tour_languages,tour_language_id',
            'included_amenities'  => 'nullable|array',
            'included_amenities.*'=> 'exists:amenities,amenity_id',
            'excluded_amenities'  => 'nullable|array',
            'excluded_amenities.*'=> 'exists:amenities,amenity_id',
        ]);

        try {
            return DB::transaction(function () use ($request, $translator) {
                // 1. Generar slug
                $slug = $request->slug
                    ? Str::slug($request->slug)
                    : Tour::generateUniqueSlug($request->name);

                // 2. Manejar itinerario (existente o nuevo)
                $itineraryId = null;

                if ($request->filled('itinerary_id') && $request->itinerary_id !== 'new') {
                    $itineraryId = $request->itinerary_id;
                } elseif ($request->filled('new_itinerary.description') || $request->filled('new_itinerary.items')) {
                    $itinerary = Itinerary::create([
                        'name' => 'Itinerario - ' . $request->name,
                        'description' => $request->input('new_itinerary.description'),
                        'is_active' => true,
                    ]);

                    if ($request->filled('new_itinerary.items')) {
                        foreach ($request->input('new_itinerary.items') as $index => $itemData) {
                            if (!empty($itemData['title'])) {
                                $item = ItineraryItem::create([
                                    'title' => $itemData['title'],
                                    'description' => $itemData['description'] ?? null,
                                    'is_active' => true,
                                ]);

                                // Attach item to itinerary
                                DB::table('itinerary_item_itinerary')->insert([
                                    'itinerary_id' => $itinerary->itinerary_id,
                                    'itinerary_item_id' => $item->item_id,
                                    'item_order' => $index,
                                    'is_active' => true,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }

                    $itineraryId = $itinerary->itinerary_id;
                }

                // 3. Crear el tour (ahora con group_size)
                $tour = Tour::create([
                    'name'         => trim($request->name),
                    'slug'         => $slug,
                    'overview'     => $request->overview ?? '',
                    'max_capacity' => (int) $request->max_capacity,
                    'group_size'   => $request->group_size, // <= NUEVO
                    'length'       => $request->length,
                    'tour_type_id' => $request->tour_type_id,
                    'itinerary_id' => $itineraryId,
                    'is_active'    => $request->boolean('is_active', true),
                    'color'        => $request->input('color', '#3490dc'),
                ]);

                // 4. Guardar precios por categoría
                if ($request->filled('prices')) {
                    foreach ($request->prices as $priceData) {
                        if (!empty($priceData['category_id']) && isset($priceData['price'])) {
                            TourPrice::create([
                                'tour_id'      => $tour->tour_id,
                                'category_id'  => $priceData['category_id'],
                                'price'        => $priceData['price'],
                                'min_quantity' => $priceData['min_quantity'] ?? 0,
                                'max_quantity' => $priceData['max_quantity'] ?? 12,
                                'is_active'    => $priceData['is_active'] ?? true,
                            ]);
                        }
                    }
                }

                // 5. Sincronizar horarios
                $scheduleIds = collect($request->schedules ?? [])->filter()->values()->all();

                if ($request->boolean('new_schedule.create') && $request->filled('new_schedule.start_time')) {
                    $newSchedule = Schedule::create([
                        'start_time'   => $request->input('new_schedule.start_time'),
                        'end_time'     => $request->input('new_schedule.end_time'),
                        'label'        => $request->input('new_schedule.label'),
                        'max_capacity' => $request->input('new_schedule.max_capacity', $tour->max_capacity),
                        'is_active'    => true,
                    ]);
                    $scheduleIds[] = $newSchedule->schedule_id;
                }

                if (!empty($scheduleIds)) {
                    $tour->schedules()->sync(
                        collect($scheduleIds)->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->all()
                    );
                }

                // 6. Sincronizar idiomas
                if ($request->filled('languages')) {
                    $tour->languages()->sync($request->languages);
                }

                // 7. Sincronizar amenidades
                if ($request->filled('included_amenities')) {
                    $tour->amenities()->sync($request->included_amenities);
                }

                if ($request->filled('excluded_amenities')) {
                    $tour->excludedAmenities()->sync($request->excluded_amenities);
                }

                // 8. Traducciones automáticas
                try {
                    $translatedNames     = $translator->translateAll($request->name ?? '');
                    $translatedOverviews = $translator->translateAll($request->overview ?? '');

                    foreach (['es', 'en', 'fr', 'pt', 'de'] as $locale) {
                        TourTranslation::create([
                            'tour_id'  => $tour->tour_id,
                            'locale'   => $locale,
                            'name'     => $translatedNames[$locale] ?? $request->name,
                            'overview' => $translatedOverviews[$locale] ?? $request->overview,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log pero no fallar si las traducciones fallan
                    \Log::warning('Translation failed', ['error' => $e->getMessage()]);
                }

                LoggerHelper::mutated($this->controller, 'store', 'tour', $tour->tour_id, [
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);

                return redirect()
                    ->route('admin.tours.index')
                    ->with('success', 'Tour creado exitosamente.');
            });
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'Error al crear el tour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /** =========================================================
     *  UPDATE
     *  ========================================================= */
    public function update(Request $request, Tour $tour, TranslatorInterface $translator)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:tours,slug,' . $tour->tour_id . ',tour_id',
            'overview'     => 'nullable|string',
            'max_capacity' => 'required|integer|min:1',
            'group_size'   => ['nullable','integer','min:1'], // <= NUEVO
            'length'       => 'nullable|numeric|min:0',
            'tour_type_id' => 'nullable|exists:tour_types,tour_type_id',
            'color'        => 'nullable|string|max:7',
            'is_active'    => 'boolean',

            'prices'                      => 'nullable|array',
            'prices.*.category_id'        => 'required|exists:customer_categories,category_id',
            'prices.*.price'              => 'required|numeric|min:0',
            'prices.*.min_quantity'       => 'required|integer|min:0',
            'prices.*.max_quantity'       => 'required|integer|min:0',
            'prices.*.is_active'          => 'boolean',

            'itinerary_id'                => 'nullable|exists:itineraries,itinerary_id',
            'new_itinerary.description'   => 'nullable|string',
            'new_itinerary.items'         => 'nullable|array',

            'schedules'                   => 'nullable|array',
            'new_schedule.start_time'     => 'nullable|date_format:H:i',
            'new_schedule.create'         => 'boolean',

            'languages'           => 'nullable|array',
            'included_amenities'  => 'nullable|array',
            'excluded_amenities'  => 'nullable|array',
        ]);

        try {
            return DB::transaction(function () use ($request, $tour, $translator) {
                // 1. Actualizar slug si cambió
                if ($request->filled('slug')) {
                    $tour->slug = Str::slug($request->slug);
                } elseif ($request->name !== $tour->name) {
                    $tour->slug = Tour::generateUniqueSlug($request->name, $tour->tour_id);
                }

                // 2. Manejar itinerario
                $itineraryId = $tour->itinerary_id;

                if ($request->filled('itinerary_id') && $request->itinerary_id !== 'new') {
                    $itineraryId = $request->itinerary_id;
                } elseif ($request->filled('new_itinerary.description') || $request->filled('new_itinerary.items')) {
                    $itinerary = Itinerary::create([
                        'name' => 'Itinerario - ' . $request->name,
                        'description' => $request->input('new_itinerary.description'),
                        'is_active' => true,
                    ]);

                    if ($request->filled('new_itinerary.items')) {
                        foreach ($request->input('new_itinerary.items') as $index => $itemData) {
                            if (!empty($itemData['title'])) {
                                $item = ItineraryItem::create([
                                    'title' => $itemData['title'],
                                    'description' => $itemData['description'] ?? null,
                                    'is_active' => true,
                                ]);

                                DB::table('itinerary_item_itinerary')->insert([
                                    'itinerary_id' => $itinerary->itinerary_id,
                                    'itinerary_item_id' => $item->item_id,
                                    'item_order' => $index,
                                    'is_active' => true,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }

                    $itineraryId = $itinerary->itinerary_id;
                }

                // 3. Actualizar tour (ahora con group_size)
                $tour->update([
                    'name'         => trim($request->name),
                    'overview'     => $request->overview ?? '',
                    'max_capacity' => (int) $request->max_capacity,
                    'group_size'   => $request->group_size, // <= NUEVO
                    'length'       => $request->length,
                    'tour_type_id' => $request->tour_type_id,
                    'itinerary_id' => $itineraryId,
                    'is_active'    => $request->boolean('is_active', $tour->is_active),
                    'color'        => $request->input('color', $tour->color),
                ]);

                // 4. Actualizar precios (eliminar y recrear)
                TourPrice::where('tour_id', $tour->tour_id)->delete();

                if ($request->filled('prices')) {
                    foreach ($request->prices as $priceData) {
                        if (!empty($priceData['category_id']) && isset($priceData['price'])) {
                            TourPrice::create([
                                'tour_id'      => $tour->tour_id,
                                'category_id'  => $priceData['category_id'],
                                'price'        => $priceData['price'],
                                'min_quantity' => $priceData['min_quantity'] ?? 0,
                                'max_quantity' => $priceData['max_quantity'] ?? 12,
                                'is_active'    => $priceData['is_active'] ?? true,
                            ]);
                        }
                    }
                }

                // 5. Sincronizar horarios
                $scheduleIds = collect($request->schedules ?? [])->filter()->values()->all();

                if ($request->boolean('new_schedule.create') && $request->filled('new_schedule.start_time')) {
                    $newSchedule = Schedule::create([
                        'start_time'   => $request->input('new_schedule.start_time'),
                        'end_time'     => $request->input('new_schedule.end_time'),
                        'label'        => $request->input('new_schedule.label'),
                        'max_capacity' => $request->input('new_schedule.max_capacity', $tour->max_capacity),
                        'is_active'    => true,
                    ]);
                    $scheduleIds[] = $newSchedule->schedule_id;
                }

                $tour->schedules()->sync(
                    !empty($scheduleIds)
                        ? collect($scheduleIds)->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->all()
                        : []
                );

                // 6. Sincronizar idiomas y amenidades
                $tour->languages()->sync($request->languages ?? []);
                $tour->amenities()->sync($request->included_amenities ?? []);
                $tour->excludedAmenities()->sync($request->excluded_amenities ?? []);

                // 7. Actualizar traducciones
                try {
                    $translatedNames     = $translator->translateAll($request->name ?? '');
                    $translatedOverviews = $translator->translateAll($request->overview ?? '');

                    foreach (['es', 'en', 'fr', 'pt', 'de'] as $locale) {
                        TourTranslation::updateOrCreate(
                            ['tour_id' => $tour->tour_id, 'locale' => $locale],
                            [
                                'name'     => $translatedNames[$locale] ?? $request->name,
                                'overview' => $translatedOverviews[$locale] ?? $request->overview,
                            ]
                        );
                    }
                } catch (\Exception $e) {
                    \Log::warning('Translation update failed', ['error' => $e->getMessage()]);
                }

                LoggerHelper::mutated($this->controller, 'update', 'tour', $tour->tour_id, [
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);

                return redirect()
                    ->route('admin.tours.index')
                    ->with('success', 'Tour actualizado exitosamente.');
            });
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour', $tour->tour_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'Error al actualizar el tour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /** =========================================================
     *  TOGGLE (activar/inactivar)
     *  ========================================================= */
    public function toggle(Request $request, Tour $tour)
    {
        try {
            $tour->update(['is_active' => !$tour->is_active]);

            LoggerHelper::mutated($this->controller, 'toggle', 'tour', $tour->tour_id, [
                'is_active' => $tour->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $message = $tour->is_active ? 'Tour activado' : 'Tour desactivado';

            return redirect()
                ->route('admin.tours.index')
                ->with('success', $message);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour', $tour->tour_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al cambiar estado del tour.');
        }
    }

    /** =========================================================
     *  DESTROY (Soft Delete)
     *  ========================================================= */
    public function destroy(Request $request, Tour $tour)
    {
        try {
            $tour->delete();

            LoggerHelper::mutated($this->controller, 'destroy(soft)', 'tour', $tour->tour_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.index', ['status' => 'archived'])
                ->with('success', 'Tour movido a Eliminados.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy(soft)', 'tour', $tour->tour_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al eliminar el tour.');
        }
    }

    /** =========================================================
     *  RESTORE
     *  ========================================================= */
    public function restore(Request $request, $tourId)
    {
        try {
            $tour = Tour::withTrashed()->findOrFail($tourId);
            $tour->restore();

            LoggerHelper::mutated($this->controller, 'restore', 'tour', $tour->tour_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.index')
                ->with('success', 'Tour restaurado exitosamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'restore', 'tour', $tourId, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al restaurar el tour.');
        }
    }

    /** =========================================================
     *  PURGE (Force Delete)
     *  ========================================================= */

public function purge(Request $request, $tourId)
{
    $userId = optional($request->user())->getAuthIdentifier();

    try {
        // Sólo tours archivados (soft deleted)
        $tour = Tour::onlyTrashed()->findOrFail($tourId);

        // Snapshots simples para el log de Laravel
        $tourIdSnapshot   = $tour->tour_id;
        $tourNameSnapshot = $tour->name;

        DB::transaction(function () use ($tour) {
            // 1) Hacer snapshot del nombre en reservas (si no lo tienen)
            DB::table('bookings')
                ->where('tour_id', $tour->tour_id)
                ->whereNull('tour_name_snapshot')
                ->update(['tour_name_snapshot' => $tour->name]);

            DB::table('booking_details')
                ->where('tour_id', $tour->tour_id)
                ->whereNull('tour_name_snapshot')
                ->update(['tour_name_snapshot' => $tour->name]);

            // 2) Desasociar tour de reservas
            BookingDetail::where('tour_id', $tour->tour_id)->update(['tour_id' => null]);
            Booking::where('tour_id', $tour->tour_id)->update(['tour_id' => null]);

            // 3) Limpiar precios
            TourPrice::where('tour_id', $tour->tour_id)->delete();

            // 4) Limpiar relaciones adicionales (imágenes, pivotes, traducciones)
            try {
                if (class_exists(\App\Models\TourImage::class)) {
                    \App\Models\TourImage::where('tour_id', $tour->tour_id)->delete();
                }
            } catch (\Throwable $e) {}

            try { $tour->schedules()->detach(); } catch (\Throwable $e) {}
            try { $tour->languages()->detach(); } catch (\Throwable $e) {}
            try { $tour->amenities()->detach(); } catch (\Throwable $e) {}
            try { $tour->excludedAmenities()->detach(); } catch (\Throwable $e) {}
            try { TourTranslation::where('tour_id', $tour->tour_id)->delete(); } catch (\Throwable $e) {}

            // 5) 🧹 Borrar logs de auditoría de este tour
            //    (para que la FK/constraint no se dispare al borrar el tour)
            try {
                TourAuditLog::where('tour_id', $tour->tour_id)->delete();
            } catch (\Throwable $e) {
                // Si algo falla, lo dejamos registrar pero no rompemos el flujo
                \Log::warning('No se pudieron borrar los tour_audit_logs antes del purge', [
                    'tour_id' => $tour->tour_id,
                    'error'   => $e->getMessage(),
                ]);
            }

            // 6) Borrado definitivo SIN disparar eventos de modelo
            Tour::withoutEvents(function () use ($tour) {
                $tour->forceDelete();
            });
        });

        // Log normal de Laravel (archivo de logs)
        \Log::info('Tour purged (hard delete)', [
            'tour_id'   => $tourIdSnapshot,
            'tour_name' => $tourNameSnapshot,
            'user_id'   => $userId,
        ]);

        return redirect()
            ->route('admin.tours.index', ['status' => 'archived'])
            ->with('success', 'Tour eliminado definitivamente.');
    } catch (\Exception $e) {
        LoggerHelper::exception($this->controller, 'purge(hard)', 'tour', $tourId, $e, [
            'user_id' => $userId,
        ]);

        return back()->with('error', 'Error al purgar el tour.');
    }
}


}
