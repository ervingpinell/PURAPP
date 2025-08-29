<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

// Base models
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\TourType;
use App\Models\Policy;

// Translation models
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;
use App\Models\TourTypeTranslation;
use App\Models\PolicyTranslation;
use App\Models\PolicySectionTranslation;

class TranslationController extends Controller
{
    /** Página de inicio del módulo de traducciones */
    public function index()
    {
        return view('admin.translations.index');
    }

    /** Listado para elegir la entidad a traducir */
    public function select(string $type)
    {
        // Usamos la clave singular traducida; si no existe, devolvemos 404
        $entitySingular = __('m_config.translations.entities_singular.' . $type);
        if ($entitySingular === 'm_config.translations.entities_singular.' . $type) {
            abort(404, 'Invalid translation type.');
        }

        $items = match ($type) {
            'tours'           => Tour::orderBy('tour_id')->get(),
            'itineraries'     => Itinerary::orderBy('itinerary_id')->get(),
            'itinerary_items' => ItineraryItem::orderBy('item_id')->get(),
            'amenities'       => Amenity::orderBy('amenity_id')->get(),
            'faqs'            => Faq::orderBy('faq_id')->get(),
            'policies'        => Policy::orderBy('policy_id')->get(),
            'tour_types'      => TourType::orderBy('tour_type_id')->get(),
            default           => collect(),
        };

        $pageTitle = __('m_config.translations.select_entity_title', ['entity' => $entitySingular]);

        return view('admin.translations.select', [
            'items'       => $items,
            'type'        => $type,
            'entityLabel' => $entitySingular,
            'title'       => $pageTitle,
        ]);
    }

    /** Selector de idioma para la entidad elegida */
    public function selectLocale(string $type, int $id)
    {
        $entity = match ($type) {
            'tours'           => Tour::findOrFail($id),
            'itineraries'     => Itinerary::findOrFail($id),
            'itinerary_items' => ItineraryItem::findOrFail($id),
            'amenities'       => Amenity::findOrFail($id),
            'faqs'            => Faq::findOrFail($id),
            'policies'        => Policy::findOrFail($id),
            'tour_types'      => TourType::findOrFail($id),
            default           => abort(404, 'Invalid translation type.'),
        };

        return view('admin.translations.choose-locale', [
            'type' => $type,
            'item' => $entity,
        ]);
    }

    /** Formulario de edición de traducciones por entidad e idioma */
    public function edit(string $type, int $id)
    {
        $availableLocales = ['es', 'en', 'fr', 'pt', 'de'];
        $localeParam      = (string) request('locale', 'en');
        $currentLocale    = in_array($localeParam, $availableLocales, true) ? $localeParam : 'en';

        $entity            = null;
        $translationModel  = null;
        $foreignKey        = '';
        $translatableFields = [];
        $allTranslations   = [];

        switch ($type) {
            case 'tours':
                $entity           = Tour::with(['itinerary.items'])->findOrFail($id);
                $translationModel = TourTranslation::class;
                $foreignKey       = 'tour_id';
                $translatableFields = ['name', 'overview'];
                break;

            case 'itineraries':
                $entity           = Itinerary::findOrFail($id);
                $translationModel = ItineraryTranslation::class;
                $foreignKey       = 'itinerary_id';
                $translatableFields = ['name', 'description'];
                break;

            case 'itinerary_items':
                $entity           = ItineraryItem::findOrFail($id);
                $translationModel = ItineraryItemTranslation::class;
                $foreignKey       = 'item_id';
                $translatableFields = ['title', 'description'];
                break;

            case 'amenities':
                $entity           = Amenity::findOrFail($id);
                $translationModel = AmenityTranslation::class;
                $foreignKey       = 'amenity_id';
                $translatableFields = ['name'];
                break;

            case 'faqs':
                $entity           = Faq::findOrFail($id);
                $translationModel = FaqTranslation::class;
                $foreignKey       = 'faq_id';
                $translatableFields = ['question', 'answer'];
                break;

            case 'policies':
                $entity           = Policy::findOrFail($id);
                $translationModel = PolicyTranslation::class;
                $foreignKey       = 'policy_id';
                $translatableFields = ['name', 'content']; // importante: 'name' (no 'title')
                break;

            case 'tour_types':
                $entity           = TourType::findOrFail($id);
                $translationModel = TourTypeTranslation::class;
                $foreignKey       = 'tour_type_id';
                $translatableFields = ['name', 'description', 'duration'];
                break;

            default:
                abort(404, 'Invalid translation type.');
        }

        foreach ($availableLocales as $lang) {
            $existing = $translationModel::where($foreignKey, $entity->getKey())
                ->where('locale', $lang)
                ->first();

            foreach ($translatableFields as $field) {
                $allTranslations[$lang][$field] = $existing ? ($existing->{$field} ?? '') : '';
            }
        }

        return view('admin.translations.edit', [
            'type'         => $type,
            'item'         => $entity,
            'locale'       => $currentLocale,
            'fields'       => $translatableFields,
            'translations' => $allTranslations[$currentLocale] ?? [],
        ]);
    }

