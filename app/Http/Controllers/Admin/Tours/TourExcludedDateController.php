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
        $excludedDates = TourExcludedDate::with(['tour', 'schedule'])->get();
        $tours = Tour::with('schedules')->get();

        // Agrupar tours por hora
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

        TourExcludedDate::create($request->all());

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

        $excludedDate = \App\Models\TourExcludedDate::findOrFail($id);

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
        $period = \Carbon\CarbonPeriod::create($start, $end);

        foreach ($tours as $tour) {
            foreach ($tour->schedules as $schedule) {
                foreach ($period as $date) {
                    TourAvailability::updateOrCreate([
                        'tour_id' => $tour->tour_id,
                        'schedule_id' => $schedule->schedule_id,
                        'date' => $date->format('Y-m-d'),
                    ], [
                        'is_available' => false,
                        'reason' => $reason,
                    ]);

                    TourExcludedDate::create([
                        'tour_id' => $tour->tour_id,
                        'schedule_id' => $schedule->schedule_id,
                        'start_date' => $date->format('Y-m-d'),
                        'end_date' => $date->format('Y-m-d'),
                        'reason' => $reason,
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        // 🔴 Esto enviará el error real al frontend
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(), // opcional para debugging avanzado
        ], 500);
    }
}


    public function destroyAll()
    {
        \App\Models\TourExcludedDate::truncate(); // Elimina todos los registros
        return redirect()->back()->with('success', 'Todas las fechas bloqueadas han sido eliminadas.');
    }
    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No se proporcionaron fechas a eliminar.'], 400);
        }

        \App\Models\TourExcludedDate::whereIn('tour_excluded_date_id', $ids)->delete();

        return response()->json(['success' => 'Fechas eliminadas correctamente.']);
    }



}
