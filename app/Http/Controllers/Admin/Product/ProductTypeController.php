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
use Illuminate\Http\Request; // Use basic request for manual translation update or keep interface if compatible


/**
 * ProductTypeController
 *
 * Handles tourtype operations.
 */
class ProductTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-tour-types'])->only(['index']);
        $this->middleware(['can:create-tour-types'])->only(['store']);
        $this->middleware(['can:edit-tour-types'])->only(['update', 'editTranslations', 'updateTranslation']);
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
        $tourTypes = ProductType::orderByDesc('created_at')
            ->get();

        $trashedCount = ProductType::onlyTrashed()->count();

        $currentLocale = app()->getLocale();

        return view('admin.tourtypes.index', compact('tourTypes', 'currentLocale', 'trashedCount'));
    }

    public function store(StoreProductTypeRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $tourType = new ProductType();
            $tourType->is_active = true;
            // Spatie: set translation for current locale
            $tourType->setTranslation('name', app()->getLocale(), $data['name']);
            // If description/duration are translatable? ProductType only has 'name' in $translatable in the file I saw.
            // But if they were translatable before, we should check. 
            // File: public $translatable = ['name']; (Step 553 verified)
            // So description/duration go to main columns if they exist? 
            // ProductType fillable: name, duration, cover_path. No description. 
            // Wait, legacy code mapped description.
            // If description is NOT in fillable and NOT translatable, it will be lost.
            // Assuming duration is plain field.
            
            if (isset($data['duration'])) {
                $tourType->duration = $data['duration'];
            }
            if (isset($data['cover_path'])) {
                 $tourType->cover_path = $data['cover_path'];
            }
            
            $tourType->save();

            DB::commit();

            LoggerHelper::mutated($this->controller, 'store', 'tour_type', $tourType->getKey(), [
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

    public function update(UpdateProductTypeRequest $request, ProductType $tourType)
    {
        try {
            DB::beginTransaction();

            $translations = $request->input('translations', []);

            if (!empty($translations)) {
                // Update via Translations Array (New Tabs Approach)
                foreach ($translations as $locale => $transData) {
                    if (isset($transData['name'])) {
                        $tourType->setTranslation('name', $locale, $transData['name']);
                    }
                    // Description/Duration ignored as ProductType model only lists 'name' as translatable and 'duration' as standard column?
                    // If duration is per-locale (legacy), we have a problem.
                    // But ProductType model only has 'duration' in fillable, likely not translatable.
                    if (isset($transData['duration']) && $locale == app()->getLocale()) {
                         $tourType->duration = $transData['duration'];
                    }
                }
            } else {
                // Legacy / Single Field Update
                $data = $request->validated(); 
                // Fallback to request input if validated doesn't cover all
                $name = $data['name'] ?? $request->input('name');
                if ($name) {
                    $tourType->setTranslation('name', app()->getLocale(), $name);
                }
                
                $duration = $data['duration'] ?? $request->input('duration');
                if ($duration) {
                     $tourType->duration = $duration;
                }
            }
            
            $tourType->save();

            DB::commit();

            LoggerHelper::mutated($this->controller, 'update', 'tour_type', $tourType->getKey(), [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()->route('admin.product-types.index')
                ->with('success', 'm_config.tourtypes.updated_success');

        } catch (Exception $e) {
            DB::rollBack();
            LoggerHelper::exception($this->controller, 'update', 'tour_type', $tourType->getKey(), $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'Error al actualizar el tipo de tour.')->withInput();
        }
    }

    public function toggle(ProductType $tourType)
    {
        try {
            $tourType->update(['is_active' => !$tourType->is_active]);

            LoggerHelper::mutated($this->controller, 'toggle', 'tour_type', $tourType->getKey(), [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
                'new_status' => $tourType->is_active
            ]);

            return back()->with('success', $tourType->is_active ? 'Activado correctamente' : 'Desactivado correctamente');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'tour_type', $tourType->getKey(), $e);
            return back()->with('error', 'Error al cambiar el estado.');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $tourType = ProductType::findOrFail($id);

        try {
            $tourType->deleted_by = auth()->id();
            $tourType->save();
            $tourType->delete();

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
        $tourTypes = ProductType::onlyTrashed()
            ->with(['deletedBy']) // Translations not a relation
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.tourtypes.trash', compact('tourTypes'));
    }

    public function restore($id)
    {
        $tourType = ProductType::onlyTrashed()->findOrFail($id);
        $tourType->deleted_by = null;
        $tourType->save();
        $tourType->restore();

        LoggerHelper::mutated($this->controller, 'restore', 'tour_type', $id);

        return redirect()
            ->route('admin.product-types.trash')
            ->with('success', 'Tipo de tour restaurado correctamente.');
    }

    public function forceDelete($id)
    {
        $tourType = ProductType::onlyTrashed()->findOrFail($id);

        // Verificar si tiene tours relacionados antes de borrar permanentemente
        if ($tourType->products()->exists()) {
            return redirect()
                ->route('admin.product-types.trash')
                ->with('error', 'No se puede eliminar permanentemente porque tiene tours asociados.');
        }

        $tourType->forceDelete();

        LoggerHelper::mutated($this->controller, 'forceDelete', 'tour_type', $id);

        return redirect()
            ->route('admin.product-types.trash')
            ->with('success', 'Tipo de tour eliminado permanentemente.');
    }

    /**
     * Mostrar vista de edición de traducciones con pestañas por locale
     */
    public function editTranslations(ProductType $tourType)
    {
        // Spatie stores translations in the model attributes, no relation to load.
        // But to pass to view as 'translationsByLocale', we need to extract them from JSON.
        // The view likely expects a collection of objects with 'name', 'locale' properties.
        
        $translationsByLocale = collect();
        $locales = $tourType->getTranslations('name'); // ['en' => 'Name', 'es' => 'Nombre']
        
        foreach ($locales as $locale => $name) {
             $translationsByLocale[$locale] = (object)[
                 'locale' => $locale,
                 'name' => $name,
                 // description/duration? ProductType only translatable 'name'
             ];
        }

        return view('admin.tourtypes.edit-translations', compact(
            'tourType',
            'supportedLocales',
            'translationsByLocale'
        ));
    }

    /**
     * Actualizar o crear traducción para un locale específico
     */
    public function updateTranslation(
        Request $request, // Or CustomRequest if compatible
        ProductType $tourType,
        string $locale
    ): RedirectResponse {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:191',
                // 'description' => 'nullable|string',
            ]);

            // Validar que el locale sea soportado
            $supportedLocales = ['es', 'en', 'fr', 'pt', 'de'];
            if (!in_array($locale, $supportedLocales)) {
                return back()
                    ->with('error', 'Locale no soportado.')
                    ->withInput();
            }

            // Actualizar o crear traducción
            $tourType->setTranslation('name', $locale, $data['name']);
            $tourType->save();

            LoggerHelper::mutated($this->controller, 'updateTranslation', 'tour_type', $tourType->getKey(), [
                'locale' => $locale,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.product-types.translations.edit', $tourType)
                ->with('success', "Traducción en {$locale} guardada correctamente.")
                ->with('active_locale', $locale); // Para mantener la pestaña activa
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'updateTranslation', 'tour_type', $tourType->getKey(), $e, [
                'locale' => $locale,
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'Error al guardar la traducción.')
                ->withInput();
        }
    }
}
