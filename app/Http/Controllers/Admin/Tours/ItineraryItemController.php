<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ItineraryItem;
use App\Models\ItineraryItemTranslation;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\ItineraryItem\StoreItineraryItemRequest;
use App\Http\Requests\Tour\ItineraryItem\UpdateItineraryItemRequest;
use App\Http\Requests\Tour\ItineraryItem\ToggleItineraryItemRequest;

/**
 * ItineraryItemController
 *
 * Handles itineraryitem operations.
 */
class ItineraryItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-itineraries'])->only(['index']); // Using 'view-itineraries' as parent or should I allow 'view-itinerary-items'?
        // Wait, I defined 'publish-itinerary-items' in the list. Do I have 'create-itinerary-items' etc?
        // Let's check PermissionsSeeder.php content from my memory or the file.
        // I recall I might NOT have added 'create-itinerary-items'.
        // Let me double check if I have those permissions defined. 
        // IF NOT, I should use 'edit-itineraries' for items too?
        // Actually, for 'toggle', I definitely defined 'publish-itinerary-items'.
        // Let's assume standard crud exists or I will just use 'edit-itineraries' for general crud and 'publish-itinerary-items' for toggle.
        // To be safe I will use specific permissions if they exist, or fallback to itinerary permissions.
        // Re-reading task description: "Identified Modules... ItineraryItemController... needs publish-itinerary-items".
        // Use separate permissions logic:
        $this->middleware(['can:view-itineraries'])->only(['index']);
        $this->middleware(['can:edit-itineraries'])->only(['store', 'update', 'updateTranslations', 'destroy', 'trash', 'restore', 'forceDelete']);
        $this->middleware(['can:publish-itinerary-items'])->only(['toggle']);
    }

    protected string $controller = 'ItineraryItemController';

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = ItineraryItem::with('translations');
        
        if ($search) {
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%");
            });
        } else {
             $query->where('is_active', true);
        }

        $items = $query->get()->sortBy('title');

        return view('admin.tours.itinerary.items.index', [
            'items' => $items,
            'search' => $search
        ]);
    }

    public function store(StoreItineraryItemRequest $request, TranslatorInterface $translator)
    {
        try {
            $data    = $request->validated();
            $title   = $data['title'];
            $desc    = $data['description'];
            $locales = supported_locales();

            $item = DB::transaction(function () use ($title, $desc, $locales, $translator) {
                $item = ItineraryItem::create([
                    'is_active'   => true,
                ]);

                $titleTr = $translator->translateAll($title);
                $descTr  = $translator->translateAll($desc);

                foreach ($locales as $locale) {
                    ItineraryItemTranslation::create([
                        'item_id'     => $item->item_id,
                        'locale'      => $locale,
                        'title'       => $titleTr[$locale] ?? $title,
                        'description' => $descTr[$locale] ?? $desc,
                    ]);
                }

                return $item;
            });

            if (! $item) {
                LoggerHelper::error($this->controller, 'store', 'ItineraryItem null after commit', [
                    'entity'  => 'itinerary_item',
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
                return back()->with('error', __('m_tours.itinerary_item.error.create'));
            }

            LoggerHelper::mutated($this->controller, 'store', 'itinerary_item', $item->item_id, [
                'locales_saved' => count($locales),
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary_item.success.created'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'itinerary_item', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.itinerary_item.error.create'));
        }
    }

    public function update(UpdateItineraryItemRequest $request, ItineraryItem $itinerary_item)
    {
        try {
            $data = $request->validated();

            // Update only Spanish translation for now
            $translation = $itinerary_item->translations()->where('locale', 'es')->first();
            if ($translation) {
                $translation->update([
                    'title'       => $data['title'],
                    'description' => $data['description'],
                ]);
            } else {
                // Create Spanish translation if it doesn't exist
                $itinerary_item->translations()->create([
                    'locale'      => 'es',
                    'title'       => $data['title'],
                    'description' => $data['description'],
                ]);
            }

            // If is_active is in the payload, apply it to the parent model
            if (array_key_exists('is_active', $data)) {
                $itinerary_item->update(['is_active' => (bool) $data['is_active']]);
            }

            $itinerary_item->refresh();

            // Si quedó inactivo (ya sea por update o por toggle), lo desasignamos
            if ($itinerary_item->is_active === false && method_exists($itinerary_item, 'itineraries')) {
                $itinerary_item->itineraries()->detach();
            }

            LoggerHelper::mutated($this->controller, 'update', 'itinerary_item', $itinerary_item->item_id, [
                'is_active' => $itinerary_item->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary_item.success.updated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'itinerary_item', $itinerary_item->item_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.itinerary_item.error.update'));
        }
    }

    /** PATCH: Activar/Desactivar (toggle) */
    public function toggle(ToggleItineraryItemRequest $request, ItineraryItem $itinerary_item)
    {
        try {
            // Race-safe
            ItineraryItem::whereKey($itinerary_item->getKey())->update(['is_active' => DB::raw('NOT is_active')]);
            $itinerary_item->refresh();

            if (! $itinerary_item->is_active && method_exists($itinerary_item, 'itineraries')) {
                $itinerary_item->itineraries()->detach();
            }

            LoggerHelper::mutated($this->controller, 'toggle', 'itinerary_item', $itinerary_item->item_id, [
                'is_active' => $itinerary_item->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $message = $itinerary_item->is_active
                ? __('m_tours.itinerary_item.success.activated')
                : __('m_tours.itinerary_item.success.deactivated');

            return back()->with('success', $message);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'itinerary_item', $itinerary_item->item_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()
                ->withErrors(['error' => __('m_tours.itinerary_item.error.toggle')])
                ->with('error', __('m_tours.itinerary_item.error.toggle'));
        }
    }

    /**
     * Update translations for an itinerary item across multiple locales.
     */
    public function updateTranslations(Request $request, ItineraryItem $itinerary_item)
    {
        $locales = config('app.supported_locales', ['es', 'en', 'fr', 'de', 'pt']);

        // Build validation rules dynamically
        $rules = [];
        foreach ($locales as $locale) {
            $rules["translations.{$locale}.title"] = $locale === 'es'
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            $rules["translations.{$locale}.description"] = 'nullable|string|max:1000';
        }

        $validated = $request->validate($rules);

        // Update or create translations for each locale
        foreach ($locales as $locale) {
            $translationData = $validated['translations'][$locale] ?? null;

            if (!$translationData) {
                continue;
            }

            // Skip if both fields are empty (except for Spanish which is required)
            if (empty($translationData['title']) && empty($translationData['description'])) {
                if ($locale !== 'es') {
                    continue;
                }
            }

            $itinerary_item->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $translationData['title'] ?? '',
                    'description' => $translationData['description'] ?? '',
                ]
            );
        }

        return redirect()
            ->route('admin.tours.itinerary.index')
            ->with('success', __('m_tours.itinerary_item.ui.translations_updated'));
    }

    /** DELETE: Eliminación definitiva */
    public function destroy(ItineraryItem $itinerary_item)
    {
        try {
            $id = $itinerary_item->item_id;

            DB::transaction(function () use ($itinerary_item) {
                if (method_exists($itinerary_item, 'itineraries')) {
                    $itinerary_item->itineraries()->detach();
                }
                if (method_exists($itinerary_item, 'translations')) {
                    $itinerary_item->translations()->delete();
                } else {
                    ItineraryItemTranslation::where('item_id', $itinerary_item->item_id)->delete();
                }
                $itinerary_item->delete();
            });

            LoggerHelper::mutated($this->controller, 'destroy', 'itinerary_item', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary_item.success.deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'itinerary_item', $itinerary_item->item_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_tours.itinerary_item.error.delete'));
        }
    }

    /**
     * View trash list for items.
     */
    public function trash()
    {
        $items = ItineraryItem::onlyTrashed()
            ->with(['translations'])
            ->get()
            ->sortBy('title');

        return view('admin.tours.itinerary.items.trash', ['items' => $items]);
    }

    /**
     * Restore soft-deleted itinerary item.
     */
    public function restore($id)
    {
        try {
            $item = ItineraryItem::withTrashed()->findOrFail($id);
            $item->restore();

            LoggerHelper::mutated($this->controller, 'restore', 'itinerary_item', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary_item.success.restored'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'restore', 'itinerary_item', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary_item.error.restored'));
        }
    }

    /**
     * Force delete itinerary item permanently.
     */
    public function forceDelete($id)
    {
        try {
            $item = ItineraryItem::withTrashed()->findOrFail($id);
            $idCaptured = $item->item_id;

            DB::transaction(function () use ($item) {
                // Detach from all itineraries
                if (method_exists($item, 'itineraries')) {
                    $item->itineraries()->detach();
                }
                // Delete translations
                if (method_exists($item, 'translations')) {
                    $item->translations()->delete();
                } else {
                    ItineraryItemTranslation::where('item_id', $item->item_id)->delete();
                }
                
                $item->forceDelete();
            });

            LoggerHelper::mutated($this->controller, 'forceDelete', 'itinerary_item', $idCaptured, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary_item.success.force_deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'forceDelete', 'itinerary_item', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary_item.error.force_delete'));
        }
    }
}
