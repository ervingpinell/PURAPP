<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\HotelList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\TourType;
use Illuminate\Support\Str;


class HomeController extends Controller
{
public function index()
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        // 1) Tipos de tour con traducciones -> meta para UI
        $typeMeta = TourType::active()
            ->with('translations')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($type) use ($locale, $fallback) {
                $tr = $type->translations->firstWhere('locale', $locale)
                    ?? $type->translations->firstWhere('locale', $fallback);

                $title    = $tr->name ?? $type->name;
                $duration = $tr->duration ?? $type->duration ?? '';
                $slug     = Str::slug($title, '_'); // p.ej. "full_day", "half_day"

                return [
                    $slug => [
                        'id'          => $type->tour_type_id,
                        'slug'        => $slug,
                        'title'       => $title,
                        'duration'    => $duration,
                        'description' => $tr->description ?? $type->description ?? '',
                    ],
                ];
            });

        // 2) Tours con traducciones y tipo
        $tours = Tour::with(['tourType.translations', 'itinerary.items', 'translations'])
            ->where('is_active', true)
            ->get()
            ->map(function ($tour) use ($locale, $fallback) {
                $tTr = $tour->translations->firstWhere('locale', $locale)
                    ?? $tour->translations->firstWhere('locale', $fallback);

                $tour->translated_name     = $tTr->name ?? $tour->name;
                $tour->translated_overview = $tTr->overview ?? $tour->overview;

                if ($tour->tourType) {
                    $ttTr  = $tour->tourType->translations->firstWhere('locale', $locale)
                           ?? $tour->tourType->translations->firstWhere('locale', $fallback);
                    $ttName = $ttTr->name ?? $tour->tourType->name;
                    $tour->tour_type_slug = Str::slug($ttName, '_');
                } else {
                    $tour->tour_type_slug = 'sin_categoria';
                }

                return $tour;
            });

        // 3) Agrupar por slug de tipo
        $toursByType = $tours
            ->sortBy('tour_type_slug', SORT_NATURAL | SORT_FLAG_CASE)
            ->groupBy(fn ($t) => $t->tour_type_slug);

        // 4) Carrusel Viator
        $viatorTours = Tour::whereNotNull('viator_code')
            ->inRandomOrder()
            ->limit(6)
            ->get(['tour_id', 'viator_code', 'name']);

        $carouselProductCodes = $viatorTours->map(fn ($t) => [
            'code' => $t->viator_code,
            'name' => $t->name,
            'id'   => $t->tour_id,
        ])->values();

        return view('public.home', compact('toursByType', 'typeMeta', 'carouselProductCodes'));
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

    // ✅ Mostrar formulario de contacto
    public function contact()
    {
        return view('public.contact');
    }

    // ✅ Enviar mensaje de contacto
    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
        ]);

        // Aquí podrías guardar en DB o enviar email. Por ahora solo feedback:
        return back()->with('success', 'Tu mensaje ha sido enviado con éxito. Pronto te contactaremos.');
    }
}
