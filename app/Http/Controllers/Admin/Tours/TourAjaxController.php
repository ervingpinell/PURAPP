<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\{
    Tour,
    Itinerary,
    ItineraryItem,
    CustomerCategory,
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
        $slug = Str::slug($request->slug);
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
     */
    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:customer_categories,name',
            'age_range' => 'nullable|string|max:50',
            'min_quantity' => 'required|integer|min:0',
            'max_quantity' => 'required|integer|min:0',
            'order' => 'nullable|integer|min:0',
        ]);

        try {
            $category = CustomerCategory::create([
                'name' => $request->name,
                'age_range' => $request->age_range,
                'min_quantity' => $request->min_quantity ?? 0,
                'max_quantity' => $request->max_quantity ?? 12,
                'order' => $request->order ?? 999,
                'is_active' => true,
            ]);

            return response()->json([
                'ok' => true,
                'category' => [
                    'id' => $category->category_id,
                    'name' => $category->name,
                    'age_range' => $category->age_range,
                    'min_quantity' => $category->min_quantity,
                    'max_quantity' => $category->max_quantity,
                ],
                'message' => __('m_tours.tour.ajax.category_created')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => __('m_tours.tour.ajax.category_error')
            ], 500);
        }
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
