<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Modelos base
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\Policy;

// Traducciones base
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;
use App\Models\PolicyTranslation;

// NUEVOS: Secciones de Policy
use App\Models\PolicySection;
use App\Models\PolicySectionTranslation;

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
            default            => abort(404),
        };

        $items = match ($type) {
            'tours'            => Tour::orderBy('tour_id')->get(),
            'itineraries'      => Itinerary::orderBy('itinerary_id')->get(),
            'itinerary_items'  => ItineraryItem::orderBy('id')->get(), // ajusta si tu PK es distinta
            'amenities'        => Amenity::orderBy('amenity_id')->get(),
            'faqs'             => Faq::orderBy('faq_id')->get(),
            'policies'         => Policy::orderBy('policy_id')->get(),
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
            default            => abort(404),
        };

        return view('admin.translations.choose-locale', compact('type', 'item'));
    }

    public function edit(string $type, int $id)
    {
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
                $foreignKey = 'item_id'; // ajusta si tu FK real es 'itinerary_item_id'
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
                // Eager-load secciones y sus traducciones para el partial
                $item = Policy::with([
                    'sections' => fn($q) => $q->orderBy('sort_order')->orderBy('section_id'),
                    'sections.translations'
                ])->findOrFail($id);
                $translationModel = PolicyTranslation::class;
                $foreignKey = 'policy_id';
                $fields = ['title', 'content'];   // Campos de la política (categoría)
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

        // Cargamos modelo + configuramos clase de traducción/keys
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

            case 'policies':
                $model = Policy::with(['sections'])->findOrFail($id);
                $translationModel = PolicyTranslation::class;
                $foreignKey = 'policy_id';
                $fields = ['title', 'content'];
                break;

            default:
                abort(404, 'Tipo de traducción no válido');
        }

        DB::transaction(function () use ($type, $model, $translationModel, $foreignKey, $fields, $data, $locale, $request) {
            // Guardar/actualizar traducción del objeto principal
            $translation = $translationModel::firstOrNew([
                $foreignKey => $model->getKey(),
                'locale'    => $locale,
            ]);

            foreach ($fields as $field) {
                $translation->{$field} = $data[$field] ?? null;
            }
            $translation->save();

            // Extra: tours -> opcional traducir itinerario/ítems si vienen en request
            if ($type === 'tours' && $model->itinerary) {
                $itineraryData = $request->input('itinerary_translations', []);
                if (!empty($itineraryData)) {
                    $itineraryTranslation = ItineraryTranslation::firstOrNew([
                        'itinerary_id' => $model->itinerary->itinerary_id,
                        'locale'       => $locale,
                    ]);
                    $itineraryTranslation->name = $itineraryData['name'] ?? null;
                    $itineraryTranslation->description = $itineraryData['description'] ?? null;
                    $itineraryTranslation->save();
                }

                $itemData = $request->input('item_translations', []);
                if (!empty($itemData)) {
                    foreach ($model->itinerary->items as $item) {
                        $itemKey = $item->id; // ajusta si tu PK es distinta
                        if (!isset($itemData[$itemKey])) continue;

                        $itemTranslation = ItineraryItemTranslation::firstOrNew([
                            'item_id' => $itemKey, // ajusta si tu FK real es 'itinerary_item_id'
                            'locale'  => $locale,
                        ]);

                        $itemTranslation->title       = $itemData[$itemKey]['title'] ?? null;
                        $itemTranslation->description = $itemData[$itemKey]['description'] ?? null;
                        $itemTranslation->save();
                    }
                }
            }

            // NUEVO: policies -> editar/crear secciones y sus traducciones
            if ($type === 'policies') {
                // 1) Metadatos de secciones existentes
                $meta = $request->input('section_meta', []); // [section_id => [sort_order, is_active]]
                foreach ($meta as $sectionId => $m) {
                    $section = PolicySection::where('policy_id', $model->policy_id)
                        ->where('section_id', $sectionId)
                        ->first();
                    if (!$section) continue;

                    $section->sort_order = isset($m['sort_order']) ? (int) $m['sort_order'] : $section->sort_order;
                    $section->is_active  = isset($m['is_active']) ? (bool) $m['is_active'] : false;
                    $section->save();
                }

                // 2) Traducciones de secciones existentes en el locale actual
                $sectionTr = $request->input('section_translations', []); // [section_id => [title, content]]
                foreach ($sectionTr as $sectionId => $stData) {
                    $exists = PolicySection::where('policy_id', $model->policy_id)
                        ->where('section_id', $sectionId)
                        ->exists();
                    if (!$exists) continue;

                    PolicySectionTranslation::updateOrCreate(
                        ['section_id' => (int) $sectionId, 'locale' => $locale],
                        [
                            'title'   => $stData['title']   ?? '',
                            'content' => $stData['content'] ?? '',
                        ]
                    );
                }

                // 3) Nuevas secciones (meta + traducción)
                $new = $request->input('new_sections', []); // [[sort_order, is_active, title, content], ...]
                foreach ($new as $row) {
                    $title   = trim($row['title']   ?? '');
                    $content = trim($row['content'] ?? '');
                    $hasAny  = $title !== '' || $content !== '' || !empty($row['sort_order']) || !empty($row['is_active']);

                    if (!$hasAny) continue; // evitar crear registros vacíos

                    $section = PolicySection::create([
                        'policy_id'  => $model->policy_id,
                        'sort_order' => (int)($row['sort_order'] ?? 0),
                        'is_active'  => !empty($row['is_active']),
                    ]);

                    PolicySectionTranslation::updateOrCreate(
                        ['section_id' => $section->section_id, 'locale' => $locale],
                        ['title' => $title, 'content' => $content]
                    );
                }
            }
        });

        return redirect()
            ->route('admin.translations.select', ['type' => $type])
            ->with('success', '✅ Traducción actualizada correctamente.');
    }
}
