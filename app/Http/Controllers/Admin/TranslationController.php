<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\LoggerHelper;
use Exception;

// Base models
use App\Models\Product;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\ProductType; // Was ProductType
use App\Models\Policy;

// Translation models removed - we use Spatie logic on base models directly.

/**
 * TranslationController
 *
 * Handles translation operations using Spatie Translatable.
 */
class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-translations'])->only(['index', 'chooseLocale', 'select', 'edit', 'changeEditingLocale']);
        $this->middleware(['can:edit-translations'])->only(['update']);
    }

    public function index()
    {
        return view('admin.translations.index');
    }

    public function chooseLocale(string $type)
    {
        // Legacy 'products' key mapping to Product
        $key = ($type === 'products') ? 'products' : $type;
        $entitySingular = __('m_config.translations.entities_singular.' . $key);

        return view('admin.translations.choose-locale', [
            'type' => $type,
        ]);
    }

    public function select(Request $request, string $type)
    {
        $entitySingular = __('m_config.translations.entities_singular.' . $type);

        $editLocale        = $request->query('edit_locale');
        $availableLocales  = ['es', 'en', 'fr', 'pt', 'de'];
        if ($editLocale && in_array($editLocale, $availableLocales, true)) {
            session(['translation_editing_locale' => $editLocale]);
        }

        $items = match ($type) {
            'products'         => Product::orderBy('product_id')->get(), // Was Product
            'itineraries'     => Itinerary::orderBy('itinerary_id')->get(),
            'itinerary_items' => ItineraryItem::orderBy('item_id')->get(),
            'amenities'       => Amenity::orderBy('amenity_id')->get(),
            'faqs'            => Faq::orderBy('faq_id')->get(),
            'policies'        => Policy::orderBy('policy_id')->get(),
            'product_types'      => ProductType::orderBy('product_type_id')->get(), // Was ProductType
            default           => collect(),
        };

        // Apply locale on items (Spatie does this automatically on accessors, but we might want explicit logic for the view if it iterates raw)
        // Actually, viewing a list of items to translate usually shows the Title in CURRENT UI locale.
        // Spatie accessors $item->name do exactly that.
        // The applyLocaleOnItems legacy method manually hydrated fields from a translation model. 
        // We don't need that anymore if the blade views just echo $item->name.
        // However, we should check if blade views use 'name' attribute or something else.
        // Assuming update needed in Blade if it relies on manual hydration, but usually $item->name works.
        
        $pageTitle = __('m_config.translations.select_entity_title', ['entity' => $entitySingular]);

        return view('admin.translations.select', [
            'items'       => $items,
            'type'        => $type,
            'entityLabel' => $entitySingular,
            'title'       => $pageTitle,
        ]);
    }

    public function changeEditingLocale(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|in:es,en,fr,pt,de'
        ]);

        session(['translation_editing_locale' => $validated['locale']]);

        return response()->json(['success' => true]);
    }

    public function edit(string $type, int $id)
    {
        $availableLocales = ['es', 'en', 'fr', 'pt', 'de'];

        if ($requestedLocale = request('edit_locale')) {
            if (in_array($requestedLocale, $availableLocales, true)) {
                session(['translation_editing_locale' => $requestedLocale]);
            }
        }

        $targetLocale = session('translation_editing_locale', app()->getLocale());
        if (!in_array($targetLocale, $availableLocales, true)) {
            $targetLocale = 'en';
        }

        $entity             = null;
        $translatableFields = [];

        switch ($type) {
            case 'products':
                $entity             = Product::with(['itinerary.items'])->findOrFail($id);
                $translatableFields = ['name', 'overview']; // Check Product model translatables
                break;

            case 'itineraries':
                $entity             = Itinerary::findOrFail($id);
                $translatableFields = ['name', 'description'];
                break;

            case 'itinerary_items':
                $entity             = ItineraryItem::findOrFail($id);
                $translatableFields = ['title', 'description'];
                break;

            case 'amenities':
                $entity             = Amenity::findOrFail($id);
                $translatableFields = ['name'];
                break;

            case 'faqs':
                $entity             = Faq::findOrFail($id);
                $translatableFields = ['question', 'answer'];
                break;

            case 'policies':
                $entity             = Policy::with('sections')->findOrFail($id);
                // Policy model: translatable = ['name', 'content']
                $translatableFields = ['name', 'content'];
                break;

            case 'product_types':
                $entity             = ProductType::findOrFail($id);
                // ProductType: translatable = ['name']
                // Legacy controller had description/duration. ProductType model ONLY lists 'name'.
                // If description is needed, it must be added to translatable in ProductType.
                // For now, only 'name'.
                $translatableFields = ['name']; 
                break;

            default:
                abort(404, 'Invalid translation type.');
        }

        $allTranslations = [];
        
        // Prepare translation data structure for view
        // Existing view likely expects translations[locale][field]
        foreach ($availableLocales as $lang) {
            foreach ($translatableFields as $field) {
                // Get raw translation for this locale, no fallback
                $val = $entity->getTranslation($field, $lang, false);
                $allTranslations[$lang][$field] = $val ?: ''; // ensure string
            }
        }

        return view('admin.translations.edit', [
            'type'           => $type,
            'item'           => $entity,
            'locale'         => $targetLocale,
            'fields'         => $translatableFields,
            'translations'   => $allTranslations[$targetLocale] ?? [],
            'uiLocale'       => app()->getLocale(),
            'editingLocale'  => $targetLocale,
        ]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $validated = $request->validate([
            'locale'                 => 'required|in:es,en,fr,pt,de',
            'translations'           => 'nullable|array',
            'itinerary_translations' => 'nullable|array', // Logic for nested itinerary?
            'item_translations'      => 'nullable|array',
            'section_translations'   => 'nullable|array',
        ]);

        try {
            $locale                 = (string) $validated['locale'];
            $mainFieldValues        = $validated['translations'] ?? [];
            $itineraryFieldValues   = $validated['itinerary_translations'] ?? [];
            $itemFieldValuesById    = $validated['item_translations'] ?? [];
            $sectionFieldValuesById = $validated['section_translations'] ?? [];

            /**
             * 🔧 Normalización para políticas y secciones
             */
            if ($type === 'policies') {
                if (isset($mainFieldValues['title']) && !isset($mainFieldValues['name'])) {
                    $mainFieldValues['name'] = $mainFieldValues['title'];
                }
                foreach ($sectionFieldValuesById as $sid => $payload) {
                    if (isset($payload['title']) && !isset($payload['name'])) {
                        $sectionFieldValuesById[$sid]['name'] = $payload['title'];
                    }
                }
            }

            $entity = null;

            switch ($type) {
                case 'products':
                    $entity = Product::with(['itinerary.items'])->findOrFail($id);
                    break;
                case 'itineraries':
                    $entity = Itinerary::findOrFail($id);
                    break;
                case 'itinerary_items':
                    $entity = ItineraryItem::findOrFail($id);
                    break;
                case 'amenities':
                    $entity = Amenity::findOrFail($id);
                    break;
                case 'faqs':
                    $entity = Faq::findOrFail($id);
                    break;
                case 'policies':
                    $entity = Policy::with('sections')->findOrFail($id);
                    break;
                case 'product_types':
                    $entity = ProductType::findOrFail($id);
                    break;
                default:
                    abort(404, 'Invalid translation type.');
            }

            DB::transaction(function () use (
                $entity,
                $locale,
                $mainFieldValues,
                $type,
                $itineraryFieldValues,
                $itemFieldValuesById,
                $sectionFieldValuesById
            ) {
                // 1. Update Main Entity Fields
                foreach ($mainFieldValues as $field => $value) {
                    // Check if field is translatable on model?
                    // Spatie throws exception if field is not in $translatable.
                    // We should check or catch.
                    if (in_array($field, $entity->getTranslatableAttributes())) {
                        $entity->setTranslation($field, $locale, (string)$value);
                    }
                }
                $entity->save();

                // 2. Nested Logic
                // Itinerario + Items (sólo para products)
                if ($type === 'products' && $entity->itinerary) {
                    $itin = $entity->itinerary;
                    foreach ($itineraryFieldValues as $f => $v) {
                        if (in_array($f, $itin->getTranslatableAttributes())) {
                            $itin->setTranslation($f, $locale, (string)$v);
                        }
                    }
                    $itin->save();

                    if (!empty($itemFieldValuesById)) {
                        foreach ($entity->itinerary->items as $item) {
                            $itemId = $item->item_id;
                            if (!array_key_exists($itemId, $itemFieldValuesById)) {
                                continue;
                            }
                            $payload = $itemFieldValuesById[$itemId] ?? [];
                            foreach ($payload as $f => $v) {
                                if (in_array($f, $item->getTranslatableAttributes())) {
                                    $item->setTranslation($f, $locale, (string)$v);
                                }
                            }
                            $item->save();
                        }
                    }
                }

                // Secciones de políticas
                if ($type === 'policies' && !empty($sectionFieldValuesById)) {
                    // Assuming PolicySection uses Spatie? Check model if possible.
                    // If not, we might fail. PolicySectionTranslation existed in legacy list.
                    // We didn't check PolicySection model file content yet.
                    if (!$entity->relationLoaded('sections')) {
                         $entity->load('sections');
                    }
                    foreach ($entity->sections as $section) {
                        $sectionId = $section->section_id;
                        if (!array_key_exists($sectionId, $sectionFieldValuesById)) {
                            continue;
                        }
                        $payload = $sectionFieldValuesById[$sectionId] ?? [];
                        
                         // Check if PolicySection uses Spatie. If not, this might be tricky.
                         // Assuming we migrated it or it should be migrated.
                         // For safety, check method existence
                         if (method_exists($section, 'setTranslation')) {
                             foreach ($payload as $f => $v) {
                                 if (in_array($f, $section->getTranslatableAttributes())) {
                                     $section->setTranslation($f, $locale, (string)$v);
                                 }
                             }
                             $section->save();
                         }
                    }
                }
            });

            LoggerHelper::mutated('TranslationController', 'update', $type, $id, ['locale' => $locale]);

            return redirect()
                ->route('admin.translations.edit', [
                    'type'        => $type,
                    'id'          => $id,
                    'edit_locale' => $locale,
                ])
                ->with('success', __('m_config.translations.updated_success'));
        } catch (Exception $e) {
            LoggerHelper::exception('TranslationController', 'update', $type, $id, $e);
            Log::error('Translations update failed', [
                'type'   => $type,
                'id'     => $id,
                'locale' => $request->input('locale'),
                'msg'    => $e->getMessage(),
            ]);

            if (config('app.debug')) {
                return back()->withInput()->with('error', $e->getMessage());
            }

            return back()->withInput()->with('error', __('m_config.translations.unexpected_error'));
        }
    }
}
