<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Models\Tour;
use App\Models\TourExcludedDate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TourExcludedDateController extends Controller
{
    public function index()
    {
        $excludedDates = TourExcludedDate::with('tour')->get();
        $tours = Tour::all();
        return view('admin.tours.excluded_dates.index', compact('excludedDates', 'tours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
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

}
