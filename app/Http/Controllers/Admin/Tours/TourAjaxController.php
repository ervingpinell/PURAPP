<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\{
    Tour,
    Itinerary,
    ItineraryItem,
    CustomerCategory,
    CustomerCategoryTranslation,
    TourLanguage,
    Amenity,
    Schedule
};
use App\Services\Contracts\TranslatorInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TourAjaxController extends Controller
{
    /**
     * Validar slug en tiempo real
     * GET /admin/tours/ajax/validate-slug?slug=aventura&tour_id=5
     */
    public function validateSlug(Request $request)
    {
        $slug = Str::slug((string)$request->slug);
        $tourId = $request->tour_id;

        $exists = Tour::where('slug', $slug)
            ->when($tourId, fn($q) => $q->where('tour_id', '!=', $tourId))
            ->exists();

        return response()->json([
            'available' => !$exists,
            'slug' => $slug,
            'message' => $exists
                ? __('m_tours.tour.validation.slug_taken')
                : __('m_tours.tour.validation.slug_available')
        ]);
    }

    /**
     * Crear nueva categoría de cliente inline
     * POST /admin/tours/ajax/create-category
     *
     * Campos esperados:
     * - slug (opcional, se genera del primer nombre si no viene)
     * - names[es] (requerido) + names[en|fr|pt|de] (opcionales)
     * - auto_translate=1 (opcional) para completar con DeepL los faltantes
     * - age_from (int requerido), age_to (int nullable), order (int, por defecto 999), is_active (bool)
     */
    public function createCategory(Request $request, TranslatorInterface $translator)
    {
        // Locales soportados
        $locales = function_exists('supported_locales') ? supported_locales() : ['es','en','fr','pt','de'];
        $primary = $locales[0] ?? 'es';

        // Reglas
        $rules = [
            'slug'        => ['nullable','alpha_dash','max:60','unique:customer_categories,slug'],
            'names'       => ['required','array'],
            "names.$primary" => ['required','string','max:120'],
            'age_from'    => ['required','integer','min:0','max:255'],
            'age_to'      => ['nullable','integer','min:0','max:255'],
            'order'       => ['nullable','integer','min:0','max:255'],
            'is_active'   => ['nullable','boolean'],
            'auto_translate' => ['nullable','boolean'],
        ];
        foreach ($locales as $loc) {
            if ($loc === $primary) continue;
            $rules["names.$loc"] = ['nullable','string','max:120'];
        }

        $data = $request->validate($rules);

        // Validación adicional age_to >= age_from (si viene)
        if (!is_null($data['age_to'] ?? null) && $data['age_to'] < $data['age_from']) {
            return response()->json([
                'ok' => false,
                'message' => __('customer_categories.validation.age_to_gte_age_from')
            ], 422);
        }

        // Asegurar SLUG: si no viene, generarlo del primer nombre y garantizar unicidad
        $slug = (string)($data['slug'] ?? '');
        if ($slug === '') {
            $base = (string)$data['names'][$primary];
            $slug = Str::slug($base) ?: Str::slug('category');
            $slug = $this->makeUniqueSlug($slug);
        }

        try {
            DB::beginTransaction();

            // Crear la categoría base
            $category = CustomerCategory::create([
                'slug'      => $slug,
                'age_from'  => (int)$data['age_from'],
                'age_to'    => array_key_exists('age_to', $data) ? $data['age_to'] : null,
                'order'     => (int)($data['order'] ?? 999),
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            // Preparar traducciones
            $names = $data['names'] ?? [];
            $auto  = (bool)($data['auto_translate'] ?? false);

            // Si auto_translate, rellenar faltantes con DeepL a partir del nombre primario
            if ($auto) {
                $seed = (string)$names[$primary];
                foreach ($locales as $loc) {
                    if (!isset($names[$loc]) || trim((string)$names[$loc]) === '') {
                        $names[$loc] = $loc === $primary
                            ? $seed
                            : $translator->translate($seed, $loc);
                    }
                }
            }

            // Guardar traducciones presentes (no vacías)
            foreach ($locales as $loc) {
                $val = trim((string)($names[$loc] ?? ''));
                if ($val === '') continue;

                CustomerCategoryTranslation::updateOrCreate(
                    ['category_id' => $category->category_id, 'locale' => $loc],
                    ['name' => $val]
                );
            }

            DB::commit();

            // Respuesta
            return response()->json([
                'ok' => true,
                'category' => [
                    'id'           => $category->category_id,
                    'slug'         => $category->slug,
                    'name'         => $category->getTranslatedName(), // <- traducido segun app()->getLocale()
                    'age_range'    => $category->age_range,
                    'age_from'     => $category->age_from,
                    'age_to'       => $category->age_to,
                    'order'        => $category->order,
                    'is_active'    => $category->is_active,
                ],
                'message' => __('m_tours.tour.ajax.category_created')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'ok' => false,
                'message' => __('m_tours.tour.ajax.category_error')
            ], 500);
        }
    }

    /**
     * Genera un slug único para customer_categories.slug
     */
    private function makeUniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 2;
        while (CustomerCategory::where('slug', $slug)->exists()) {
            $slug = Str::limit($base, 54, '') . '-' . $i; // 54 + '-' + max 5 dígitos < 60
            $i++;
        }
        return $slug;
    }

    /**
     * Crear nuevo idioma inline
     * POST /admin/tours/ajax/create-language
     */
    public function createLanguage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:tour_languages,name',
            'code' => 'required|string|max:5|unique:tour_languages,code',
        ]);

        try {
            $language = TourLanguage::create([
                'name' => $request->name,
                'code' => strtolower($request->code),
                'is_active' => true,
            ]);

            return response()->json([
                'ok' => true,
                'language' => [
                    'id' => $language->tour_language_id,
                    'name' => $language->name,
                    'code' => $language->code,
                ],
                'message' => __('m_tours.tour.ajax.language_created')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => __('m_tours.tour.ajax.language_error')
            ], 500);
        }
    }

    /**
     * Crear nueva amenidad inline
     * POST /admin/tours/ajax/create-amenity
     */
    public function createAmenity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name',
            'icon' => 'nullable|string|max:50',
        ]);

        try {
            $amenity = Amenity::create([
                'name' => $request->name,
                'icon' => $request->icon ?? 'fas fa-check',
                'is_active' => true,
            ]);

            return response()->json([
                'ok' => true,
                'amenity' => [
                    'id' => $amenity->amenity_id,
                    'name' => $amenity->name,
                    'icon' => $amenity->icon,
                ],
                'message' => __('m_tours.tour.ajax.amenity_created')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => __('m_tours.tour.ajax.amenity_error')
            ], 500);
        }
    }

    /**
     * Crear nuevo horario inline
     * POST /admin/tours/ajax/create-schedule
     */
    public function createSchedule(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'label' => 'nullable|string|max:100',
        ]);

        try {
            $schedule = Schedule::create([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'label' => $request->label,
                'is_active' => true,
            ]);

            return response()->json([
                'ok' => true,
                'schedule' => [
                    'id' => $schedule->schedule_id,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'label' => $schedule->label,
                    'formatted' => date('g:i A', strtotime($schedule->start_time)) .
                                  ' - ' .
                                  date('g:i A', strtotime($schedule->end_time)),
                ],
                'message' => __('m_tours.tour.ajax.schedule_created')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => __('m_tours.tour.ajax.schedule_error')
            ], 500);
        }
    }

    /**
     * Crear nuevo itinerario con items inline
     * POST /admin/tours/ajax/create-itinerary
     */
    public function createItinerary(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.title' => 'required_with:items|string|max:255',
            'items.*.description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $itinerary = Itinerary::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => true,
            ]);

            if ($request->filled('items')) {
                foreach ($request->items as $index => $itemData) {
                    $item = ItineraryItem::create([
                        'title' => $itemData['title'],
                        'description' => $itemData['description'] ?? null,
                        'is_active' => true,
                    ]);

                    DB::table('itinerary_item_itinerary')->insert([
                        'itinerary_id' => $itinerary->itinerary_id,
                        'itinerary_item_id' => $item->item_id,
                        'item_order' => $index,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            $itinerary->load('items');

            return response()->json([
                'ok' => true,
                'itinerary' => [
                    'id' => $itinerary->itinerary_id,
                    'name' => $itinerary->name,
                    'description' => $itinerary->description,
                    'items' => $itinerary->items->map(fn($item) => [
                        'title' => $item->title,
                        'description' => $item->description,
                    ]),
                ],
                'message' => __('m_tours.tour.ajax.itinerary_created')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'message' => __('m_tours.tour.ajax.itinerary_error')
            ], 500);
        }
    }

    /**
     * Previsualizar traducciones
     * POST /admin/tours/ajax/preview-translations
     */
    public function previewTranslations(Request $request, TranslatorInterface $translator)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        try {
            $translations = $translator->translateAll($request->text);

            return response()->json([
                'ok' => true,
                'translations' => $translations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => __('m_tours.tour.ajax.translation_error')
            ], 500);
        }
    }

    /**
     * Cargar datos de itinerario
     * GET /admin/tours/ajax/load-itinerary/{itinerary}
     */
    public function loadItinerary(Itinerary $itinerary)
    {
        $itinerary->load('items');

        return response()->json([
            'ok' => true,
            'itinerary' => [
                'id' => $itinerary->itinerary_id,
                'name' => $itinerary->name,
                'description' => $itinerary->description,
                'items' => $itinerary->items->map(fn($item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                ]),
            ]
        ]);
    }
}
