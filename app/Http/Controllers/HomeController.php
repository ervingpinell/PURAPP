<?php

namespace App\Http\Controllers;

use App\Models\Tour;

class HomeController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();

        $tours = Tour::with(['tourType', 'itinerary.items', 'translations'])
            ->where('is_active', true)
            ->get()
            ->map(function ($tour) use ($locale) {
                $translation = $tour->translations->firstWhere('locale', $locale);

                $tour->translated_name = $translation->name ?? $tour->name;
                $tour->translated_overview = $translation->overview ?? $tour->overview;

                return $tour;
            })
            ->groupBy(fn($tour) => $tour->tourType->name ?? 'Sin categoría');

        return view('public.home', compact('tours'));
    }

    public function showTour($id)
    {
        $locale = app()->getLocale();

        $tour = Tour::with([
            'tourType',
            'schedules',
            'languages',
            'itinerary.items',
            'amenities',
            'excludedAmenities',
            'translations',
        ])->findOrFail($id);

        // 🧠 Traducciones desde DB o fallback
        $t = $tour->translations->firstWhere('locale', $locale);
        $tour->translated_name = $t->name ?? $tour->name;
        $tour->translated_overview = $t->overview ?? $tour->overview;

        // Itinerario
        if ($tour->itinerary) {
            $it = $tour->itinerary->translations->firstWhere('locale', $locale);
            $tour->itinerary->translated_name = $it->name ?? $tour->itinerary->name;
            $tour->itinerary->translated_description = $it->description ?? $tour->itinerary->description;

            foreach ($tour->itinerary->items as $item) {
                $itT = $item->translations->firstWhere('locale', $locale);
                $item->translated_title = $itT->title ?? $item->title;
                $item->translated_description = $itT->description ?? $item->description;
            }
        }

        // Amenidades
        foreach ($tour->amenities as $a) {
            $t = $a->translations->firstWhere('locale', $locale);
            $a->translated_name = $t->name ?? $a->name;
        }

        foreach ($tour->excludedAmenities as $e) {
            $t = $e->translations->firstWhere('locale', $locale);
            $e->translated_name = $t->name ?? $e->name;
        }

        $hotels = \App\Models\HotelList::orderBy('name')->get();

        return view('public.tour-show', compact('tour', 'hotels'));
    }
}
