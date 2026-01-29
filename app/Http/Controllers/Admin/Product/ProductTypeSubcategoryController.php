<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\ProductTypeSubcategory;
use App\Services\LoggerHelper;
use App\Services\DeepLTranslator;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductTypeSubcategoryController extends Controller
{
    protected string $controller = 'ProductTypeSubcategoryController';
    protected DeepLTranslator $translator;

    public function __construct(DeepLTranslator $translator)
    {
        $this->translator = $translator;
        $this->middleware(['can:view-product-types'])->only(['index']);
        $this->middleware(['can:create-product-types'])->only(['store']);
        $this->middleware(['can:edit-product-types'])->only(['update', 'toggle']);
        $this->middleware(['can:delete-product-types'])->only(['destroy']);
    }

    /**
     * Display subtypes for a specific product type
     */
    public function index(ProductType $productType)
    {
        $subtypes = $productType->subcategories()
            ->orderBy('sort_order')
            ->get();

        $currentLocale = app()->getLocale();

        return view('admin.producttypes.subtypes.index', compact(
            'productType',
            'subtypes',
            'currentLocale'
        ));
    }

    /**
     * Store a new subtype
     */
    public function store(Request $request, ProductType $productType): RedirectResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:191',
                'slug' => 'nullable|string|max:50|unique:product_type_subcategories,slug',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
            ]);

            $subtype = new ProductTypeSubcategory();
            $subtype->product_type_id = $productType->product_type_id;
            $subtype->setTranslation('name', app()->getLocale(), $data['name']);
            $subtype->slug = $data['slug'] ?? Str::slug($data['name']);
            $subtype->description = $data['description'] ?? null;
            $subtype->icon = $data['icon'] ?? null;
            $subtype->color = $data['color'] ?? null;
            
            // Auto-assign sort_order
            $maxOrder = ProductTypeSubcategory::where('product_type_id', $productType->product_type_id)
                ->max('sort_order');
            $subtype->sort_order = ($maxOrder ?? 0) + 1;
            
            $subtype->is_active = true;
            $subtype->save();

            // Auto-translate with DeepL
            $this->autoTranslateSubtype($subtype, $data['name']);

            LoggerHelper::mutated($this->controller, 'store', 'product_type_subcategory', $subtype->subtype_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.product-types.subtypes.index', $productType)
                ->with('success', 'Subtipo creado correctamente.');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'product_type_subcategory', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            
            return back()
                ->with('error', 'Error al crear el subtipo.')
                ->withInput();
        }
    }

    /**
     * Update an existing subtype
     */
    public function update(Request $request, ProductTypeSubcategory $subtype): RedirectResponse
    {
        try {
            $translations = $request->input('translations', []);

            if (!empty($translations)) {
                // Update via Translations Array (Tabs Approach)
                foreach ($translations as $locale => $transData) {
                    if (isset($transData['name'])) {
                        $subtype->setTranslation('name', $locale, $transData['name']);
                    }
                    if (isset($transData['description'])) {
                        $subtype->setTranslation('description', $locale, $transData['description']);
                    }
                }
            } else {
                // Legacy / Single Field Update
                $data = $request->validate([
                    'name' => 'required|string|max:191',
                    'slug' => 'required|string|max:50|unique:product_type_subcategories,slug,' . $subtype->subtype_id . ',subtype_id',
                    'description' => 'nullable|string',
                    'icon' => 'nullable|string|max:50',
                    'color' => 'nullable|string|max:20',
                ]);

                $subtype->setTranslation('name', app()->getLocale(), $data['name']);
                $subtype->slug = $data['slug'];
                $subtype->description = $data['description'] ?? null;
                $subtype->icon = $data['icon'] ?? null;
                $subtype->color = $data['color'] ?? null;
            }

            $subtype->save();

            LoggerHelper::mutated($this->controller, 'update', 'product_type_subcategory', $subtype->subtype_id, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.product-types.subtypes.index', $subtype->product_type_id)
                ->with('success', 'Subtipo actualizado correctamente.');

        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'product_type_subcategory', $subtype->subtype_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            
            return back()
                ->with('error', 'Error al actualizar el subtipo.')
                ->withInput();
        }
    }

    /**
     * Toggle active status
     */
    public function toggle(ProductTypeSubcategory $subtype): RedirectResponse
    {
        try {
            $subtype->update(['is_active' => !$subtype->is_active]);

            LoggerHelper::mutated($this->controller, 'toggle', 'product_type_subcategory', $subtype->subtype_id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
                'new_status' => $subtype->is_active
            ]);

            return back()->with('success', $subtype->is_active ? 'Activado correctamente' : 'Desactivado correctamente');
            
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'product_type_subcategory', $subtype->subtype_id, $e);
            return back()->with('error', 'Error al cambiar el estado.');
        }
    }

    /**
     * Delete (soft delete) a subtype
     */
    public function destroy(ProductTypeSubcategory $subtype): RedirectResponse
    {
        try {
            // Check if subtype has products
            if ($subtype->products()->exists()) {
                return back()->with('error', 'No se puede eliminar porque tiene productos asociados.');
            }

            $subtype->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'product_type_subcategory', $subtype->subtype_id, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'Subtipo eliminado correctamente.');
            
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'product_type_subcategory', $subtype->subtype_id, $e, [
                'user_id' => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Error al eliminar el subtipo.');
        }
    }

    /**
     * Update sort order (AJAX)
     */
    public function reorder(Request $request, ProductType $productType)
    {
        try {
            $order = $request->input('order', []);

            foreach ($order as $index => $subtypeId) {
                ProductTypeSubcategory::where('subtype_id', $subtypeId)
                    ->update(['sort_order' => $index + 1]);
            }

            return response()->json(['success' => true]);
            
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Auto-translate subtype name to other languages using DeepL
     */
    protected function autoTranslateSubtype(ProductTypeSubcategory $subtype, string $sourceName): void
    {
        try {
            $sourceLocale = app()->getLocale();
            $targetLocales = ['es', 'en', 'fr', 'pt', 'de'];
            
            // Remove source locale from targets
            $targetLocales = array_diff($targetLocales, [$sourceLocale]);

            foreach ($targetLocales as $targetLocale) {
                // Skip if already has translation
                if ($subtype->getTranslation('name', $targetLocale, false)) {
                    continue;
                }

                // Translate
                $translated = $this->translator->translate($sourceName, $sourceLocale, $targetLocale);
                
                if ($translated) {
                    $subtype->setTranslation('name', $targetLocale, $translated);
                }
            }

            $subtype->save();
            
        } catch (Exception $e) {
            // Log but don't fail - translations are optional
            \Log::warning('DeepL translation failed for subtype', [
                'subtype_id' => $subtype->subtype_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
