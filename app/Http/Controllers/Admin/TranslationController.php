<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Modelos base
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\TourType; // 👈 NEW

// Traducciones
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;
use App\Models\TourTypeTranslation; // 👈 NEW

// Policies (solo cabecera; las secciones se gestionan en su controller dedicado)
use App\Models\Policy;
use App\Models\PolicyTranslation;

class TranslationController extends Controller
{
    public function index()
    {
        return view('admin.translations.index');
    }

    public function select(string $type)
    {
        $labelSingular = match ($type) {
            'tours'            => 'tour',
            'itineraries'      => 'itinerario',
            'itinerary_items'  => 'ítem de itinerario',
            'amenities'        => 'amenidad',
            'faqs'             => 'pregunta frecuente',
            'policies'         => 'política',
            'tour_types'       => 'tipo de tour', // 👈 NEW
            default            => abort(404),
        };

        $items = match ($type) {
            'tours'            => Tour::orderBy('tour_id')->get(),
            'itineraries'      => Itinerary::orderBy('itinerary_id')->get(),
            'itinerary_items'  => ItineraryItem::orderBy('id')->get(), // ajusta si tu PK es distinta
            'amenities'        => Amenity::orderBy('amenity_id')->get(),
            'faqs'             => Faq::orderBy('faq_id')->get(),
            'policies'         => Policy::orderBy('policy_id')->get(),
            'tour_types'       => TourType::orderBy('tour_type_id')->get(), // 👈 NEW
            default            => collect(),
        };

        $title = "Seleccionar {$labelSingular} para traducir";

        return view('admin.translations.select', compact('items', 'type', 'labelSingular', 'title'));
    }

    public function selectLocale(string $type, int $id)
    {
        $item = match ($type) {
            'tours'            => Tour::findOrFail($id),
            'itineraries'      => Itinerary::findOrFail($id),
            'itinerary_items'  => ItineraryItem::findOrFail($id),
            'amenities'        => Amenity::findOrFail($id),
            'faqs'             => Faq::findOrFail($id),
            'policies'         => Policy::findOrFail($id),
            'tour_types'       => TourType::findOrFail($id), // 👈 NEW
            default            => abort(404),
        };

        return view('admin.translations.choose-locale', compact('type', 'item'));
    }

