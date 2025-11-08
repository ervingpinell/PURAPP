<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Tour;

class TourDataController extends Controller
{
    public function schedules(Tour $tour)
    {
        $data = $tour->schedules()
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->orderBy('start_time')
            ->get(['schedules.schedule_id','schedules.start_time','schedules.end_time'])
            ->map(fn($s)=>[
                'schedule_id' => (int)$s->schedule_id,
                'start_time'  => (string)$s->start_time,
                'end_time'    => (string)$s->end_time,
            ]);

        return response()->json($data);
    }

    public function languages(Tour $tour)
    {
        $data = $tour->languages()
            ->orderBy('name')
            ->get(['tour_languages.tour_language_id','tour_languages.name'])
            ->map(fn($l)=>[
                'tour_language_id' => (int)$l->tour_language_id,
                'name'             => (string)$l->name,
            ]);

        return response()->json($data);
    }

    public function categories(Tour $tour)
    {
        $locale = app()->getLocale();

        // Cargamos la categoría con TODAS sus traducciones (no existe 'translation' singular)
        $prices = $tour->prices()
            ->where('is_active', true)
            ->with(['category.translations']) // si tu relación se llama distinto, cámbiala a 'customerCategory.translations'
            ->orderBy('category_id')
            ->get(['category_id','price','min_quantity','max_quantity','is_active']);

        $data = $prices->map(function ($p) use ($locale) {
            // Ajusta a $p->customerCategory si tu relación en TourPrice no se llama 'category'
            $cat = $p->category;

            // Nombre traducido usando tu helper del modelo
            $name = '';
            if ($cat) {
                if (method_exists($cat, 'getTranslatedName')) {
                    $name = $cat->getTranslatedName($locale);
                } else {
                    // Fallback manual si no existiera el helper
                    $translations = $cat->translations ?? collect();
                    $cands = array_unique([$locale, substr($locale, 0, 2), config('app.fallback_locale'), 'es']);
                    foreach ($cands as $lc) {
                        $t = $translations->firstWhere('locale', $lc) ?? $translations->firstWhere('language_code', $lc);
                        if ($t && !empty($t->name)) { $name = $t->name; break; }
                    }
                    if (!$name) {
                        $name = $cat->name
                            ?? ($cat->slug ? \Illuminate\Support\Str::of($cat->slug)->replace(['_','-'], ' ')->title() : '');
                    }
                }
            }

            $slug  = $cat->slug ?? null;
            $price = (float)($p->price ?? 0);
            $min   = (int)($p->min_quantity ?? 0);
            $max   = (int)($p->max_quantity ?? 99);

            return [
                // Claves que tu JS espera
                'id'           => (int)$p->category_id,
                'category_id'  => (int)$p->category_id,
                'slug'         => $slug,
                'name'         => (string)$name,
                'price'        => $price,
                'price_usd'    => $price, // compat
                'min'          => $min,
                'max'          => $max,
                'min_quantity' => $min,   // compat
                'max_quantity' => $max,   // compat
                'is_active'    => (bool)$p->is_active,

                // Soporte a mapCategory() (translation + translations)
                'translation'  => [
                    'locale' => $locale,
                    'name'   => $name,
                ],
                'translations' => ($cat && $cat->relationLoaded('translations'))
                    ? $cat->translations->map(fn($tr) => [
                        'locale' => $tr->locale ?? $tr->language_code,
                        'name'   => $tr->name,
                    ])->values()->all()
                    : [],
            ];
        })->values();

        return response()->json($data);
    }
}
