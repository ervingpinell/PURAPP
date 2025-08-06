<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Models\Tour;
use App\Models\TourExcludedDate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TourAvailability;
use Carbon\CarbonPeriod;

class TourExcludedDateController extends Controller
{
    public function index(Request $request)
    {
        $filterStart = $request->input('filter_start_date');
        $filterEnd = $request->input('filter_end_date');
        $filterTour = $request->input('filter_tour_id');

        $excludedDates = TourExcludedDate::with(['tour', 'schedule'])
            ->when($filterStart, fn($q) => $q->where('start_date', '>=', $filterStart))
            ->when($filterEnd, fn($q) => $q->where('end_date', '<=', $filterEnd))
            ->when($filterTour, fn($q) => $q->where('tour_id', $filterTour))
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        $tours = Tour::with('schedules')->get();

        $groupedTours = collect();
        foreach ($tours as $tour) {
            foreach ($tour->schedules as $schedule) {
                $groupedTours->push([
                    'hora' => $schedule->start_time,
                    'name' => $tour->name,
                    'tour_id' => $tour->tour_id,
                    'schedule_id' => $schedule->schedule_id,
                ]);
            }
        }

        $groupedTours = $groupedTours->sortBy('hora')->groupBy('hora');

        return view('admin.tours.excluded_dates.index', compact(
            'excludedDates',
            'tours',
            'groupedTours'
        ));
    }



    public function store(Request $request)
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'schedule_id' => 'nullable|exists:schedules,schedule_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);

        // Prevenir duplicados
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

    public function blockAll(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'reason' => 'nullable|string',
            ]);

            $start = $request->start_date;
            $end = $request->end_date ?? $start;
            $reason = $request->reason ?? 'Bloqueo total';

            $tours = Tour::with('schedules')->get();
            $period = CarbonPeriod::create($start, $end);

            foreach ($tours as $tour) {
                foreach ($tour->schedules as $schedule) {
                    foreach ($period as $date) {
                        $formattedDate = $date->format('Y-m-d');

                        // Actualizar disponibilidad
                        TourAvailability::updateOrCreate([
                            'tour_id' => $tour->tour_id,
                            'schedule_id' => $schedule->schedule_id,
                            'date' => $formattedDate,
                        ], [
                            'is_available' => false,
                            'reason' => $reason,
                        ]);

                        // Prevenir duplicado en fecha bloqueada
                        $alreadyExists = TourExcludedDate::where('tour_id', $tour->tour_id)
                            ->where('schedule_id', $schedule->schedule_id)
                            ->whereDate('start_date', $formattedDate)
                            ->exists();

                        if (!$alreadyExists) {
                            TourExcludedDate::create([
                                'tour_id' => $tour->tour_id,
                                'schedule_id' => $schedule->schedule_id,
                                'start_date' => $formattedDate,
                                'end_date' => $formattedDate,
                                'reason' => $reason,
                            ]);
                        }
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
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
