<?php

namespace App\Services\Bookings;

use App\Models\Tour;
use Illuminate\Support\Collection;

class BookingValidationService
{
    /**
     * Valida cantidades según categorías activas del tour
     * $quantities = ['category_id' => quantity, ...]
     */
    public function validateQuantities(Tour $tour, array $quantities, bool $skipGlobalLimit = false): array
    {
        $errors   = [];
        $totalPax = array_sum(array_map('intval', $quantities));

        // Límite global
        $maxGlobal = (int) config('booking.max_persons_per_booking', 12);
        if (!$skipGlobalLimit && $totalPax > $maxGlobal) {
            $errors[] = __('m_bookings.validation.max_persons_exceeded', ['max' => $maxGlobal]);
        }
        if ($totalPax < 1) {
            $errors[] = __('m_bookings.validation.min_one_person_required');
        }

        // Categorías activas con precio (ya con nombre traducido resuelto)
        $active = $this->getActiveCategoriesForTour($tour);
        if ($active->isEmpty()) {
            $errors[] = __('m_bookings.validation.no_active_categories');
            return ['valid' => false, 'errors' => $errors, 'limits' => []];
        }

        // Por categoría
        foreach ($quantities as $categoryId => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) continue;

            $cat = $active->firstWhere('category_id', (int)$categoryId);
            if (!$cat) {
                $errors[] = __('m_bookings.validation.category_not_available', ['category_id' => $categoryId]);
                continue;
            }
            if ($qty < $cat->min_quantity) {
                $errors[] = __('m_bookings.validation.min_category_not_met', [
                    'category' => $cat->name,
                    'min'      => $cat->min_quantity
                ]);
            }
            if (!$skipGlobalLimit && $qty > $cat->max_quantity) {
                $errors[] = __('m_bookings.validation.max_category_exceeded', [
                    'category' => $cat->name,
                    'max'      => $cat->max_quantity
                ]);
            }
        }

        // Mínimo de adultos (si aplica)
        $minAdults = (int) config('booking.min_adults_per_booking', 0);
        if ($minAdults > 0) {
            $adult = $active->firstWhere('slug', 'adult');
            if ($adult) {
                $adultQty = (int) ($quantities[$adult->category_id] ?? 0);
                if ($adultQty < $minAdults) {
                    $errors[] = __('m_bookings.validation.min_adults_required', ['min' => $minAdults]);
                }
            }
        }

        // Máximo de niños (si aplica)
        $maxKids = PHP_INT_MAX;
        if ($maxKids < PHP_INT_MAX) {
            $kid = $active->firstWhere('slug', 'kid');
            if ($kid) {
                $kidQty = (int) ($quantities[$kid->category_id] ?? 0);
                if ($kidQty > $maxKids) {
                    $errors[] = __('m_bookings.validation.max_kids_exceeded', ['max' => $maxKids]);
                }
            }
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
            'limits' => [
                'max_global' => $maxGlobal,
                'categories' => $active->map(fn($cat) => [
                    'category_id' => $cat->category_id,
                    'name'        => $cat->name,          // <- YA RESUELTO
                    'slug'        => $cat->slug,
                    'min'         => $cat->min_quantity,
                    'max'         => $cat->max_quantity,
                    'price'       => $cat->price,
                ])->toArray(),
            ],
        ];
    }

    /**
     * Categorías activas (con precio) del tour.
     * Devuelve una colección de stdClass con:
     * category_id, name (traducido/resuelto), slug, min_quantity, max_quantity, price
     */
    protected function getActiveCategoriesForTour(Tour $tour): Collection
    {
        return $tour->prices()
            ->where('tour_prices.is_active', true)
            ->whereHas('category', fn($q) => $q->where('is_active', true))
            ->with('category')
            ->orderBy('category_id')
            ->get()
            ->map(function ($price) {
                $catModel = $price->category;

                // Resolver nombre traducido con fallbacks
                $resolvedName = $this->resolveCategoryLabel($catModel);

                // Slug seguro
                $slug = $catModel->slug ?? (isset($catModel->name) ? \Str::slug($catModel->name) : null);
                $slug = $slug ?: 'category-' . $catModel->category_id;

                return (object) [
                    'category_id'  => (int) $price->category_id,
                    'name'         => $resolvedName,                       // <- nombre ya final
                    'slug'         => $slug,
                    'min_quantity' => (int) $price->min_quantity,
                    'max_quantity' => (int) $price->max_quantity,
                    'price'        => (float) $price->price,
                ];
            });
    }

    /**
     * Límits para frontend
     */
    public function getLimitsForTour(Tour $tour): array
    {
        $maxGlobal = (int) config('booking.max_persons_per_booking', 12);
        $active    = $this->getActiveCategoriesForTour($tour);

        return [
            'max_persons_total' => $maxGlobal,
            'categories'        => $active->map(fn($cat) => [
                'category_id' => $cat->category_id,
                'name'        => $cat->name,        // <- YA RESUELTO (no llamar métodos aquí)
                'slug'        => $cat->slug,
                'min'         => $cat->min_quantity,
                'max'         => $cat->max_quantity,
                'price'       => $cat->price,
            ])->toArray(),
        ];
    }

    /**
     * Resolver etiqueta traducida de la categoría con fallbacks:
     * 1) Si el modelo tiene getTranslatedName($locale), úsalo.
     * 2) Si hay slug, intenta con claves de lang:
     *    - 'customer_categories.labels.{slug}'
     *    - 'm_tours.customer_categories.labels.{slug}'
     * 3) Fallback al nombre crudo.
     */
    protected function resolveCategoryLabel($categoryModel): string
    {
        $locale = app()->getLocale();

        // 1) Método del modelo (si existe)
        if (is_object($categoryModel) && method_exists($categoryModel, 'getTranslatedName')) {
            $name = $categoryModel->getTranslatedName($locale);
            if (!empty($name)) return $name;
        }

        // 2) Claves de lang por slug
        $slug = $categoryModel->slug ?? (isset($categoryModel->name) ? \Str::slug($categoryModel->name) : null);
        if (!empty($slug)) {
            foreach (
                [
                    "customer_categories.labels.$slug",
                    "m_tours.customer_categories.labels.$slug",
                ] as $key
            ) {
                $tr = __($key);
                if ($tr !== $key) return $tr;
            }
        }

        // 3) Fallback: nombre crudo
        return (string) ($categoryModel->name ?? ucfirst($slug ?? 'Category'));
    }
}
