<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * ProductDataController
 *
 * Handles product data operations.
 */
class ProductDataController extends Controller
{
    public function schedules(Product $product)
    {
        $data = $product->schedules()
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->orderBy('start_time')
            ->get(['schedules.schedule_id', 'schedules.start_time', 'schedules.end_time'])
            ->map(fn($s) => [
                'schedule_id' => (int)$s->schedule_id,
                'start_time'  => (string)$s->start_time,
                'end_time'    => (string)$s->end_time,
            ]);

        return response()->json($data);
    }

    public function languages(Product $product)
    {
        $data = $product->languages()
            ->orderBy('name')
            ->get(['tour_languages.tour_language_id', 'tour_languages.name'])
            ->map(fn($l) => [
                'tour_language_id' => (int)$l->tour_language_id,
                'name'             => (string)$l->name,
            ]);

        return response()->json($data);
    }

    public function categories(Request $request, Product $product)
    {
        $locale = app()->getLocale();
        $serviceDate = $request->input('tour_date');

        // Base query
        $query = $product->prices()
            ->where('is_active', true)
            ->with(['category.translations'])
            ->orderBy('category_id');

        // Filter by date if provided
        if ($serviceDate) {
            $query->where(function ($q) use ($serviceDate) {
                // Prices valid for specific date OR default prices (null dates)
                $q->where(function ($sub) use ($serviceDate) {
                    $sub->whereNotNull('valid_from')
                        ->whereNotNull('valid_until')
                        ->whereDate('valid_from', '<=', $serviceDate)
                        ->whereDate('valid_until', '>=', $serviceDate);
                })->orWhere(function ($sub) {
                    $sub->whereNull('valid_from')
                        ->whereNull('valid_until');
                });
            });
        }

        $prices = $query->get();

        // If we have date-specific prices, we might have duplicates (default + specific).
        // We should prioritize specific dates over defaults.
        if ($serviceDate) {
            $prices = $prices->sortByDesc(function ($price) {
                return $price->valid_from ? 1 : 0; // Specific dates first
            })->unique('category_id'); // Keep only the first (most specific) for each category
        }

        $data = $prices->map(function ($p) use ($locale) {
            $cat = $p->category;

            // Nombre traducido
            $name = '';
            if ($cat) {
                if (method_exists($cat, 'getTranslatedName')) {
                    $name = $cat->getTranslatedName($locale);
                } else {
                    $translations = $cat->translations ?? collect();
                    $cands = array_unique([$locale, substr($locale, 0, 2), config('app.fallback_locale'), 'es']);
                    foreach ($cands as $lc) {
                        $t = $translations->firstWhere('locale', $lc) ?? $translations->firstWhere('language_code', $lc);
                        if ($t && !empty($t->name)) {
                            $name = $t->name;
                            break;
                        }
                    }
                    if (!$name) {
                        $name = $cat->name
                            ?? ($cat->slug ? \Illuminate\Support\Str::of($cat->slug)->replace(['_', '-'], ' ')->title() : '');
                    }
                }
            }

            $slug  = $cat->slug ?? null;
            // Use final_price if available (includes tax logic), otherwise raw price
            $finalPrice = method_exists($p, 'getFinalPriceAttribute') ? $p->final_price : $p->price;
            $rawPrice = (float)($p->price ?? 0);

            $min   = (int)($p->min_quantity ?? 0);
            $max   = (int)($p->max_quantity ?? 99);

            return [
                'id'           => (int)$p->category_id,
                'category_id'  => (int)$p->category_id,
                'slug'         => $slug,
                'name'         => (string)$name,
                'price'        => (float)$finalPrice, // Precio final con impuestos si aplica
                'raw_price'    => $rawPrice,          // Precio base sin impuestos (o como esté en DB)
                'price_usd'    => (float)$finalPrice, // compat
                'min'          => $min,
                'max'          => $max,
                'min_quantity' => $min,
                'max_quantity' => $max,
                'is_active'    => (bool)$p->is_active,
                'valid_from'   => $p->valid_from ? $p->valid_from->format('Y-m-d') : null,
                'valid_until'  => $p->valid_until ? $p->valid_until->format('Y-m-d') : null,

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
