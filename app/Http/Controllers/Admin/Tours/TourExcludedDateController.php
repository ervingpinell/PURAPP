<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourAvailability;
use App\Models\TourExcludedDate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Exception;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\TourExcludedDate\StoreExcludedDateRequest;
use App\Http\Requests\Tour\TourExcludedDate\UpdateExcludedDateRequest;
use App\Http\Requests\Tour\TourExcludedDate\ToggleExcludedDateRequest;
use App\Http\Requests\Tour\TourExcludedDate\BulkToggleExcludedDatesRequest;
use App\Http\Requests\Tour\TourExcludedDate\StoreMultipleExcludedDatesRequest;
use App\Http\Requests\Tour\TourExcludedDate\BlockAllRequest;
use App\Http\Requests\Tour\TourExcludedDate\DestroySelectedExcludedDatesRequest;

class TourExcludedDateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-tour-excluded-dates'])->only(['index', 'blocked']);
        $this->middleware(['can:create-tour-excluded-dates'])->only(['store']);
        $this->middleware(['can:edit-tour-excluded-dates'])->only(['update']);
        $this->middleware(['can:publish-tour-excluded-dates'])->only(['toggle', 'bulkToggle', 'storeMultiple', 'blockAll']);
        $this->middleware(['can:delete-tour-excluded-dates'])->only(['destroy', 'destroyAll', 'destroySelected']);
    }

    protected string $controller = 'TourExcludedDateController';

    // NEW: Resolver nombre traducido si el modelo lo expone.
    private function resolveTourName(Tour $tour, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if (method_exists($tour, 'getTranslatedName')) {
            try {
                $n = $tour->getTranslatedName($locale);
                if (is_string($n) && $n !== '') return $n;
            } catch (\Throwable $e) {
                // fallback abajo
            }
        }
        // Fallbacks comunes: atributo translated_name, o relación translations
        if (!empty($tour->translated_name)) {
            return (string) $tour->translated_name;
        }
        if (isset($tour->translations) && is_iterable($tour->translations)) {
            $match = collect($tour->translations)->first(
                fn($t) => ($t['locale'] ?? $t->locale ?? null) === $locale && !empty($t['name'] ?? $t->name ?? null)
            );
            if ($match) return (string) ($match['name'] ?? $match->name);
        }
        return (string) $tour->name;
    }

    public function index(Request $request)
    {
        $timezone         = config('app.timezone', 'America/Costa_Rica');
        $today            = Carbon::today($timezone)->startOfDay();
        $todayDateString  = $today->toDateString();

        $this->purgePastExcludedDates($todayDateString);
        $this->purgePastAvailabilities($todayDateString);

        $requestedStart   = Carbon::parse($request->input('date', $todayDateString), $timezone);
        $startDate        = $requestedStart->lt($today) ? $today : $requestedStart;
        $startDateString  = $startDate->toDateString();

        $daysRequested    = (int) $request->input('days', 7);
        $days             = max(1, min(30, $daysRequested));
        $searchQuery      = trim((string) $request->input('q', ''));

        $tours = Tour::with('schedules')
            ->when($searchQuery !== '', function ($query) use ($searchQuery) {
                // Búsqueda básica por nombre base; si usas tabla de traducciones,
                // puedes agregar aquí un join/whereHas.
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->orderBy('name')
            ->get();

        $endDateString = Carbon::parse($startDateString, $timezone)->addDays($days - 1)->toDateString();
        $dateRange     = collect(CarbonPeriod::create($startDateString, $endDateString))->map->toDateString();

        $availabilityRecords = TourAvailability::whereIn('date', $dateRange)->get();
        $exclusionRecords    = TourExcludedDate::whereIn('start_date', $dateRange)->get();

        $calendar = $this->buildCalendar($tours, $dateRange, $timezone, $availabilityRecords, $exclusionRecords);

        return view('admin.tours.excluded_dates.index', [
            'calendar' => $calendar,
            'date'     => $startDateString,
            'days'     => $days,
            'q'        => $searchQuery,
        ]);
    }


    public function blocked(Request $request)
    {
        $timezone        = config('app.timezone', 'America/Costa_Rica');
        $startDateString = $request->input('date', Carbon::today($timezone)->toDateString());
        $days            = (int) $request->input('days', 7);
        $searchQuery     = trim((string) $request->input('q', ''));

        $tours = Tour::with('schedules')
            ->when($searchQuery !== '', fn($query) => $query->where('name', 'like', "%{$searchQuery}%"))
            ->orderBy('name')
            ->get();

        $dateRange = collect(CarbonPeriod::create($startDateString, $days - 1))->map->toDateString();

        $availabilityRecords = TourAvailability::whereIn('date', $dateRange)->get();
        $exclusionRecords    = TourExcludedDate::whereIn('start_date', $dateRange)->get();

        $calendar = $this->buildCalendar($tours, $dateRange, $timezone, $availabilityRecords, $exclusionRecords, onlyBlocked: true);
        $calendar = array_filter($calendar, fn($buckets) => count($buckets['am']) + count($buckets['pm']) > 0);

        return view('admin.tours.excluded_dates.blocked', [
            'calendar' => $calendar,
            'date'     => $startDateString,
            'days'     => $days,
            'q'        => $searchQuery,
        ]);
    }

    public function store(StoreExcludedDateRequest $request)
    {
        try {
            $alreadyExists = TourExcludedDate::where('tour_id', $request->tour_id)
                ->where('schedule_id', $request->schedule_id)
                ->whereDate('start_date', $request->start_date)
                ->exists();

            if (!$alreadyExists) {
                TourExcludedDate::create($request->validated());
            }

            LoggerHelper::mutated($this->controller, 'store', 'tour_excluded_date', null, [
                'tour_id'     => $request->tour_id,
                'schedule_id' => $request->schedule_id,
                'start_date'  => $request->start_date,
                'user_id'     => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Fecha bloqueada creada correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'tour_excluded_date', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'No se pudo crear la fecha bloqueada.');
        }
    }

    public function update(UpdateExcludedDateRequest $request, TourExcludedDate $excludedDate)
    {
        try {
            $excludedDate->update($request->validated());

            LoggerHelper::mutated($this->controller, 'update', 'tour_excluded_date', $excludedDate->tour_excluded_date_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.tours.excluded_dates.index')
                ->with('success', 'Fecha bloqueada actualizada correctamente.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'tour_excluded_date', $excludedDate->tour_excluded_date_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'No se pudo actualizar la fecha bloqueada.');
        }
    }

    public function destroy(TourExcludedDate $excludedDate)
    {
        try {
            $excludedDateId = $excludedDate->tour_excluded_date_id;
            $excludedDate->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_excluded_date', $excludedDateId, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Fecha bloqueada eliminada.');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_excluded_date', $excludedDate->tour_excluded_date_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'No se pudo eliminar la fecha bloqueada.');
        }
    }

    public function blockAll(BlockAllRequest $request)
    {
        try {
            $startDate = $request->start_date;
            $endDate   = $request->end_date ?? $startDate;
            $reason    = $request->reason ?? 'Bloqueo total';

            $tours     = Tour::with('schedules')->get();
            $dateRange = CarbonPeriod::create($startDate, $endDate);

            foreach ($tours as $tour) {
                foreach ($tour->schedules as $schedule) {
                    foreach ($dateRange as $date) {
                        $day = $date->format('Y-m-d');

                        TourAvailability::updateOrCreate(
                            [
                                'tour_id'     => $tour->tour_id,
                                'schedule_id' => $schedule->schedule_id,
                                'date'        => $day,
                            ],
                            [
                                'is_blocked' => true,
                                'max_capacity' => null,
                            ]
                        );

                        $exists = TourExcludedDate::where('tour_id', $tour->tour_id)
                            ->where('schedule_id', $schedule->schedule_id)
                            ->whereDate('start_date', $day)
                            ->exists();

                        if (!$exists) {
                            TourExcludedDate::create([
                                'tour_id'     => $tour->tour_id,
                                'schedule_id' => $schedule->schedule_id,
                                'start_date'  => $day,
                                'end_date'    => $day,
                                'reason'      => $reason,
                            ]);
                        }
                    }
                }
            }

            LoggerHelper::mutated($this->controller, 'blockAll', 'tour_excluded_date', null, [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'user_id'    => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'blockAll', 'tour_excluded_date', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function toggle(ToggleExcludedDateRequest $request)
    {
        try {
            $result = $this->performToggle([
                'tour_id'     => (int) $request->tour_id,
                'schedule_id' => (int) $request->schedule_id,
                'date'        => (string) $request->date,
                'want'        => $request->input('want'),
                'reason'      => $request->input('reason'),
            ]);

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_availability', null, [
                'tour_id'      => $request->tour_id,
                'schedule_id'  => $request->schedule_id,
                'date'         => $request->date,
                'is_available' => $result['is_available'],
                'user_id'      => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json($result);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_availability', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => false, 'error' => 'Toggle failed'], 500);
        }
    }

    public function bulkToggle(BulkToggleExcludedDatesRequest $request)
    {
        try {
            $changedCount = 0;

            foreach ($request->input('items', []) as $item) {
                $payload = [
                    'tour_id'     => (int) $item['tour_id'],
                    'schedule_id' => (int) $item['schedule_id'],
                    'date'        => (string) $item['date'],
                    'want'        => $request->input('want'),
                    'reason'      => $request->input('reason'),
                ];

                $result = $this->performToggle($payload);
                if (!empty($result['ok']) && $result['ok'] === true) {
                    $changedCount++;
                }
            }

            LoggerHelper::mutated($this->controller, 'bulkToggle', 'tour_availability', null, [
                'items'   => $changedCount,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => true, 'changed' => $changedCount]);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'bulkToggle', 'tour_availability', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => false, 'error' => 'Bulk toggle failed'], 500);
        }
    }

    public function storeMultiple(StoreMultipleExcludedDatesRequest $request)
    {
        try {
            $changedCount = 0;

            foreach ($request->input('items', []) as $item) {
                $result = $this->performToggle([
                    'tour_id'     => (int) $item['tour_id'],
                    'schedule_id' => (int) $item['schedule_id'],
                    'date'        => (string) $item['date'],
                    'want'        => 'block',
                    'reason'      => $request->input('reason', 'Bloqueo múltiple'),
                ]);

                if (!empty($result['ok']) && $result['ok'] === true) {
                    $changedCount++;
                }
            }

            LoggerHelper::mutated($this->controller, 'storeMultiple', 'tour_excluded_date', null, [
                'items'   => $changedCount,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => true, 'changed' => $changedCount]);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'storeMultiple', 'tour_excluded_date', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['ok' => false, 'error' => 'Store multiple failed'], 500);
        }
    }

    public function destroyAll()
    {
        TourExcludedDate::truncate();
        return back()->with('success', 'Todas las fechas bloqueadas han sido eliminadas.');
    }

    public function destroySelected(DestroySelectedExcludedDatesRequest $request)
    {
        try {
            $idsToDelete = $request->input('ids', []);
            TourExcludedDate::whereIn('tour_excluded_date_id', $idsToDelete)->delete();

            LoggerHelper::mutated($this->controller, 'destroySelected', 'tour_excluded_date', null, [
                'count'   => count($idsToDelete),
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['success' => 'Fechas eliminadas correctamente.']);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroySelected', 'tour_excluded_date', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return response()->json(['error' => 'No se pudieron eliminar las fechas.'], 500);
        }
    }

    private function purgePastExcludedDates(string $todayDateString): void
    {
        TourExcludedDate::whereDate('end_date', '<', $todayDateString)
            ->orWhere(function ($query) use ($todayDateString) {
                $query->whereNull('end_date')->whereDate('start_date', '<', $todayDateString);
            })
            ->delete();
    }

    private function purgePastAvailabilities(string $todayDateString): void
    {
        TourAvailability::whereDate('date', '<', $todayDateString)->delete();
    }

    /**
     * Construye el calendario de disponibilidad con capacidades correctas
     */
    private function buildCalendar($tours, $dateRange, string $timezone, $availabilityRecords, $exclusionRecords, bool $onlyBlocked = false): array
    {
        $calendar = [];
        $capacityService = app(\App\Services\Bookings\BookingCapacityService::class);
        $locale = app()->getLocale() ?: 'es';

        foreach ($dateRange as $dateString) {
            $calendar[$dateString] = ['am' => [], 'pm' => []];

            foreach ($tours as $tour) {
                foreach ($tour->schedules as $schedule) {
                    $startTime = \Carbon\Carbon::parse($schedule->start_time, $timezone);
                    $scheduleBucket = ((int) $startTime->format('H') < 12) ? 'am' : 'pm';
                    $formattedTime = $startTime->format('g:ia');

                    // Disponibilidad
                    $availability = $availabilityRecords->first(
                        fn($record) =>
                        $record->tour_id == $tour->tour_id &&
                            $record->schedule_id == $schedule->schedule_id &&
                            $record->date === $dateString
                    );

                    $exclusion = $exclusionRecords->first(
                        fn($record) =>
                        $record->tour_id == $tour->tour_id &&
                            $record->schedule_id == $schedule->schedule_id &&
                            \Carbon\Carbon::parse($record->start_date, $timezone)->toDateString() === $dateString
                    );

                    $isAvailable = $availability !== null
                        ? !$availability->is_blocked
                        : ($exclusion === null);

                    // Capacidad
                    $snapshot = $capacityService->capacitySnapshot($tour, $schedule, $dateString);
                    $effectiveCapacity = $snapshot['max'];
                    $occupiedCount = $snapshot['confirmed'] + $snapshot['held'];

                    // Nivel de override
                    $overrideLevel = $tour->getCapacityOverrideLevel($dateString, $schedule->schedule_id);
                    if (!$isAvailable) {
                        $overrideLevel = 'blocked';
                    }

                    if ($onlyBlocked && $isAvailable) {
                        continue;
                    }

                    // === NOMBRE TRADUCIDO ===
                    $tourName = method_exists($tour, 'getTranslatedName')
                        ? ($tour->getTranslatedName($locale) ?? $tour->name)
                        : $tour->name;

                    $entry = [
                        'tour_id'          => $tour->tour_id,
                        'tour_name'        => $tourName,
                        'schedule_id'      => $schedule->schedule_id,
                        'time'             => $formattedTime,
                        'is_available'     => $isAvailable,
                        'date'             => $dateString,
                        'current_capacity' => $effectiveCapacity,
                        'occupied_count'   => $occupiedCount,
                        'override_level'   => $overrideLevel,
                    ];

                    $calendar[$dateString][$scheduleBucket][] = $entry;
                }
            }

            foreach (['am', 'pm'] as $bucket) {
                usort(
                    $calendar[$dateString][$bucket],
                    fn($left, $right) => strnatcasecmp($left['tour_name'], $right['tour_name'])
                );
            }
        }

        return $calendar;
    }

    /**
     * Realizar toggle de disponibilidad
     */
    private function performToggle(array $data): array
    {
        $targetDate = Carbon::parse($data['date'])->toDateString();
        $reason     = $data['reason'] ?? null;

        $currentAvailability = TourAvailability::where([
            'tour_id'     => $data['tour_id'],
            'schedule_id' => $data['schedule_id'],
            'date'        => $targetDate,
        ])->first();

        $currentIsBlocked = $currentAvailability?->is_blocked ?? false;

        $nextIsBlocked = match ($data['want'] ?? null) {
            'block'   => true,
            'unblock' => false,
            default   => !$currentIsBlocked,
        };

        $updatedAvailability = TourAvailability::updateOrCreate(
            [
                'tour_id'     => $data['tour_id'],
                'schedule_id' => $data['schedule_id'],
                'date'        => $targetDate,
            ],
            [
                'is_blocked'   => $nextIsBlocked,
                'max_capacity' => $nextIsBlocked ? null : ($currentAvailability?->max_capacity ?? null),
            ]
        );

        if ($nextIsBlocked === true) {
            TourExcludedDate::firstOrCreate(
                [
                    'tour_id'     => $data['tour_id'],
                    'schedule_id' => $data['schedule_id'],
                    'start_date'  => $targetDate,
                    'end_date'    => $targetDate,
                ],
                [
                    'reason' => $reason ?: 'Blocked',
                ]
            );
        } else {
            TourExcludedDate::where([
                'tour_id'     => $data['tour_id'],
                'schedule_id' => $data['schedule_id'],
            ])->whereDate('start_date', $targetDate)->delete();
        }

        return [
            'ok'           => true,
            'is_available' => !$nextIsBlocked,
            'label'        => $nextIsBlocked ? 'Blocked' : 'Available',
        ];
    }
}
