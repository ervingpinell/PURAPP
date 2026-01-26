<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Itinerary;
// use App\Models\ItineraryTranslation; // Removed
use App\Services\ItineraryService;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;
use App\Http\Requests\Tour\Itinerary\StoreItineraryRequest;
use App\Http\Requests\Tour\Itinerary\UpdateItineraryRequest;
use App\Http\Requests\Tour\Itinerary\AssignItineraryItemsRequest;

/**
 * ItineraryController
 *
 * Handles itinerary operations.
 */
class ItineraryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-itineraries'])->only(['index']);
        $this->middleware(['can:create-itineraries'])->only(['store']);
        $this->middleware(['can:edit-itineraries'])->only(['update', 'assignItems', 'updateTranslations']);
        $this->middleware(['can:publish-itineraries'])->only(['toggle']);
        $this->middleware(['can:delete-itineraries'])->only(['destroy']);
        $this->middleware(['can:restore-itineraries'])->only(['restore']);
        $this->middleware(['can:force-delete-itineraries'])->only(['forceDelete']);
    }

    protected string $controller = 'ItineraryController';

    public function index(Request $request, ItineraryService $service)
    {
        $search = $request->input('search');
        
        // Spatie translatable: remove 'translations' eager load
        $query = Itinerary::with(['allItems']);
        
        if ($search) {
             // Search in JSON column for current locale or any locale
             // LOWER(name->>'es') LIKE '%search%'
             $locale = app()->getLocale();
             $query->whereRaw("LOWER(name->>?) LIKE ?", [$locale, "%" . strtolower($search) . "%"]);
        }

        $itineraryList = $query->get();
        // $availableItems = $service->getAvailableItems(); // No longer needed for index view if we separate them

        // We might still need availableItems for the "Assign Items" modal which is included in the index view.
        // Let's keep it but ideally we should load it via AJAX or View Composer if performance matters.
        $availableItems = $service->getAvailableItems();

        return view('admin.products.itinerary.index', [
            'itineraries' => $itineraryList,
            'items'       => $availableItems,
            'search'      => $search
        ]);
    }

    public function store(StoreItineraryRequest $request, TranslatorInterface $translator)
    {
        try {
            $data        = $request->validated();
            $name        = $data['name'];
            $description = (string) ($data['description'] ?? '');
            $locales     = supported_locales();

            $itinerary = DB::transaction(function () use ($name, $description, $locales, $translator) {
                $itinerary = Itinerary::create([
                    'is_active'   => true,
                ]);

                // Traducciones automáticas
                $nameTr = $translator->translateAll($name);
                $descTr = $translator->translateAll($description);

                foreach ($locales as $locale) {
                    $itinerary->setTranslation('name', $locale, $nameTr[$locale] ?? $name);
                    $itinerary->setTranslation('description', $locale, $descTr[$locale] ?? $description);
                }
                $itinerary->save();

                return $itinerary;
            });

            if (! $itinerary) {
                LoggerHelper::error($this->controller, 'store', 'Itinerary null after commit', [
                    'entity'  => 'itinerary',
                    'user_id' => optional($request->user())->getAuthIdentifier(),
                ]);
                return back()->with('error', __('m_tours.itinerary.error.create'));
            }

            LoggerHelper::mutated($this->controller, 'store', 'itinerary', $itinerary->itinerary_id, [
                'locales_saved' => count($locales),
                'user_id'       => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary.success.created'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'itinerary', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.create'));
        }
    }

    public function update(UpdateItineraryRequest $request, Itinerary $itinerary)
    {
        try {
            $data = $request->validated();

            // Update Spanish translation
            $itinerary->setTranslation('name', 'es', $data['name']);
            if (isset($data['description'])) {
                $itinerary->setTranslation('description', 'es', $data['description']);
            }
            $itinerary->save();

            LoggerHelper::mutated($this->controller, 'update', 'itinerary', $itinerary->itinerary_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.products.itinerary.index')
                ->with('success', __('m_tours.itinerary.success.updated'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'itinerary', $itinerary->itinerary_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.update'));
        }
    }

    /**
     * View trash list.
     */
    public function trash()
    {
        $trashedItineraries = Itinerary::onlyTrashed()
            ->with(['deletedBy'])
            ->get();

        return view('admin.products.itinerary.trash', [
            'itineraries' => $trashedItineraries,
        ]);
    }

    /**
     * Restore soft-deleted itinerary.
     */
    public function restore($id)
    {
        try {
            $itinerary = Itinerary::withTrashed()->findOrFail($id);
            $itinerary->restore();

            // Clear deleted_by
            $itinerary->update(['deleted_by' => null]);

            LoggerHelper::mutated($this->controller, 'restore', 'itinerary', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary.success.restored'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'restore', 'itinerary', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.restore'));
        }
    }

    /**
     * Force delete itinerary permanently.
     */
    public function forceDelete($id)
    {
        try {
            $itinerary = Itinerary::withTrashed()->findOrFail($id);
            $idCaptured = $itinerary->itinerary_id;

            // Detach items first if necessary or rely on database cascade if configured
            $itinerary->items()->detach();
            // $itinerary->translations()->delete(); // Not needed for Spatie JSON

            $itinerary->forceDelete();

            LoggerHelper::mutated($this->controller, 'forceDelete', 'itinerary', $idCaptured, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary.success.force_deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'forceDelete', 'itinerary', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.force_delete'));
        }
    }

    /**
     * Toggle activar/desactivar itinerario
     */
    public function toggle(Itinerary $itinerary)
    {
        try {
            // Race-safe: NOT is_active a nivel de DB
            Itinerary::whereKey($itinerary->getKey())->update(['is_active' => DB::raw('NOT is_active')]);
            $itinerary->refresh();

            LoggerHelper::mutated($this->controller, 'toggle', 'itinerary', $itinerary->itinerary_id, [
                'is_active' => $itinerary->is_active,
                'user_id'   => optional(request()->user())->getAuthIdentifier(),
            ]);

            $msg = $itinerary->is_active
                ? __('m_tours.itinerary.success.activated')
                : __('m_tours.itinerary.success.deactivated');

            return back()->with('success', $msg);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'itinerary', $itinerary->itinerary_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.toggle'));
        }
    }

    /**
     * Eliminación definitiva (NO usar aún).
     * Dejo el método comentado para cuando quieras habilitarlo.
     */
    public function destroy(Itinerary $itinerary)
    {
        try {
            $id = $itinerary->itinerary_id;

            // Soft delete logic with deleted_by
            $itinerary->update([
                'deleted_by' => optional(request()->user())->getAuthIdentifier(),
            ]);
            $itinerary->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'itinerary', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', __('m_tours.itinerary.success.deleted'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'itinerary', $itinerary->itinerary_id ?? null, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', __('m_tours.itinerary.error.delete'));
        }
    }

    /**
     * Asignar ítems a un itinerario (recibe items[ID]=orden desde el modal)
     */
    public function assignItems(AssignItineraryItemsRequest $request, Itinerary $itinerary)
    {
        try {
            $data = $request->validated();
            $map  = $data['items'] ?? []; // normalizado por el Request

            $pivotData = [];
            foreach ($map as $itemId => $order) {
                $pivotData[(int) $itemId] = [
                    'item_order' => (int) $order,
                    'is_active'  => true,
                ];
            }

            $itinerary->items()->sync($pivotData);

            LoggerHelper::mutated($this->controller, 'assignItems', 'itinerary', $itinerary->itinerary_id, [
                'items_assigned' => count($pivotData),
                'user_id'        => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.products.itinerary.index')
                ->with('success', __('m_tours.itinerary.success.items_assigned'));
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'assignItems', 'itinerary', $itinerary->itinerary_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->withErrors(['items' => __('m_tours.itinerary.error.assign')])
                ->with('error', __('m_tours.itinerary.error.assign'))
                ->with('showAssignModal', $itinerary->itinerary_id);
        }
    }

    /**
     * Update translations for an itinerary across multiple locales.
     */
    public function updateTranslations(Request $request, Itinerary $itinerary)
    {
        $locales = config('app.supported_locales', ['es', 'en', 'fr', 'de', 'pt']);

        // Build validation rules dynamically
        $rules = [];
        foreach ($locales as $locale) {
            $rules["translations.{$locale}.name"] = $locale === 'es'
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
            if (empty($translationData['name']) && empty($translationData['description'])) {
                if ($locale !== 'es') {
                    continue;
                }
            }

            $itinerary->setTranslation('name', $locale, $translationData['name'] ?? '');
            $itinerary->setTranslation('description', $locale, $translationData['description'] ?? '');
        }
        $itinerary->save();


        return redirect()
            ->route('admin.products.itinerary.index')
            ->with('success', __('m_tours.itinerary.ui.translations_updated'));
    }
}