    public function edit(string $type, int $id)
    {
        // Puedes ampliar locales si lo necesitas
        $availableLocales = ['es', 'en', 'fr', 'pt', 'de'];
        $locale = request('locale', 'en');

        $item = null;
        $translationModel = null;
        $fields = [];
        $foreignKey = '';
        $translations = [];

        switch ($type) {
            case 'tours':
                $item = Tour::with(['itinerary.items'])->findOrFail($id);
                $translationModel = TourTranslation::class;
                $foreignKey = 'tour_id';
                $fields = ['name', 'overview'];
                break;

            case 'itineraries':
                $item = Itinerary::findOrFail($id);
                $translationModel = ItineraryTranslation::class;
                $foreignKey = 'itinerary_id';
                $fields = ['name', 'description'];
                break;

            case 'itinerary_items':
                $item = ItineraryItem::findOrFail($id);
                $translationModel = ItineraryItemTranslation::class;
                $foreignKey = 'item_id'; // ajusta si tu FK real es distinto
                $fields = ['title', 'description'];
                break;

            case 'amenities':
                $item = Amenity::findOrFail($id);
                $translationModel = AmenityTranslation::class;
                $foreignKey = 'amenity_id';
                $fields = ['name'];
                break;

            case 'faqs':
                $item = Faq::findOrFail($id);
                $translationModel = FaqTranslation::class;
                $foreignKey = 'faq_id';
                $fields = ['question', 'answer'];
                break;

            case 'policies':
                $item = Policy::findOrFail($id);
                $translationModel = PolicyTranslation::class;
                $foreignKey = 'policy_id';
                $fields = ['title', 'content'];
                break;

            case 'tour_types': // 👈 NEW
                $item = TourType::findOrFail($id);
                $translationModel = TourTypeTranslation::class;
                $foreignKey = 'tour_type_id';
                $fields = ['name', 'description', 'duration'];
                break;

            default:
                abort(404, 'Tipo de traducción no válido');
        }

        foreach ($availableLocales as $lang) {
            $record = $translationModel::where($foreignKey, $item->getKey())
                ->where('locale', $lang)
                ->first();

            foreach ($fields as $field) {
                $translations[$lang][$field] = $record ? ($record->{$field} ?? '') : '';
            }
        }

        return view('admin.translations.edit', [
            'type'         => $type,
            'item'         => $item,
            'locale'       => $locale,
            'fields'       => $fields,
            'translations' => $translations[$locale] ?? [],
        ]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $locale = $request->input('locale');
        $data   = $request->input('translations', []);

        $model = null;
        $translationModel = null;
        $foreignKey = '';
        $fields = [];

        switch ($type) {
            case 'tours':
                $model = Tour::with(['itinerary.items'])->findOrFail($id);
                $translationModel = TourTranslation::class;
                $foreignKey = 'tour_id';
                $fields = ['name', 'overview'];
                break;

            case 'itineraries':
                $model = Itinerary::findOrFail($id);
                $translationModel = ItineraryTranslation::class;
                $foreignKey = 'itinerary_id';
                $fields = ['name', 'description'];
                break;

            case 'itinerary_items':
                $model = ItineraryItem::findOrFail($id);
                $translationModel = ItineraryItemTranslation::class;
                $foreignKey = 'item_id'; // ajusta si tu FK real es distinto
                $fields = ['title', 'description'];
                break;

            case 'amenities':
                $model = Amenity::findOrFail($id);
                $translationModel = AmenityTranslation::class;
                $foreignKey = 'amenity_id';
                $fields = ['name'];
                break;

            case 'faqs':
                $model = Faq::findOrFail($id);
                $translationModel = FaqTranslation::class;
                $foreignKey = 'faq_id';
                $fields = ['question', 'answer'];
                break;

            case 'policies':
                $model = Policy::findOrFail($id);
                $translationModel = PolicyTranslation::class;
                $foreignKey = 'policy_id';
                $fields = ['title', 'content'];
                break;

            case 'tour_types': // 👈 NEW
                $model = TourType::findOrFail($id);
                $translationModel = TourTypeTranslation::class;
                $foreignKey = 'tour_type_id';
                $fields = ['name', 'description', 'duration'];
                break;

            default:
                abort(404, 'Tipo de traducción no válido');
        }

        // ---------- Guardado SEGURO (sin nulls indeseados) ----------
        $translation = $translationModel::firstOrNew([
            $foreignKey => $model->getKey(),
            'locale'    => $locale,
        ]);

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                // Vino en el request (aunque sea ''), úsalo
                $translation->{$field} = $data[$field];
            } else {
                // No vino: si es nuevo el registro, inicializa desde el modelo o ''
                if (!$translation->exists) {
                    $translation->{$field} = (string) ($model->{$field} ?? '');
                }
            }
        }
        $translation->save();

        // ---------- Extras: T O U R S (itinerario + ítems) ----------
        if ($type === 'tours' && $model->itinerary) {
            // Itinerario
            $itineraryData = $request->input('itinerary_translations', []);
            if (!empty($itineraryData)) {
                $itTr = ItineraryTranslation::firstOrNew([
                    'itinerary_id' => $model->itinerary->itinerary_id,
                    'locale'       => $locale,
                ]);

                if (array_key_exists('name', $itineraryData)) {
                    $itTr->name = $itineraryData['name'];
                } elseif (!$itTr->exists) {
                    $itTr->name = (string) ($model->itinerary->name ?? '');
                }

                if (array_key_exists('description', $itineraryData)) {
                    $itTr->description = $itineraryData['description'];
                } elseif (!$itTr->exists) {
                    $itTr->description = (string) ($model->itinerary->description ?? '');
                }

                $itTr->save();
            }

            // Ítems de itinerario
            $itemData = $request->input('item_translations', []);
            if (!empty($itemData)) {
                foreach ($model->itinerary->items as $item) {
                    $itemKey = $item->id; // ajusta si tu PK es distinta
                    if (!array_key_exists($itemKey, $itemData)) continue;

                    $payload = $itemData[$itemKey] ?? [];

                    $itemTr = ItineraryItemTranslation::firstOrNew([
                        'item_id' => $itemKey,
                        'locale'  => $locale,
                    ]);

                    if (array_key_exists('title', $payload)) {
                        $itemTr->title = $payload['title'];
                    } elseif (!$itemTr->exists) {
                        $itemTr->title = (string) ($item->title ?? '');
                    }

                    if (array_key_exists('description', $payload)) {
                        $itemTr->description = $payload['description'];
                    } elseif (!$itemTr->exists) {
                        $itemTr->description = (string) ($item->description ?? '');
                    }

                    $itemTr->save();
                }
            }
        }

        return redirect()
            ->route('admin.translations.select', ['type' => $type])
            ->with('success', '✅ Traducción actualizada correctamente.');
    }
}
