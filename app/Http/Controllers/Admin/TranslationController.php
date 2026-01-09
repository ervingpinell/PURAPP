<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

/**
 * TranslationController
 *
 * Handles translation operations.
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
        $entitySingular = __('m_config.translations.entities_singular.' . $type);
        if ($entitySingular === 'm_config.translations.entities_singular.' . $type) {
            abort(404, 'Invalid translation type.');
        }

        return view('admin.translations.choose-locale', [
            'type' => $type,
        ]);
    }

    public function select(Request $request, string $type)
    {
        $entitySingular = __('m_config.translations.entities_singular.' . $type);
        if ($entitySingular === 'm_config.translations.entities_singular.' . $type) {
            abort(404, 'Invalid translation type.');
        }

        $editLocale        = $request->query('edit_locale');
        $availableLocales  = ['es', 'en', 'fr', 'pt', 'de'];
        if ($editLocale && in_array($editLocale, $availableLocales, true)) {
            session(['translation_editing_locale' => $editLocale]);
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

        $uiLocale = app()->getLocale();
        $items    = $this->applyLocaleOnItems($items, $type, $uiLocale);

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
        $translationModel   = null;
        $foreignKey         = '';
        $translatableFields = [];
        $allTranslations    = [];

        switch ($type) {
            case 'tours':
                $entity             = Tour::with(['itinerary.items'])->findOrFail($id);
                $translationModel   = TourTranslation::class;
                $foreignKey         = 'tour_id';
                $translatableFields = ['name', 'overview'];
                break;

            case 'itineraries':
                $entity             = Itinerary::findOrFail($id);
                $translationModel   = ItineraryTranslation::class;
                $foreignKey         = 'itinerary_id';
                $translatableFields = ['name', 'description'];
                break;

            case 'itinerary_items':
                $entity             = ItineraryItem::findOrFail($id);
                $translationModel   = ItineraryItemTranslation::class;
                $foreignKey         = 'item_id';
                $translatableFields = ['title', 'description'];
                break;

            case 'amenities':
                $entity             = Amenity::findOrFail($id);
                $translationModel   = AmenityTranslation::class;
                $foreignKey         = 'amenity_id';
                $translatableFields = ['name'];
                break;

            case 'faqs':
                $entity             = Faq::findOrFail($id);
                $translationModel   = FaqTranslation::class;
                $foreignKey         = 'faq_id';
                $translatableFields = ['question', 'answer'];
                break;

            case 'policies':
                $entity             = Policy::with('sections')->findOrFail($id);
                $translationModel   = PolicyTranslation::class;
                $foreignKey         = 'policy_id';
                // usamos name (no title)
                $translatableFields = ['name', 'content'];
                break;

            case 'tour_types':
                $entity             = TourType::findOrFail($id);
                $translationModel   = TourTypeTranslation::class;
                $foreignKey         = 'tour_type_id';
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
                $val = '';
                if ($existing) {
                    $val = (string) ($existing->{$field} ?? '');
                    if ($val === '') {
                        // Fallback: obtener de la traducción en español si existe
                        if ($type === 'tour_types') {
                            $val = (string) ($entity->translate('es')?->{$field} ?? '');
                        } elseif ($type === 'policies' && $field === 'name') {
                            $val = (string) ($entity->name ?? '');
                        } else {
                            $val = (string) ($entity->{$field} ?? '');
                        }
                    }
                } else {
                    // No existe traducción: obtener de la traducción en español si existe
                    if ($type === 'tour_types') {
                        $val = (string) ($entity->translate('es')?->{$field} ?? '');
                    } elseif ($type === 'policies' && $field === 'name') {
                        $val = (string) ($entity->name ?? '');
                    } else {
                        $val = (string) ($entity->{$field} ?? '');
                    }
                }
                $allTranslations[$lang][$field] = $val;
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
            'itinerary_translations' => 'nullable|array',
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
             * 🔧 Normalización para políticas y secciones:
             * Aceptar 'title' como alias de 'name' mientras el Blade se actualiza.
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

            $entity             = null;
            $translationModel   = null;
            $foreignKey         = '';
            $translatableFields = [];

            switch ($type) {
                case 'tours':
                    $entity             = Tour::with(['itinerary.items'])->findOrFail($id);
                    $translationModel   = TourTranslation::class;
                    $foreignKey         = 'tour_id';
                    $translatableFields = ['name', 'overview'];
                    break;

                case 'itineraries':
                    $entity             = Itinerary::findOrFail($id);
                    $translationModel   = ItineraryTranslation::class;
                    $foreignKey         = 'itinerary_id';
                    $translatableFields = ['name', 'description'];
                    break;

                case 'itinerary_items':
                    $entity             = ItineraryItem::findOrFail($id);
                    $translationModel   = ItineraryItemTranslation::class;
                    $foreignKey         = 'item_id';
                    $translatableFields = ['title', 'description'];
                    break;

                case 'amenities':
                    $entity             = Amenity::findOrFail($id);
                    $translationModel   = AmenityTranslation::class;
                    $foreignKey         = 'amenity_id';
                    $translatableFields = ['name'];
                    break;

                case 'faqs':
                    $entity             = Faq::findOrFail($id);
                    $translationModel   = FaqTranslation::class;
                    $foreignKey         = 'faq_id';
                    $translatableFields = ['question', 'answer'];
                    break;

                case 'policies':
                    $entity             = Policy::with('sections')->findOrFail($id);
                    $translationModel   = PolicyTranslation::class;
                    $foreignKey         = 'policy_id';
                    // usamos name, no title
                    $translatableFields = ['name', 'content'];
                    break;

                case 'tour_types':
                    $entity             = TourType::findOrFail($id);
                    $translationModel   = TourTypeTranslation::class;
                    $foreignKey         = 'tour_type_id';
                    $translatableFields = ['name', 'description', 'duration'];
                    break;

                default:
                    abort(404, 'Invalid translation type.');
            }

            DB::transaction(function () use (
                $translationModel,
                $foreignKey,
                $entity,
                $locale,
                $translatableFields,
                $mainFieldValues,
                $type,
                $itineraryFieldValues,
                $itemFieldValuesById,
                $sectionFieldValuesById
            ) {
                $translation = $translationModel::firstOrNew([
                    $foreignKey => $entity->getKey(),
                    'locale'    => $locale,
                ]);

                foreach ($translatableFields as $field) {
                    if (array_key_exists($field, $mainFieldValues)) {
                        $translation->{$field} = (string) $mainFieldValues[$field];
                    } elseif (!$translation->exists) {
                        // Fallback para nuevas traducciones
                        if ($type === 'tour_types') {
                            $translation->{$field} = (string) ($entity->translate('es')?->{$field} ?? '');
                        } elseif ($type === 'policies' && $field === 'name') {
                            $translation->name = (string) ($entity->name ?? '');
                        } else {
                            $translation->{$field} = (string) ($entity->{$field} ?? '');
                        }
                    }
                }
                $translation->save();

                // Itinerario + Items (sólo para tours)
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

                // Secciones de políticas
                if ($type === 'policies' && !empty($sectionFieldValuesById)) {
                    if (!$entity->relationLoaded('sections')) {
                        $entity->load('sections');
                    }

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
            });

            return redirect()
                ->route('admin.translations.edit', [
                    'type'        => $type,
                    'id'          => $id,
                    'edit_locale' => $locale,
                ])
                ->with('success', __('m_config.translations.updated_success'));
        } catch (Exception $e) {
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

    private function applyLocaleOnItems($items, string $type, string $locale)
    {
        if (method_exists($items, 'isEmpty') && $items->isEmpty()) return $items;

        switch ($type) {
            case 'tours':
                $map = TourTranslation::whereIn('tour_id', $items->pluck('tour_id'))
                    ->where('locale', $locale)->get()->keyBy('tour_id');
                foreach ($items as $it) {
                    $tr = $map[$it->tour_id] ?? null;
                    if ($tr && ($tr->name ?? '') !== '')       $it->name = $tr->name;
                    if ($tr && ($tr->overview ?? '') !== '')   $it->overview = $tr->overview;
                }
                break;

            case 'itineraries':
                $map = ItineraryTranslation::whereIn('itinerary_id', $items->pluck('itinerary_id'))
                    ->where('locale', $locale)->get()->keyBy('itinerary_id');
                foreach ($items as $it) {
                    $tr = $map[$it->itinerary_id] ?? null;
                    if ($tr && ($tr->name ?? '') !== '')         $it->name = $tr->name;
                    if ($tr && ($tr->description ?? '') !== '')  $it->description = $tr->description;
                }
                break;

            case 'itinerary_items':
                $map = ItineraryItemTranslation::whereIn('item_id', $items->pluck('item_id'))
                    ->where('locale', $locale)->get()->keyBy('item_id');
                foreach ($items as $it) {
                    $tr = $map[$it->item_id] ?? null;
                    if ($tr && ($tr->title ?? '') !== '')        $it->title = $tr->title;
                    if ($tr && ($tr->description ?? '') !== '')  $it->description = $tr->description;
                }
                break;

            case 'amenities':
                $map = AmenityTranslation::whereIn('amenity_id', $items->pluck('amenity_id'))
                    ->where('locale', $locale)->get()->keyBy('amenity_id');
                foreach ($items as $it) {
                    $tr = $map[$it->amenity_id] ?? null;
                    if ($tr && ($tr->name ?? '') !== '') $it->name = $tr->name;
                }
                break;

            case 'faqs':
                $map = FaqTranslation::whereIn('faq_id', $items->pluck('faq_id'))
                    ->where('locale', $locale)->get()->keyBy('faq_id');
                foreach ($items as $it) {
                    $tr = $map[$it->faq_id] ?? null;
                    if ($tr && ($tr->question ?? '') !== '') $it->question  = $tr->question;
                    if ($tr && ($tr->answer ?? '')   !== '') $it->answer    = $tr->answer;
                }
                break;

            case 'policies':
                $map = PolicyTranslation::whereIn('policy_id', $items->pluck('policy_id'))
                    ->where('locale', $locale)->get()->keyBy('policy_id');
                foreach ($items as $it) {
                    $tr = $map[$it->policy_id] ?? null;
                    if ($tr && ($tr->name ?? '') !== '')    $it->name    = $tr->name;
                    if ($tr && ($tr->content ?? '') !== '') $it->content = $tr->content;
                }
                break;

            case 'tour_types':
                $map = TourTypeTranslation::whereIn('tour_type_id', $items->pluck('tour_type_id'))
                    ->where('locale', $locale)->get()->keyBy('tour_type_id');
                foreach ($items as $it) {
                    $tr = $map[$it->tour_type_id] ?? null;
                    if ($tr && ($tr->name ?? '') !== '')        $it->name        = $tr->name;
                    if ($tr && ($tr->description ?? '') !== '') $it->description = $tr->description;
                    if ($tr && ($tr->duration ?? '') !== '')    $it->duration    = $tr->duration;
                }
                break;
        }

        return $items;
    }
}
