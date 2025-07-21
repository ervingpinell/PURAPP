<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\HotelList;
class HomeController extends Controller
{
    /**
     * Muestra la página principal con los tours activos.
     *
     * @return \Illuminate\View\View
     */
public function index()
{
    $tours = Tour::with('tourType')
        ->where('is_active', true)
        ->get()
        ->groupBy(function ($tour) {
            return $tour->tourType->name ?? 'Sin categoría';
        });

    return view('public.home', compact('tours'));
}



public function showTour($id)
{
    $tour = Tour::with([
        'tourType',
        'languages',
        'amenities',
        'excludedAmenities',
        'schedules',
        'itinerary.items',
    ])->findOrFail($id);

    $hotels = HotelList::where('is_active', true)->orderBy('name')->get();

    return view('public.tour-show', compact('tour', 'hotels'));
}
}
