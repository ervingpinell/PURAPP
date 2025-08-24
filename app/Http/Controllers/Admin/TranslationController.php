<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

class TranslationController extends Controller
{
    /**
     * Entry point for the translations module.
     */
    public function index()
    {
        return view('admin.translations.index');
    }

    /**
     * Show a selector list for the chosen entity type.
     */
    public function select(string $type)
    {
        $entityLabel = match ($type) {
            'tours'           => 'tour',
            'itineraries'     => 'itinerary',
            'itinerary_items' => 'itinerary item',
            'amenities'       => 'amenity',
            'faqs'            => 'FAQ',
            'policies'        => 'policy',
            'tour_types'      => 'tour type',
            default           => abort(404),
        };

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

        $title = "Select {$entityLabel} to translate";

        return view('admin.translations.select', compact('items', 'type', 'entityLabel', 'title'));
    }

    /**
     * Choose locale to translate for a specific entity.
     */
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
            default           => abort(404),
        };

        return view('admin.translations.choose-locale', [
            'type' => $type,
            'item' => $entity,
        ]);
    }

    /**
     * Edit translations for the selected entity and locale.
     */
    public function edit(string $type, int $id)
    {
        $availableLocales = ['es', 'en', 'fr', 'pt', 'de'];
        $locale = request('locale', 'en');
        if (!in_array($locale, $availableLocales, true)) {
            $locale = 'en';
        }

        $entity = null;
        $translationModel = null;
        $foreignKey = '';
        $fields = [];
        $translations = [];

        switch ($type) {
            case 'tours':
                $entity = Tour::with(['itinerary.items'])->findOrFail($id);
                $translationModel = TourTranslation::class;
                $foreignKey = 'tour_id';
                $fields = ['name', 'overview'];
                break;

            case 'itineraries':
                $entity = Itinerary::findOrFail($id);
                $translationModel = ItineraryTranslation::class;
                $foreignKey = 'itinerary_id';
                $fields = ['name', 'description'];
                break;

            case 'itinerary_items':
                $entity = ItineraryItem::findOrFail($id);
                $translationModel = ItineraryItemTranslation::class;
                $foreignKey = 'item_id';
                $fields = ['title', 'description'];
                break;

            case 'amenities':
                $entity = Amenity::findOrFail($id);
                $translationModel = AmenityTranslation::class;
                $foreignKey = 'amenity_id';
                $fields = ['name'];
                break;

            case 'faqs':
                $entity = Faq::findOrFail($id);
                $translationModel = FaqTranslation::class;
                $foreignKey = 'faq_id';
                $fields = ['question', 'answer'];
                break;

            case 'policies':
                $entity = Policy::findOrFail($id);
                $translationModel = PolicyTranslation::class;
                $foreignKey = 'policy_id';
                $fields = ['title', 'content'];
                break;

            case 'tour_types':
                $entity = TourType::findOrFail($id);
                $translationModel = TourTypeTranslation::class;
                $foreignKey = 'tour_type_id';
                $fields = ['name', 'description', 'duration'];
                break;

            default:
                abort(404, 'Invalid translation type.');
        }

        foreach ($availableLocales as $lang) {
            $existing = $translationModel::where($foreignKey, $entity->getKey())
                ->where('locale', $lang)
                ->first();

            foreach ($fields as $field) {
                $translations[$lang][$field] = $existing ? ($existing->{$field} ?? '') : '';
            }
        }

        return view('admin.translations.edit', [
            'type'         => $type,
            'item'         => $entity,
            'locale'       => $locale,
            'fields'       => $fields,
            'translations' => $translations[$locale] ?? [],
        ]);
    }

    /**
     * Persist translations for an entity/locale.
     */
    public function update(Request $request, string $type, int $id)
    {
        $validated = $request->validate([
            'locale'        => 'required|in:es,en,fr,pt,de',
            'translations'  => 'nullable|array',
            'itinerary_translations' => 'nullable|array',
            'item_translations'      => 'nullable|array',
        ]);

        $locale             = $validated['locale'];
        $fieldValues        = $validated['translations'] ?? [];
        $itineraryValues    = $validated['itinerary_translations'] ?? [];
        $itemValuesById     = $validated['item_translations'] ?? [];

        $entity = null;
        $translationModel = null;
        $foreignKey = '';
        $fields = [];

        switch ($type) {
            case 'tours':
                $entity = Tour::with(['itinerary.items'])->findOrFail($id);
                $translationModel = TourTranslation::class;
                $foreignKey = 'tour_id';
                $fields = ['name', 'overview'];
                break;

            case 'itineraries':
                $entity = Itinerary::findOrFail($id);
                $translationModel = ItineraryTranslation::class;
                $foreignKey = 'itinerary_id';
                $fields = ['name', 'description'];
                break;

            case 'itinerary_items':
                $entity = ItineraryItem::findOrFail($id);
                $translationModel = ItineraryItemTranslation::class;
                $foreignKey = 'item_id';
                $fields = ['title', 'description'];
                break;

            case 'amenities':
                $entity = Amenity::findOrFail($id);
                $translationModel = AmenityTranslation::class;
                $foreignKey = 'amenity_id';
                $fields = ['name'];
                break;

            case 'faqs':
                $entity = Faq::findOrFail($id);
                $translationModel = FaqTranslation::class;
                $foreignKey = 'faq_id';
                $fields = ['question', 'answer'];
                break;

            case 'policies':
                $entity = Policy::findOrFail($id);
                $translationModel = PolicyTranslation::class;
                $foreignKey = 'policy_id';
                $fields = ['title', 'content'];
                break;

            case 'tour_types':
                $entity = TourType::findOrFail($id);
                $translationModel = TourTypeTranslation::class;
                $foreignKey = 'tour_type_id';
                $fields = ['name', 'description', 'duration'];
                break;

            default:
                abort(404, 'Invalid translation type.');
        }

        // Upsert main translation
        $translation = $translationModel::firstOrNew([
            $foreignKey => $entity->getKey(),
            'locale'    => $locale,
        ]);

        foreach ($fields as $field) {
            if (array_key_exists($field, $fieldValues)) {
                $translation->{$field} = (string) $fieldValues[$field];
            } elseif (!$translation->exists) {
                // Seed with original if creating a new translation row
                $translation->{$field} = (string) ($entity->{$field} ?? '');
            }
        }
        $translation->save();

        // If editing a tour, optionally update its itinerary + items in the same locale
        if ($type === 'tours' && $entity->itinerary) {
            if (!empty($itineraryValues)) {
                $itTr = ItineraryTranslation::firstOrNew([
                    'itinerary_id' => $entity->itinerary->itinerary_id,
                    'locale'       => $locale,
                ]);

                if (array_key_exists('name', $itineraryValues)) {
                    $itTr->name = (string) $itineraryValues['name'];
                } elseif (!$itTr->exists) {
                    $itTr->name = (string) ($entity->itinerary->name ?? '');
                }

                if (array_key_exists('description', $itineraryValues)) {
                    $itTr->description = (string) $itineraryValues['description'];
                } elseif (!$itTr->exists) {
                    $itTr->description = (string) ($entity->itinerary->description ?? '');
                }

                $itTr->save();
            }

            if (!empty($itemValuesById)) {
                foreach ($entity->itinerary->items as $item) {
                    $itemId = $item->item_id;
                    if (!array_key_exists($itemId, $itemValuesById)) {
                        continue;
                    }

                    $payload = $itemValuesById[$itemId] ?? [];

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

        return redirect()
            ->route('admin.translations.select', ['type' => $type])
            ->with('success', __('adminlte::adminlte.translation_updated_successfully') ?: 'Translation updated successfully.');
    }
}
