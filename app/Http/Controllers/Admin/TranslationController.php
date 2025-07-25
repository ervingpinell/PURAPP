<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;

class TranslationController extends Controller
{
    public function index()
    {
        return view('admin.translations.index');
    }

    public function select(string $type)
    {
        $labelSingular = match ($type) {
            'tours' => 'tour',
            'itineraries' => 'itinerario',
            'itinerary_items' => 'ítem de itinerario',
            'amenities' => 'amenidad',
            'faqs' => 'pregunta frecuente',
            default => abort(404)
        };

        $items = match ($type) {
            'tours' => Tour::all(),
            'itineraries' => Itinerary::all(),
            'itinerary_items' => ItineraryItem::all(),
            'amenities' => Amenity::all(),
            'faqs' => Faq::all(),
            default => collect()
        };

        $title = "Seleccionar {$labelSingular} para traducir";

        return view('admin.translations.select', compact('items', 'type', 'labelSingular', 'title'));
    }

    public function selectLocale(string $type, int $id)
    {
        $item = match ($type) {
            'tours' => Tour::findOrFail($id),
            'itineraries' => Itinerary::findOrFail($id),
            'itinerary_items' => ItineraryItem::findOrFail($id),
            'amenities' => Amenity::findOrFail($id),
            'faqs' => Faq::findOrFail($id),
            default => abort(404)
        };

        return view('admin.translations.choose-locale', compact('type', 'item'));
    }

    public function edit(string $type, int $id)
    {
        $availableLocales = ['en', 'pt', 'fr', 'de'];
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
                $foreignKey = 'item_id';
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

            default:
                abort(404, 'Tipo de traducción no válido');
        }

        foreach ($availableLocales as $lang) {
            $record = $translationModel::where($foreignKey, $item->getKey())->where('locale', $lang)->first();
            foreach ($fields as $field) {
                $translations[$lang][$field] = $record->{$field} ?? '';
            }
        }

        return view('admin.translations.edit', [
            'type' => $type,
            'item' => $item,
            'locale' => $locale,
            'fields' => $fields,
            'translations' => $translations[$locale] ?? []
        ]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $locale = $request->input('locale');
        $data = $request->input('translations', []);

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
                $foreignKey = 'item_id';
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
            default:
                abort(404, 'Tipo de traducción no válido');
        }

        // ✅ Guardar traducción principal (Tour, Itinerario, etc.)
        $translation = $translationModel::firstOrNew([
            $foreignKey => $model->getKey(),
            'locale' => $locale,
        ]);

        foreach ($fields as $field) {
            $translation->{$field} = $data[$field] ?? null;
        }

        $translation->save();

        // ✅ Guardar traducción del itinerario (si existe y es un tour)
        if ($type === 'tours' && $model->itinerary) {
            $itineraryData = $request->input('itinerary_translations', []);
            $itineraryTranslation = ItineraryTranslation::firstOrNew([
                'itinerary_id' => $model->itinerary->itinerary_id,
                'locale' => $locale,
            ]);

            $itineraryTranslation->name = $itineraryData['name'] ?? null;
            $itineraryTranslation->description = $itineraryData['description'] ?? null;
            $itineraryTranslation->save();

            // ✅ Guardar traducciones de ítems del itinerario
            $itemData = $request->input('item_translations', []);
            foreach ($model->itinerary->items as $item) {
                if (!isset($itemData[$item->id])) continue;

                $itemTranslation = ItineraryItemTranslation::firstOrNew([
                    'item_id' => $item->id,
                    'locale' => $locale,
                ]);

                $itemTranslation->title = $itemData[$item->id]['title'] ?? null;
                $itemTranslation->description = $itemData[$item->id]['description'] ?? null;
                $itemTranslation->save();
            }
        }

        return redirect()
            ->route('admin.translations.select', ['type' => $type])
            ->with('success', '✅ Traducción actualizada correctamente.');
    }
}