    /** Persistencia de traducciones (entidad principal + opcionales) */
    public function update(Request $request, string $type, int $id)
    {
        $validated = $request->validate([
            'locale'                 => 'required|in:es,en,fr,pt,de',
            'translations'           => 'nullable|array',
            'itinerary_translations' => 'nullable|array',
            'item_translations'      => 'nullable|array',
            'section_translations'   => 'nullable|array', // para policies.sections si corresponde
        ]);

        try {
            $locale                 = (string) $validated['locale'];
            $mainFieldValues        = $validated['translations'] ?? [];
            $itineraryFieldValues   = $validated['itinerary_translations'] ?? [];
            $itemFieldValuesById    = $validated['item_translations'] ?? [];
            $sectionFieldValuesById = $validated['section_translations'] ?? [];

            $entity            = null;
            $translationModel  = null;
            $foreignKey        = '';
            $translatableFields = [];

            switch ($type) {
                case 'tours':
                    $entity           = Tour::with(['itinerary.items'])->findOrFail($id);
                    $translationModel = TourTranslation::class;
                    $foreignKey       = 'tour_id';
                    $translatableFields = ['name', 'overview'];
                    break;

                case 'itineraries':
                    $entity           = Itinerary::findOrFail($id);
                    $translationModel = ItineraryTranslation::class;
                    $foreignKey       = 'itinerary_id';
                    $translatableFields = ['name', 'description'];
                    break;

                case 'itinerary_items':
                    $entity           = ItineraryItem::findOrFail($id);
                    $translationModel = ItineraryItemTranslation::class;
                    $foreignKey       = 'item_id';
                    $translatableFields = ['title', 'description'];
                    break;

                case 'amenities':
                    $entity           = Amenity::findOrFail($id);
                    $translationModel = AmenityTranslation::class;
                    $foreignKey       = 'amenity_id';
                    $translatableFields = ['name'];
                    break;

                case 'faqs':
                    $entity           = Faq::findOrFail($id);
                    $translationModel = FaqTranslation::class;
                    $foreignKey       = 'faq_id';
                    $translatableFields = ['question', 'answer'];
                    break;

                case 'policies':
                    $entity           = Policy::with('sections')->findOrFail($id);
                    $translationModel = PolicyTranslation::class;
                    $foreignKey       = 'policy_id';
                    $translatableFields = ['name', 'content']; // importante: 'name'
                    break;

                case 'tour_types':
                    $entity           = TourType::findOrFail($id);
                    $translationModel = TourTypeTranslation::class;
                    $foreignKey       = 'tour_type_id';
                    $translatableFields = ['name', 'description', 'duration'];
                    break;

                default:
                    abort(404, 'Invalid translation type.');
            }

            // Upsert de traducción principal
            $translation = $translationModel::firstOrNew([
                $foreignKey => $entity->getKey(),
                'locale'    => $locale,
            ]);

            foreach ($translatableFields as $field) {
                if (array_key_exists($field, $mainFieldValues)) {
                    $translation->{$field} = (string) $mainFieldValues[$field];
                } elseif (!$translation->exists) {
                    // Si no existe, sembramos con el valor base
                    $translation->{$field} = (string) ($entity->{$field} ?? '');
                }
            }
            $translation->save();

            // Soporte adicional: itinerary + items (solo para tours)
            if ($type === 'tours' && $entity->itinerary) {
                if (!empty($itineraryFieldValues)) {
                    $itTr = ItineraryTranslation::firstOrNew([
                        'itinerary_id' => $entity->itinerary->itinerary_id,
                        'locale'       => $locale,
                    ]);

                    if (array_key_exists('name', $itineraryFieldValues)) {
                        $itTr->name = (string) $itineraryFieldValues['name'];
                    } elseif (!$itTr->exists) {
                        $itTr->name = (string) ($entity->itinerary->name ?? '');
                    }

                    if (array_key_exists('description', $itineraryFieldValues)) {
                        $itTr->description = (string) $itineraryFieldValues['description'];
                    } elseif (!$itTr->exists) {
                        $itTr->description = (string) ($entity->itinerary->description ?? '');
                    }

                    $itTr->save();
                }

                if (!empty($itemFieldValuesById)) {
                    foreach ($entity->itinerary->items as $item) {
                        $itemId = $item->item_id;
                        if (!array_key_exists($itemId, $itemFieldValuesById)) {
                            continue;
                        }

                        $payload = $itemFieldValuesById[$itemId] ?? [];

                        $itemTr = ItineraryItemTranslation::firstOrNew([
                            'item_id' => $itemId,
                            'locale'  => $locale,
                        ]);

                        if (array_key_exists('title', $payload)) {
                            $itemTr->title = (string) $payload['title'];
                        } elseif (!$itemTr->exists) {
                            $itemTr->title = (string) ($item->title ?? '');
                        }

                        if (array_key_exists('description', $payload)) {
                            $itemTr->description = (string) $payload['description'];
                        } elseif (!$itemTr->exists) {
                            $itemTr->description = (string) ($item->description ?? '');
                        }

                        $itemTr->save();
                    }
                }
            }

            // Soporte adicional: secciones de policies (opcional)
            if ($type === 'policies' && !empty($sectionFieldValuesById) && $entity->relationLoaded('sections')) {
                foreach ($entity->sections as $section) {
                    $sectionId = $section->section_id;
                    if (!array_key_exists($sectionId, $sectionFieldValuesById)) {
                        continue;
                    }

                    $payload = $sectionFieldValuesById[$sectionId] ?? [];

                    $secTr = PolicySectionTranslation::firstOrNew([
                        'section_id' => $sectionId,
                        'locale'     => $locale,
                    ]);

                    if (array_key_exists('name', $payload)) {
                        $secTr->name = (string) $payload['name'];
                    } elseif (!$secTr->exists) {
                        $secTr->name = (string) ($section->name ?? '');
                    }

                    if (array_key_exists('content', $payload)) {
                        $secTr->content = (string) $payload['content'];
                    } elseif (!$secTr->exists) {
                        $secTr->content = (string) ($section->content ?? '');
                    }

                    $secTr->save();
                }
            }

            return redirect()
                ->route('admin.translations.select', ['type' => $type])
                ->with('success', __('m_config.translations.updated_success'));

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', __('m_config.translations.unexpected_error'));
        }
    }
}
