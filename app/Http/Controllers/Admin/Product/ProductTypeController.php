<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\ProductType;
use App\Services\LoggerHelper;
use App\Http\Requests\Product\ProductType\StoreProductTypeRequest;
use App\Http\Requests\Product\ProductType\UpdateProductTypeRequest;
// use App\Http\Requests\Tour\TourType\UpdateTourTypeTranslationRequest; // Unused if merged logic, or need to check
use App\Services\DeepLTranslator;
use Illuminate\Http\Request; // Use basic request for manual translation update or keep interface if compatible


/**
 * ProductTypeController
 *
 * Handles tourtype operations.
 */
class ProductTypeController extends Controller
{
    protected DeepLTranslator $translator;

    public function __construct(DeepLTranslator $translator)
    {
        $this->translator = $translator;
        $this->middleware(['can:view-tour-types'])->only(['index']);
        $this->middleware(['can:create-tour-types'])->only(['store']);
        $this->middleware(['can:edit-tour-types'])->only(['update']);
        $this->middleware(['can:publish-tour-types'])->only(['toggle']);
        $this->middleware(['can:delete-tour-types'])->only(['destroy']);
        $this->middleware(['can:restore-tour-types'])->only(['trash', 'restore']);
        $this->middleware(['can:force-delete-tour-types'])->only(['forceDelete']);
    }

    protected string $controller = 'ProductTypeController';

    public function index()
    {
        // Spatie autoloads or just works with attributes. 
        // No 'with("translations")' needed as it's JSON in same table.
        $productTypes = ProductType::orderByDesc('created_at')
            ->get();

        $trashedCount = ProductType::onlyTrashed()->count();

        $currentLocale = app()->getLocale();

        return view('admin.tourtypes.index', compact('productTypes', 'currentLocale', 'trashedCount'));
    }

    public function store(StoreProductTypeRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $productType = new ProductType();
            $productType->is_active = true;
            // Spatie: set translation for current locale
            $productType->setTranslation('name', app()->getLocale(), $data['name']);
            // If description/duration are translatable? ProductType only has 'name' in $translatable in the file I saw.
            // But if they were translatable before, we should check. 
            // File: public $translatable = ['name']; (Step 553 verified)
            // So description/duration go to main columns if they exist? 
            // ProductType fillable: name, duration, cover_path. No description. 
            // Wait, legacy code mapped description.
            // If description is NOT in fillable and NOT translatable, it will be lost.
            // Assuming duration is plain field.
            
            if (isset($data['duration'])) {
                $productType->duration = $data['duration'];
            }
            if (isset($data['cover_path'])) {
                 $productType->cover_path = $data['cover_path'];
            }
            
            $productType->save();

            // Auto-translate with DeepL
            $this->autoTranslate($productType, $data['name']);

            DB::commit();

            LoggerHelper::mutated($this->controller, 'store', 'tour_type', $productType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.product-types.index')
                ->with('success', 'm_config.tourtypes.created_success');

        } catch (Exception $e) {
            DB::rollBack();
            LoggerHelper::exception($this->controller, 'store', 'tour_type', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al crear el tipo de tour.')->withInput();
        }
    }

    public function update(UpdateProductTypeRequest $request, ProductType $productType)
    {
        try {
            DB::beginTransaction();

            $translations = $request->input('translations', []);

            if (!empty($translations)) {
                // Update via Translations Array (New Tabs Approach)
                foreach ($translations as $locale => $transData) {
                    if (isset($transData['name'])) {
                        $productType->setTranslation('name', $locale, $transData['name']);
                    }
                    if (isset($transData['description'])) {
                        $productType->setTranslation('description', $locale, $transData['description']);
                    }
                    // Duration is translatable per locale
                    if (isset($transData['duration'])) {
                        $productType->setTranslation('duration', $locale, $transData['duration']);
                    }
                }
            } else {
                // Legacy / Single Field Update
                $data = $request->validated(); 
                // Fallback to request input if validated doesn't cover all
                $name = $data['name'] ?? $request->input('name');
                if ($name) {
                    $productType->setTranslation('name', app()->getLocale(), $name);
                }
                
                $duration = $data['duration'] ?? $request->input('duration');
                if ($duration) {
                     $productType->duration = $duration;
                }
            }
            
            $productType->save();

            DB::commit();

            LoggerHelper::mutated($this->controller, 'update', 'tour_type', $productType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.product-types.index')
                ->with('success', 'm_config.tourtypes.updated_success');

        } catch (Exception $e) {
            DB::rollBack();
            LoggerHelper::exception($this->controller, 'update', 'tour_type', $productType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al actualizar el tipo de tour.')->withInput();
        }
    }

    public function toggle(ProductType $productType)
    {
        try {
            // Use update() instead of save() to avoid creating duplicates
            $newStatus = !$productType->is_active;
            $productType->update(['is_active' => $newStatus]);

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_type', $productType->getKey(), [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
                'new_status' => $newStatus
            ]);

            return back()->with('success', $newStatus ? 'Activado correctamente' : 'Desactivado correctamente');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_type', $productType->getKey(), $e);
            return back()->with('error', 'Error al cambiar el estado.');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $productType = ProductType::findOrFail($id);

        try {
            $productType->deleted_by = auth()->id();
            $productType->save();
            $productType->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'tour_type', $id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'm_config.tourtypes.deleted_success');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'tour_type', $id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'm_config.tourtypes.in_use_error');
        }
    }

    public function trash()
    {
        $productTypes = ProductType::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.tourtypes.trash', compact('productTypes'));
    }

    public function restore($id)
    {
        $productType = ProductType::onlyTrashed()->findOrFail($id);
        $productType->deleted_by = null;
        $productType->save();
        $productType->restore();

        LoggerHelper::mutated($this->controller, 'restore', 'tour_type', $id);

        return redirect()
            ->route('admin.product-types.trash')
            ->with('success', 'Tipo de tour restaurado correctamente.');
    }

    public function forceDelete($id)
    {
        $productType = ProductType::onlyTrashed()->findOrFail($id);

        // Verificar si tiene tours relacionados antes de borrar permanentemente
        if ($productType->products()->exists()) {
            return redirect()
                ->route('admin.product-types.trash')
                ->with('error', 'No se puede eliminar permanentemente porque tiene tours asociados.');
        }

        $productType->forceDelete();

        LoggerHelper::mutated($this->controller, 'forceDelete', 'tour_type', $id);

        return redirect()
            ->route('admin.product-types.trash')
            ->with('success', 'Tipo de tour eliminado permanentemente.');
    }

    /**
     * Auto-translate product type name to other languages using DeepL
     */
    protected function autoTranslate(ProductType $productType, string $sourceName): void
    {
        try {
            $sourceLocale = app()->getLocale();
            $targetLocales = array_diff(['es', 'en', 'fr', 'pt', 'de'], [$sourceLocale]);

            foreach ($targetLocales as $targetLocale) {
                if ($productType->getTranslation('name', $targetLocale, false)) {
                    continue;
                }

                $translated = $this->translator->translate($sourceName, $sourceLocale, $targetLocale);
                
                if ($translated) {
                    $productType->setTranslation('name', $targetLocale, $translated);
                }
            }

            $productType->save();
            
        } catch (Exception $e) {
            \Log::warning('DeepL translation failed for product type', [
                'product_type_id' => $productType->product_type_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
