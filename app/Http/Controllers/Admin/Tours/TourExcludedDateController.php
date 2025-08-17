<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourAvailability;
use App\Models\TourExcludedDate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class TourExcludedDateController extends Controller
{
    /**
     * Vista “Disponibilidades” por día (AM/PM) con filtros.
     */
public function index(Request $request)
{
    $tz       = config('app.timezone', 'America/Costa_Rica');
    $today    = \Carbon\Carbon::today($tz);
    $todayStr = $today->toDateString();

    // 1) Limpieza de registros pasados para aligerar carga
    \App\Models\TourExcludedDate::whereDate('end_date', '<', $todayStr)
        ->orWhere(function($q) use($todayStr){
            $q->whereNull('end_date')->whereDate('start_date','<',$todayStr);
        })
        ->delete();

    // Opcional: limpiar disponibilidades pasadas
    \App\Models\TourAvailability::whereDate('date','<', $todayStr)->delete();

    // 2) Normalizar filtros
    $rawDate = $request->input('date', $todayStr);
    $start   = \Carbon\Carbon::parse($rawDate, $tz);
    if ($start->lt($today)) {
        $start = $today; // forzar hoy si viene una fecha pasada
    }
    $startDate = $start->toDateString();

    // clamp Days 1..30
    $days = (int) $request->input('days', 7);
    $days = max(1, min(30, $days));

    $q = trim((string)$request->input('q', ''));

    // 3) Tours (filtro por nombre si aplica)
    $tours = \App\Models\Tour::with('schedules')
        ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
        ->orderBy('name')
        ->get();

    // 4) Periodo inclusivo [start, start+days-1]
    $endDate = \Carbon\Carbon::parse($startDate, $tz)->addDays($days - 1)->toDateString();
    $period  = \Carbon\CarbonPeriod::create($startDate, $endDate);
    $dateList = collect($period)->map->toDateString();

    // 5) Cargar disponibilidades/bloqueos del rango
    $avail = \App\Models\TourAvailability::whereIn('date', $dateList)->get();
    $excls = \App\Models\TourExcludedDate::whereIn('start_date', $dateList)->get();

    // 6) Construir calendario
    $calendar = [];
    foreach ($dateList as $dateStr) {
        $calendar[$dateStr] = ['am' => [], 'pm' => []];

        foreach ($tours as $tour) {
            foreach ($tour->schedules as $schedule) {
                $time   = \Carbon\Carbon::parse($schedule->start_time, $tz);
                $bucket = ((int)$time->format('H') < 12) ? 'am' : 'pm';

                $a = $avail->first(fn($x) =>
                    $x->tour_id == $tour->tour_id &&
                    $x->schedule_id == $schedule->schedule_id &&
                    $x->date === $dateStr
                );

                $e = $excls->first(fn($x) =>
                    $x->tour_id == $tour->tour_id &&
                    $x->schedule_id == $schedule->schedule_id &&
                    \Carbon\Carbon::parse($x->start_date, $tz)->toDateString() === $dateStr
                );

                $isAvailable = $a !== null ? (bool)$a->is_available : ($e === null);

                $calendar[$dateStr][$bucket][] = [
                    'tour_id'     => $tour->tour_id,
                    'tour_name'   => $tour->name,
                    'schedule_id' => $schedule->schedule_id,
                    'time'        => $time->format('g:ia'),
                    'is_available'=> $isAvailable,
                    'date'        => $dateStr,
                ];
            }
        }

        foreach (['am','pm'] as $b) {
            usort($calendar[$dateStr][$b], fn($a,$b) => strnatcasecmp($a['tour_name'], $b['tour_name']));
        }
    }

    return view('admin.tours.excluded_dates.index', [
        'calendar' => $calendar,
        'date'     => $startDate,
        'days'     => $days,
        'q'        => $q,
    ]);
}
// App/Http/Controllers/Admin/Tours/TourExcludedDateController.php

public function blocked(Request $request)
{
    $startDate = $request->input('date', \Carbon\Carbon::today()->toDateString());
    $days      = (int)($request->input('days', 7));
    $q         = trim((string)$request->input('q', ''));

    // Construir calendario igual que en index()
    $tours = \App\Models\Tour::with('schedules')
        ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
        ->orderBy('name')
        ->get();

    $period   = \Carbon\CarbonPeriod::create($startDate, $days - 1);
    $dateList = collect($period)->map->toDateString();

    $avail = \App\Models\TourAvailability::whereIn('date', $dateList)->get();
    $excls = \App\Models\TourExcludedDate::whereIn('start_date', $dateList)->get();

    $calendar = [];
    foreach ($dateList as $dateStr) {
        $calendar[$dateStr] = ['am' => [], 'pm' => []];

        foreach ($tours as $tour) {
            foreach ($tour->schedules as $schedule) {
                $time   = \Carbon\Carbon::parse($schedule->start_time);
                $bucket = ((int)$time->format('H') < 12) ? 'am' : 'pm';

                $a = $avail->first(fn($x) =>
                    $x->tour_id == $tour->tour_id &&
                    $x->schedule_id == $schedule->schedule_id &&
                    $x->date === $dateStr
                );

                $e = $excls->first(fn($x) =>
                    $x->tour_id == $tour->tour_id &&
                    $x->schedule_id == $schedule->schedule_id &&
                    \Carbon\Carbon::parse($x->start_date)->toDateString() === $dateStr
                );

                $isAvailable = $a !== null ? (bool)$a->is_available : ($e === null);

                if ($isAvailable === false) { // solo bloqueados
                    $calendar[$dateStr][$bucket][] = [
                        'tour_id'     => $tour->tour_id,
                        'tour_name'   => $tour->name,
                        'schedule_id' => $schedule->schedule_id,
                        'time'        => $time->format('g:ia'),
                        'is_available'=> $isAvailable,
                        'date'        => $dateStr,
                    ];
                }
            }
        }

        foreach (['am','pm'] as $b) {
            usort($calendar[$dateStr][$b], fn($a,$b) => strnatcasecmp($a['tour_name'], $b['tour_name']));
        }
    }

    // Limpia días vacíos para que la vista quede compacta
    $calendar = array_filter($calendar, function($buckets){
        return count($buckets['am']) + count($buckets['pm']) > 0;
    });

    return view('admin.tours.excluded_dates.blocked', [
        'calendar' => $calendar,
        'date'     => $startDate,
        'days'     => $days,
        'q'        => $q,
    ]);
}


    /**
     * Compatibilidad: crear un registro puntual en excluded_dates.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tour_id'     => 'required|exists:tours,tour_id',
            'schedule_id' => 'nullable|exists:schedules,schedule_id',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'reason'      => 'nullable|string|max:255',
        ]);

        $exists = TourExcludedDate::where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->whereDate('start_date', $request->start_date)
            ->exists();

        if (!$exists) {
            TourExcludedDate::create($request->all());
        }

        return redirect()->back()->with('success', 'Fecha bloqueada creada correctamente.');
    }

    public function destroy($id)
    {
        $date = TourExcludedDate::findOrFail($id);
        $date->delete();

        return redirect()->back()->with('success', 'Fecha bloqueada eliminada.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tour_id'    => 'required|exists:tours,tour_id',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:255',
        ]);

        $excludedDate = TourExcludedDate::findOrFail($id);

        $excludedDate->update([
            'tour_id'    => $request->tour_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'reason'     => $request->reason,
        ]);

        return redirect()->route('admin.tours.excluded_dates.index')
            ->with('success', 'Fecha bloqueada actualizada correctamente.');
    }

    /**
     * Bloqueo total por rango (todos los tours/horarios).
     */
    public function blockAll(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date'   => 'nullable|date|after_or_equal:start_date',
                'reason'     => 'nullable|string',
            ]);

            $start  = $request->start_date;
            $end    = $request->end_date ?? $start;
            $reason = $request->reason ?? 'Bloqueo total';

            $tours  = Tour::with('schedules')->get();
            $period = CarbonPeriod::create($start, $end);

            foreach ($tours as $tour) {
                foreach ($tour->schedules as $schedule) {
                    foreach ($period as $date) {
                        $day = $date->format('Y-m-d');

                        TourAvailability::updateOrCreate([
                            'tour_id'     => $tour->tour_id,
                            'schedule_id' => $schedule->schedule_id,
                            'date'        => $day,
                        ], [
                            'is_available' => false,
                            'reason'       => $reason,
                        ]);

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

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Alternar / forzar estado para un tour+horario en una fecha.
     */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'tour_id'     => 'required|exists:tours,tour_id',
            'schedule_id' => 'required|exists:schedules,schedule_id',
            'date'        => 'required|date',
            'want'        => 'nullable|in:block,unblock',
            'reason'      => 'nullable|string|max:255',
        ]);

        $date   = Carbon::parse($data['date'])->toDateString();
        $reason = $data['reason'] ?? null;

        $current = TourAvailability::where([
            'tour_id'     => $data['tour_id'],
            'schedule_id' => $data['schedule_id'],
            'date'        => $date,
        ])->first();

        $isAvailable = $current?->is_available ?? true;

        $new = $data['want'] === 'block' ? false
             : ($data['want'] === 'unblock' ? true : !$isAvailable);

        $rec = TourAvailability::updateOrCreate(
            [
                'tour_id'     => $data['tour_id'],
                'schedule_id' => $data['schedule_id'],
                'date'        => $date,
            ],
            [
                'is_available' => $new,
                'reason'       => $new ? null : ($reason ?: 'Blocked'),
            ]
        );

        if ($new === false) {
            TourExcludedDate::firstOrCreate([
                'tour_id'     => $data['tour_id'],
                'schedule_id' => $data['schedule_id'],
                'start_date'  => $date,
                'end_date'    => $date,
            ], [
                'reason' => $rec->reason,
            ]);
        } else {
            TourExcludedDate::where([
                'tour_id'     => $data['tour_id'],
                'schedule_id' => $data['schedule_id'],
            ])->whereDate('start_date', $date)->delete();
        }

        return response()->json([
            'ok'           => true,
            'is_available' => $new,
            'label'        => $new ? 'Available' : 'Blocked',
        ]);
    }

    /**
     * Alternar en lote (o forzar con want=block|unblock).
     */
    public function bulkToggle(Request $request)
    {
        $validated = $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.tour_id'      => 'required|exists:tours,tour_id',
            'items.*.schedule_id'  => 'required|exists:schedules,schedule_id',
            'items.*.date'         => 'required|date',
            'want'                 => 'nullable|in:block,unblock',
            'reason'               => 'nullable|string|max:255',
        ]);

        $changed = 0;
        foreach ($validated['items'] as $it) {
            $sub = new Request(array_merge($it, [
                'want'   => $validated['want'] ?? null,
                'reason' => $validated['reason'] ?? null,
            ]));
            $res = $this->toggle($sub);
            if ($res->getStatusCode() === 200) $changed++;
        }

        return response()->json(['ok' => true, 'changed' => $changed]);
    }

    /**
     * Alias para /admin/tour-excluded/block-all (bloquear lote).
     */
    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.tour_id'     => 'required|exists:tours,tour_id',
            'items.*.schedule_id' => 'required|exists:schedules,schedule_id',
            'items.*.date'        => 'required|date',
            'reason'              => 'nullable|string|max:255',
        ]);

        $req = new Request([
            'items'  => $validated['items'],
            'want'   => 'block',
            'reason' => $validated['reason'] ?? 'Bloqueo múltiple',
        ]);

        return $this->bulkToggle($req);
    }

    public function destroyAll()
    {
        TourExcludedDate::truncate();
        return redirect()->back()->with('success', 'Todas las fechas bloqueadas han sido eliminadas.');
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No se proporcionaron fechas a eliminar.'], 400);
        }

        TourExcludedDate::whereIn('tour_excluded_date_id', $ids)->delete();

        return response()->json(['success' => 'Fechas eliminadas correctamente.']);
    }
}
