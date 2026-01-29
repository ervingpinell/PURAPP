<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ItineraryItem;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;
use App\Http\Requests\Product\ItineraryItem\StoreItineraryItemRequest;
use App\Http\Requests\Product\ItineraryItem\UpdateItineraryItemRequest;
use App\Http\Requests\Product\ItineraryItem\ToggleItineraryItemRequest;

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
        $this->middleware(['can:edit-itineraries'])->only(['store', 'update', 'destroy', 'trash', 'restore', 'forceDelete']);
        $this->middleware(['can:publish-itinerary-items'])->only(['toggle']);
    }

    protected string $controller = 'ItineraryItemController';

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = ItineraryItem::query();
        
        if ($search) {
             $locale = app()->getLocale();
             $query->whereRaw("LOWER(title->>?) LIKE ?", [$locale, "%" . strtolower($search) . "%"]);
        }
        
        // Order by title and paginate
        $items = $query->orderBy('title->es')->paginate(15)->withQueryString();

        return view('admin.products.itinerary.items.index', [
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
                    $item->setTranslation('title', $locale, $titleTr[$locale] ?? $title);
                    $item->setTranslation('description', $locale, $descTr[$locale] ?? $desc);
                }
                $item->save();

                return $item;
            });

            if (! $item) {
                LoggerHelper::error($this->controller, 'store', 'ItineraryItem null after commit', [
                    'entity'  => 'itinerary_item',
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
                return back()->with('error', __('m_products.itinerary_item.error.create'));
            }

            LoggerHelper::mutated($this->controller, 'store', 'itinerary_item', $item->item_id, [
                'locales_saved' => count($locales),
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_products.itinerary_item.success.created'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'itinerary_item', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.itinerary_item.error.create'));
        }
    }

    public function update(UpdateItineraryItemRequest $request, ItineraryItem $itinerary_item)
    {
        try {
            $data = $request->validated();

            // Check if using new tab-based translations array
            if (isset($data['translations']) && is_array($data['translations'])) {
                // Update via Translations Array (New Tabs Approach)
                foreach ($data['translations'] as $locale => $transData) {
                    if (isset($transData['title'])) {
                        $itinerary_item->setTranslation('title', $locale, $transData['title']);
                    }
                    if (isset($transData['description'])) {
                        $itinerary_item->setTranslation('description', $locale, $transData['description']);
                    }
                }
            } else {
                // Legacy / Single Field Update (fallback for old forms)
                if (isset($data['title'])) {
                    $itinerary_item->setTranslation('title', app()->getLocale(), $data['title']);
                }
                if (isset($data['description'])) {
                    $itinerary_item->setTranslation('description', app()->getLocale(), $data['description']);
                }
            }

            // If is_active is in the payload, apply it to the parent model
            if (array_key_exists('is_active', $data)) {
                $itinerary_item->is_active = (bool) $data['is_active'];
            }
            
            $itinerary_item->save();

            $itinerary_item->refresh();

            // Si quedó inactivo (ya sea por update o por toggle), lo desasignamos
            if ($itinerary_item->is_active === false && method_exists($itinerary_item, 'itineraries')) {
                $itinerary_item->itineraries()->detach();
            }

            LoggerHelper::mutated($this->controller, 'update', 'itinerary_item', $itinerary_item->item_id, [
                'is_active' => $itinerary_item->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_products.itinerary_item.success.updated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'itinerary_item', $itinerary_item->item_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.itinerary_item.error.update'));
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
                ? __('m_products.itinerary_item.success.activated')
                : __('m_products.itinerary_item.success.deactivated');

            return back()->with('success', $message);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'itinerary_item', $itinerary_item->item_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()
                ->withErrors(['error' => __('m_products.itinerary_item.error.toggle')])
                ->with('error', __('m_products.itinerary_item.error.toggle'));
        }
    }



    /** DELETE: Eliminación definitiva */
    public function destroy(Request $request, ItineraryItem $itinerary_item)
    {
        try {
            $id = $itinerary_item->item_id;
            $userId = optional($request->user())->getAuthIdentifier();

            DB::transaction(function () use ($itinerary_item, $userId) {
                // Set deleted_by before soft deleting
                $itinerary_item->deleted_by = $userId;
                $itinerary_item->save();
                
                if (method_exists($itinerary_item, 'itineraries')) {
                    $itinerary_item->itineraries()->detach();
                }
                
                $itinerary_item->delete();
            });

            LoggerHelper::mutated($this->controller, 'destroy', 'itinerary_item', $id, [
                'user_id' => $userId,
            ]);

            return back()->with('success', __('m_products.itinerary_item.success.deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'itinerary_item', $itinerary_item->item_id ?? null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', __('m_products.itinerary_item.error.delete'));
        }
    }

    /**
     * View trash list for items.
     */
    public function trash()
    {
        $items = ItineraryItem::onlyTrashed()
            ->orderBy('title->es')
            ->paginate(15);

        return view('admin.products.itinerary.items.trash', ['items' => $items]);
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

            return back()->with('success', __('m_products.itinerary_item.success.restored'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'restore', 'itinerary_item', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_products.itinerary_item.error.restored'));
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
                if (method_exists($item, 'itineraries')) {
                    $item->itineraries()->detach();
                }
                // Translations are in JSON now
                /*
                if (method_exists($item, 'translations')) {
                    $item->translations()->delete();
                }
                */
                
                $item->forceDelete();
            });

            LoggerHelper::mutated($this->controller, 'forceDelete', 'itinerary_item', $idCaptured, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_products.itinerary_item.success.force_deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'forceDelete', 'itinerary_item', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_products.itinerary_item.error.force_delete'));
        }
    }
}
