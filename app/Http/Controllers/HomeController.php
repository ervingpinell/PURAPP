<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\HotelList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\TourType;

class HomeController extends Controller
{
    public function index()
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        // 1) Tipos de tour con traducciones -> meta para UI
        $typeMeta = TourType::active()
            ->with('translations') // si no hay traducciones aún, la colección vendrá vacía
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($type) use ($locale, $fallback) {
                $tr = ($type->translations ?? collect())->firstWhere('locale', $locale)
                    ?: ($type->translations ?? collect())->firstWhere('locale', $fallback);

                return [
                    $type->tour_type_id => [
                        'id'          => $type->tour_type_id,
                        'title'       => $tr->name ?? $type->name,                 // fallback al original (ES)
                        'duration'    => $tr->duration ?? $type->duration ?? '',
                        'description' => $tr->description ?? $type->description ?? '',
                    ],
                ];
            });

        // 2) Tours con traducciones y tipo (con fallback al original)
        $tours = Tour::with(['tourType.translations', 'itinerary.items', 'translations'])
            ->where('is_active', true)
            ->get()
            ->map(function ($tour) use ($locale, $fallback) {
                $tTr = ($tour->translations ?? collect())->firstWhere('locale', $locale)
                    ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);

                $tour->translated_name     = $tTr->name ?? $tour->name;
                $tour->translated_overview = $tTr->overview ?? $tour->overview;

                $tour->tour_type_id_group  = optional($tour->tourType)->tour_type_id ?? 'sin_categoria';

                return $tour;
            });

        // 3) Agrupar por id de tipo
        $toursByType = $tours
            ->sortBy('tour_type_id_group', SORT_NATURAL | SORT_FLAG_CASE)
            ->groupBy(fn ($t) => $t->tour_type_id_group);

        // 4) Carrusel Viator — hoy usa nombre original (ES); mañana usará traducción si existe
        $viatorTours = Tour::with('translations')
            ->whereNotNull('viator_code')
            ->inRandomOrder()
            ->limit(6)
            ->get(['tour_id', 'viator_code', 'name']);

        $carouselProductCodes = $viatorTours->map(function ($t) use ($locale, $fallback) {
            $tr = ($t->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($t->translations ?? collect())->firstWhere('locale', $fallback);

            return [
                'id'   => $t->tour_id,
                'code' => $t->viator_code,
                'name' => $tr->name ?? $t->name ?? '',   // nunca null
            ];
        })->values();

        return view('public.home', compact('toursByType', 'typeMeta', 'carouselProductCodes'));
    }

    public function showTour($id)
{
    $locale   = app()->getLocale();
    $fallback = config('app.fallback_locale', 'es');

    $tour = Tour::with([
        'tourType.translations',
        'schedules',
        'languages',
        'itinerary.items.translations',
        'itinerary.translations',
        'amenities.translations',
        'excludedAmenities.translations',
        'translations',
    ])->findOrFail($id);

    // Traducciones del tour
    $t = ($tour->translations ?? collect())->firstWhere('locale', $locale)
       ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);

    $tour->translated_name     = $t->name     ?? $tour->name;
    $tour->translated_overview = $t->overview ?? $tour->overview;

    // Itinerario
    if ($tour->itinerary) {
        $it = ($tour->itinerary->translations ?? collect())->firstWhere('locale', $locale)
           ?: ($tour->itinerary->translations ?? collect())->firstWhere('locale', $fallback);

        $tour->itinerary->translated_name        = $it->name        ?? $tour->itinerary->name;
        $tour->itinerary->translated_description = $it->description ?? $tour->itinerary->description;

        foreach ($tour->itinerary->items as $item) {
            $itT = ($item->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($item->translations ?? collect())->firstWhere('locale', $fallback);
            $item->translated_title       = $itT->title       ?? $item->title;
            $item->translated_description = $itT->description ?? $item->description;
        }
    }

    // Amenidades
    foreach ($tour->amenities as $a) {
        $t = ($a->translations ?? collect())->firstWhere('locale', $locale)
           ?: ($a->translations ?? collect())->firstWhere('locale', $fallback);
        $a->translated_name = $t->name ?? $a->name;
    }

    foreach ($tour->excludedAmenities as $e) {
        $t = ($e->translations ?? collect())->firstWhere('locale', $locale)
           ?: ($e->translations ?? collect())->firstWhere('locale', $fallback);
        $e->translated_name = $t->name ?? $e->name;
    }

    $hotels = HotelList::orderBy('name')->get();

    // ✅ Definir variables esperadas por el include
    // Si tienes columnas en tours:
    $cancel = $tour->cancel_policy ?? null;
    $refund = $tour->refund_policy ?? null;


    return view('public.tour-show', compact('tour', 'hotels', 'cancel', 'refund'));
}

    public function contact()
    {
        return view('public.contact');
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
        ]);

        return back()->with('success', 'Tu mensaje ha sido enviado con éxito. Pronto te contactaremos.');
    }
}
