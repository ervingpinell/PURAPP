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
use App\Models\TourLanguage;
use App\Models\Amenity;
use App\Models\HotelList;
use App\Models\Schedule;
use App\Models\TourTranslation;

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
        $status = request('status', 'active'); // active | inactive | archived | all

        $base = Tour::query()
            ->with([
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
            ]);

        // withCount(bookings) solo si existe la relación
        if (method_exists(Tour::class, 'bookings')) {
            $base->withCount('bookings');
        }

        // Filtros por estado
        if ($status === 'archived') {
            $base->onlyTrashed();
        } elseif ($status === 'all') {
            $base->withTrashed();
        } elseif ($status === 'inactive') {
            $base->where('is_active', false);
        } else { // active (default)
            $base->where('is_active', true);
        }

        $toursWithRelations = $base->orderBy('tour_id')->get();

        $tourTypeOptions         = TourType::where('is_active', true)->orderBy('name')->get();
        $itineraryOptions        = Itinerary::where('is_active', true)->orderBy('name')->get();
        $languageOptions         = TourLanguage::where('is_active', true)->orderBy('name')->get();
        $amenityOptions          = Amenity::where('is_active', true)->orderBy('name')->get();
        $availableItineraryItems = collect((new ItineraryService)->getAvailableItems())->where('is_active', true)->values();
        $hotelOptions            = HotelList::where('is_active', true)->orderBy('name')->get();
        $scheduleCatalog         = Schedule::where('is_active', true)->orderBy('start_time')->orderBy('label')->get();

        return view('admin.tours.index', [
            'tours'          => $toursWithRelations,
            'tourtypes'      => $tourTypeOptions,
            'itineraries'    => $itineraryOptions,
            'languages'      => $languageOptions,
            'amenities'      => $amenityOptions,
            'availableItems' => $availableItineraryItems,
            'hotels'         => $hotelOptions,
            'allSchedules'   => $scheduleCatalog,
            'status'         => $status,
        ]);
    }

    /** =========================================================
     *  EDIT
     *  ========================================================= */
    public function edit(Tour $tour)
    {
        $tourTypeOptions  = TourType::where('is_active', true)->orderBy('name')->get();
        $itineraryOptions = Itinerary::where('is_active', true)
            ->with(['items' => fn ($relationQuery) => $relationQuery->wherePivot('is_active', true)])
            ->orderBy('name')
            ->get();
        $languageOptions  = TourLanguage::where('is_active', true)->orderBy('name')->get();
        $amenityOptions   = Amenity::where('is_active', true)->orderBy('name')->get();
        $availableItems   = collect((new ItineraryService)->getAvailableItems())
            ->where('is_active', true)
            ->values();

        return view('admin.tours.edit', [
            'tour'          => $tour,
            'tourtypes'     => $tourTypeOptions,
            'itineraries'   => $itineraryOptions,
            'languages'     => $languageOptions,
            'amenities'     => $amenityOptions,
            'availableItems'=> $availableItems,
        ]);
    }

    /** =========================================================
     *  STORE
     *  ========================================================= */
    public function store(StoreTourRequest $request, TranslatorInterface $translator)
    {
        $validatedData = $request->validated();

        try {
            DB::transaction(function () use ($validatedData, $request, $translator) {
                // Generar slug si no viene
                $slug = !empty($validatedData['slug'])
                    ? Str::slug($validatedData['slug'])
                    : Tour::generateUniqueSlug($validatedData['name']);

                $tour = Tour::create([
                    'name'         => trim($validatedData['name']),
                    'slug'         => $slug,
                    'overview'     => $validatedData['overview'] ?? '',
                    'adult_price'  => $validatedData['adult_price'],
                    'kid_price'    => $validatedData['kid_price'] ?? 0,
                    'max_capacity' => (int) $validatedData['max_capacity'],
                    'length'       => $validatedData['length'],
                    'tour_type_id' => $validatedData['tour_type_id'],
                    'itinerary_id' => $validatedData['itinerary_id'],
                    'is_active'    => true,
                    'color'        => $validatedData['color'] ?? $request->input('color', '#5cb85c'),
                    'viator_code'  => $validatedData['viator_code'] ?? $request->input('viator_code'),
                ]);

                $tour->languages()->sync($validatedData['languages'] ?? []);
                $tour->amenities()->sync($validatedData['amenities'] ?? []);
                $tour->excludedAmenities()->sync($validatedData['excluded_amenities'] ?? []);

                $selectedScheduleIds = [];

                foreach ($validatedData['schedules_existing'] ?? [] as $existingScheduleId) {
                    $selectedScheduleIds[] = (int) $existingScheduleId;
                }

                foreach ($validatedData['schedules_new_norm'] ?? [] as $normalizedSchedule) {
                    $capacityForSchedule = $normalizedSchedule['cap'] ?? (int) $validatedData['max_capacity'];

                    $matchingSchedule = Schedule::where('start_time', $normalizedSchedule['start'] . ':00')
                        ->where('end_time', $normalizedSchedule['end'] . ':00')
                        ->when($normalizedSchedule['label'], fn ($query) => $query->where('label', $normalizedSchedule['label']))
                        ->when(!$normalizedSchedule['label'], fn ($query) => $query->whereNull('label'))
                        ->where('max_capacity', $capacityForSchedule)
                        ->first();

                    if (!$matchingSchedule) {
                        $matchingSchedule = Schedule::create([
                            'start_time'   => $normalizedSchedule['start'],
                            'end_time'     => $normalizedSchedule['end'],
                            'label'        => $normalizedSchedule['label'],
                            'max_capacity' => $capacityForSchedule,
                            'is_active'    => true,
                        ]);
                    }

                    $selectedScheduleIds[] = $matchingSchedule->schedule_id;
                }

                $pivotAssignments = collect($selectedScheduleIds)
                    ->unique()
                    ->values()
                    ->mapWithKeys(fn ($scheduleId) => [(int) $scheduleId => ['is_active' => true]])
                    ->all();

                $tour->schedules()->sync($pivotAssignments);

                $translatedNames     = $translator->translateAll($validatedData['name'] ?? '');
                $translatedOverviews = $translator->translateAll($validatedData['overview'] ?? '');

                foreach (['es', 'en', 'fr', 'pt', 'de'] as $locale) {
                    TourTranslation::create([
                        'tour_id'  => $tour->tour_id,
                        'locale'   => $locale,
                        'name'     => $translatedNames[$locale]     ?? ($validatedData['name'] ?? ''),
                        'overview' => $translatedOverviews[$locale] ?? ($validatedData['overview'] ?? ''),
                    ]);
                }

                LoggerHelper::mutated($this->controller, 'store', 'tour', $tour->tour_id, [
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
            });

            return redirect()
                ->route('admin.tours.index')
                ->with('success', __('m_tours.tour.success.created'));
        } catch (Exception $exception) {
            LoggerHelper::exception($this->controller, 'store', 'tour', null, $exception, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', __('m_tours.tour.error.create'))
                ->withInput()
                ->with('showCreateModal', true);
        }
    }

    /** =========================================================
     *  UPDATE
     *  ========================================================= */
    public function update(UpdateTourRequest $request, Tour $tour)
    {
        $validatedData = $request->validated();

        try {
            DB::transaction(function () use ($tour, $validatedData, $request) {
                // Manejar slug personalizado o regenerar
                if (!empty($validatedData['slug'])) {
                    $tour->slug = Str::slug($validatedData['slug']);
                } elseif ($request->has('regenerate_slug')) {
                    $tour->slug = Tour::generateUniqueSlug($tour->name, $tour->tour_id);
                }

                $tour->update([
                    'name'         => trim($validatedData['name']),
                    'overview'     => $validatedData['overview'] ?? '',
                    'adult_price'  => $validatedData['adult_price'],
                    'kid_price'    => $validatedData['kid_price'] ?? 0,
                    'max_capacity' => (int) $validatedData['max_capacity'],
                    'length'       => $validatedData['length'],
                    'tour_type_id' => $validatedData['tour_type_id'],
                    'itinerary_id' => $validatedData['itinerary_id'],
                    'color'        => $validatedData['color'] ?? $request->input('color', '#5cb85c'),
                    'viator_code'  => $validatedData['viator_code'] ?? $request->input('viator_code'),
                ]);

                $tour->languages()->sync($validatedData['languages'] ?? []);
                $tour->amenities()->sync($validatedData['amenities'] ?? []);
                $tour->excludedAmenities()->sync($validatedData['excluded_amenities'] ?? []);

                $selectedScheduleIds = [];

                foreach ($validatedData['schedules_existing'] ?? [] as $existingScheduleId) {
                    $selectedScheduleIds[] = (int) $existingScheduleId;
                }

                foreach ($validatedData['schedules_new_norm'] ?? [] as $normalizedSchedule) {
                    $capacityForSchedule = $normalizedSchedule['cap'] ?? (int) $validatedData['max_capacity'];

                    $matchingSchedule = Schedule::where('start_time', $normalizedSchedule['start'] . ':00')
                        ->where('end_time', $normalizedSchedule['end'] . ':00')
                        ->when($normalizedSchedule['label'], fn ($query) => $query->where('label', $normalizedSchedule['label']))
                        ->when(!$normalizedSchedule['label'], fn ($query) => $query->whereNull('label'))
                        ->where('max_capacity', $capacityForSchedule)
                        ->first();

                    if (!$matchingSchedule) {
                        $matchingSchedule = Schedule::create([
                            'start_time'   => $normalizedSchedule['start'],
                            'end_time'     => $normalizedSchedule['end'],
                            'label'        => $normalizedSchedule['label'],
                            'max_capacity' => $capacityForSchedule,
                            'is_active'    => true,
                        ]);
                    }

                    $selectedScheduleIds[] = $matchingSchedule->schedule_id;
                }

                $pivotAssignments = collect($selectedScheduleIds)
                    ->unique()
                    ->values()
                    ->mapWithKeys(fn ($scheduleId) => [(int) $scheduleId => ['is_active' => true]])
                    ->all();

                $tour->schedules()->sync($pivotAssignments);

                TourTranslation::updateOrCreate(
                    ['tour_id' => $tour->tour_id, 'locale' => 'es'],
                    ['name' => $validatedData['name'], 'overview' => $validatedData['overview'] ?? '']
                );

                LoggerHelper::mutated($this->controller, 'update', 'tour', $tour->tour_id, [
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
            });

            return redirect()
                ->route('admin.tours.index')
                ->with('success', __('m_tours.tour.success.updated'));
        } catch (Exception $exception) {
            LoggerHelper::exception($this->controller, 'update', 'tour', $tour->tour_id, $exception, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', __('m_tours.tour.error.update'))
                ->withInput()
                ->with('showEditModal', $tour->tour_id);
        }
    }

    /** =========================================================
     *  TOGGLE (activar/inactivar)
     *  ========================================================= */
    public function toggle(ToggleTourRequest $request, Tour $tour)
    {
        try {
            $tour->update(['is_active' => ! $tour->is_active]);

            LoggerHelper::mutated($this->controller, 'toggle', 'tour', $tour->tour_id, [
                'is_active' => $tour->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $statusMessage = $tour->is_active
                ? __('m_tours.tour.success.activated')
                : __('m_tours.tour.success.deactivated');

            return redirect()
                ->route('admin.tours.index')
                ->with('success', $statusMessage);
        } catch (Exception $exception) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour', $tour->tour_id, $exception, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.tour.error.toggle'));
        }
    }

    /** =========================================================
     *  NUEVOS MÉTODOS: Eliminar (soft), Restaurar y Purgar
     *  ========================================================= */

    /** Eliminar (soft delete) => se mueve a "Eliminados" */
    public function destroy(Request $request, Tour $tour)
    {
        try {
            $tour->delete();

            LoggerHelper::mutated($this->controller, 'destroy(soft)', 'tour', $tour->tour_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.index', ['status' => 'archived'])
                ->with('success', __('m_tours.tour.success.archived'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy(soft)', 'tour', $tour->tour_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.tour.error.delete'));
        }
    }

    /** Restaurar desde "Eliminados" */
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
                ->with('success', __('m_tours.tour.success.restored'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'restore', 'tour', $tourId, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.tour.error.restore'));
        }
    }

    /** Eliminar definitivamente (purge) — CONSERVANDO reservas */
    public function purge(Request $request, $tourId)
    {
        try {
            $tour = Tour::onlyTrashed()->findOrFail($tourId);

            DB::transaction(function () use ($tour) {
                // 0) Snapshot del nombre del tour en bookings y booking_details (si no estuviera ya)
                DB::table('bookings')
                    ->where('tour_id', $tour->tour_id)
                    ->whereNull('tour_name_snapshot')
                    ->update(['tour_name_snapshot' => $tour->name]);

                DB::table('booking_details')
                    ->where('tour_id', $tour->tour_id)
                    ->whereNull('tour_name_snapshot')
                    ->update(['tour_name_snapshot' => $tour->name]);

                // 1) Reunir todas las reservas vinculadas al tour (por si necesitas usarlas)
                $bookingIdsFromDetails = BookingDetail::where('tour_id', $tour->tour_id)->pluck('booking_id');
                $bookingIdsFromHeader  = Booking::where('tour_id', $tour->tour_id)->pluck('booking_id');
                $bookingIds = $bookingIdsFromDetails->merge($bookingIdsFromHeader)->filter()->unique()->values();

                // 2) Desasociar el tour de reservas (NO borrar reservas ni redenciones/promos)
                BookingDetail::where('tour_id', $tour->tour_id)->update(['tour_id' => null]);
                Booking::where('tour_id', $tour->tour_id)->update(['tour_id' => null]);

                // 3) Opcional: limpiar reviews estrictamente ligadas al tour (si tu FK no permite null)
                try {
                    if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'tour_id')) {
                        // Si la FK no admite null podrías borrar; si admite null, mejor setear a null.
                        // \App\Models\Review::where('tour_id', $tour->tour_id)->delete();
                    }
                } catch (\Throwable $e) {}

                // 4) Imágenes / traducciones / pivotes
                try { if (class_exists(\App\Models\TourImage::class)) { \App\Models\TourImage::where('tour_id', $tour->tour_id)->delete(); } } catch (\Throwable $e) {}
                try { $tour->schedules()->detach(); } catch (\Throwable $e) {}
                try { $tour->languages()->detach(); } catch (\Throwable $e) {}
                try { $tour->amenities()->detach(); } catch (\Throwable $e) {}
                try { $tour->excludedAmenities()->detach(); } catch (\Throwable $e) {}
                try { TourTranslation::where('tour_id', $tour->tour_id)->delete(); } catch (\Throwable $e) {}

                // 5) Borrado definitivo del tour
                $tour->forceDelete();
            });

            LoggerHelper::mutated($this->controller, 'purge(hard)', 'tour', $tourId, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.index')
                ->with('success', __('m_tours.tour.success.purged'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'purge(hard)', 'tour', $tourId, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.tour.error.purge'));
        }
    }
}
