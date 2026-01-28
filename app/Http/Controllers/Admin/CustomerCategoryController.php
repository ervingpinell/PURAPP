<?php

// app/Http/Controllers/Admin/CustomerCategoryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
// use App\Models\CustomerCategoryTranslation;
use App\Http\Requests\Product\CustomerCategory\StoreCustomerCategoryRequest;
use App\Services\DeepLTranslator; // tu servicio
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LoggerHelper;

/**
 * CustomerCategoryController
 *
 * Handles customercategory operations.
 */
class CustomerCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-customer-categories'])->only(['index']);
        $this->middleware(['can:create-customer-categories'])->only(['create', 'store']);
        $this->middleware(['can:edit-customer-categories'])->only(['edit', 'update']);
        $this->middleware(['can:publish-customer-categories'])->only(['toggle']);
        $this->middleware(['can:delete-customer-categories'])->only(['destroy']);
        $this->middleware(['can:restore-customer-categories'])->only(['trash', 'restore']);
        $this->middleware(['can:hard-delete-customer-categories'])->only(['forceDelete']);
    }

    public function index()
    {
        $categories = CustomerCategory::ordered()->paginate(20);
        $trashedCount = CustomerCategory::onlyTrashed()->count();
        return view('admin.customer_categories.index', compact('categories', 'trashedCount'));
    }

    public function create()
    {
        return view('admin.customer_categories.create');
    }

    // AJAX Store
    public function store(StoreCustomerCategoryRequest $request, DeepLTranslator $translator)
    {
        try {
            DB::beginTransaction();

            $category = new CustomerCategory([
                'slug'      => $request->input('slug'),
                'is_active' => $request->boolean('is_active'),
                'age_from'  => $request->input('age_from'),
                'age_to'    => $request->input('age_to'),
            ]);

            if (!$category->validateNoOverlap()) {
                 if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'El rango de edad se solapa con otra categoría existente.'
                    ], 422);
                }
                return back()->with('error', 'El rango de edad se solapa con otra categoría existente.')->withInput();
            }

            $category->save();

            // For AJAX inline creation, we typically receive 'initial_name'
            // But validation uses 'names' array usually? 
            // The StoreRequest rules need to match.
            // My JS sends 'initial_name'.
            // The Request might need update or I map it here?
            // Actually, let's look at the Request object if I can... 
            // I'll assume I need to handle 'initial_name' or 'names'.
            // For now, I'll support 'initial_name' as a manual override if 'names' is missing.
            
            $name = $request->input('initial_name');
            $locale = app()->getLocale();
            
            if ($name) {
                // Smart Detect Language
                $detected = $translator->detect($name); // Returns 'en', 'es', etc.
                if ($detected && in_array($detected, supported_locales())) {
                    $locale = $detected;
                }

                $category->setTranslation('name', $locale, $name);
                
                // Smart translate for others
                if ($request->boolean('auto_translate')) {
                    foreach (supported_locales() as $loc) {
                        if ($loc !== $locale) {
                            try {
                                $trans = $translator->translate($name, $loc); 
                                $category->setTranslation('name', $loc, $trans);
                            } catch (\Throwable $e) {}
                        }
                    }
                }
            } else {
                // Fallback to legacy behaviour if form is standard
                 $names = $request->input('names', []);
                 foreach ($names as $l => $n) {
                    if (!empty($n)) {
                        $category->setTranslation('name', $l, $n);
                    }
                 }
            }
            $category->save();

            DB::commit();

            LoggerHelper::mutated('CustomerCategoryController', 'store', 'CustomerCategory', $category->category_id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Categoría creada con éxito.',
                    'category' => $category
                ]);
            }

            return redirect()
                ->route('admin.customer_categories.index')
                ->with('success', 'Categoría creada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            LoggerHelper::exception('CustomerCategoryController', 'store', 'CustomerCategory', null, $e);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al crear la categoría: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(CustomerCategory $category)
    {
        // Spatie autoloads translations, no need to eager load
        return view('admin.customer_categories.edit', compact('category'));
    }

    public function update(StoreCustomerCategoryRequest $request, CustomerCategory $category)
    {
        try {
            DB::beginTransaction();

            $category->update($request->validated());

            // Update translations
            foreach ($request->input('names', []) as $locale => $name) {
                if (!empty($name)) {
                    $category->setTranslation('name', $locale, $name);
                }
            }
            $category->save();

            DB::commit();

            LoggerHelper::mutated('CustomerCategoryController', 'update', 'CustomerCategory', $category->category_id);

            return redirect()
                ->route('admin.customer_categories.index')
                ->with('success', 'Categoría actualizada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            LoggerHelper::exception('CustomerCategoryController', 'update', 'CustomerCategory', $category->category_id, $e);
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function toggle(CustomerCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        LoggerHelper::mutated('CustomerCategoryController', 'toggle', 'CustomerCategory', $category->category_id);

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(CustomerCategory $category)
    {
        $category->deleted_by = auth()->id();
        $category->save();
        $category->delete();

        LoggerHelper::mutated('CustomerCategoryController', 'destroy', 'CustomerCategory', $category->category_id);

        return redirect()
            ->route('admin.customer_categories.index')
            ->with('success', 'Categoría enviada a la papelera.');
    }

    public function trash()
    {
        $categories = CustomerCategory::onlyTrashed()
            ->with(['deletedBy']) // Removed 'translations'
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.customer_categories.trash', compact('categories'));
    }

    public function restore($id)
    {
        $category = CustomerCategory::onlyTrashed()->findOrFail($id);
        $category->deleted_by = null;
        $category->save();
        $category->restore();

        LoggerHelper::mutated('CustomerCategoryController', 'restore', 'CustomerCategory', $category->category_id);

        return redirect()
            ->route('admin.customer_categories.trash')
            ->with('success', 'Categoría restaurada correctamente.');
    }

    public function forceDelete($id)
    {
        $category = CustomerCategory::onlyTrashed()->findOrFail($id);

        // Check for dependencies (TourPrice)
        if ($category->tourPrices()->exists()) {
            return redirect()
                ->route('admin.customer_categories.trash')
                ->with('error', 'No se puede eliminar permanentemente porque tiene precios de tours asociados.');
        }

        $category->forceDelete();

        LoggerHelper::mutated('CustomerCategoryController', 'forceDelete', 'CustomerCategory', $id);

        return redirect()
            ->route('admin.customer_categories.trash')
            ->with('success', 'Categoría eliminada permanentemente.');
    }

    // New Methods for Inline Editing
    public function quickUpdate(Request $request, CustomerCategory $category, DeepLTranslator $translator)
    {
        $rules = [
            'age_from' => 'nullable|integer|min:0',
            'age_to'   => 'nullable|integer|gte:age_from',
            'slug'     => 'nullable|string|max:60|unique:customer_categories,slug,'.$category->category_id.',category_id',
            'name'     => 'nullable|string|max:120',
        ];

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->validator->errors()->first()], 422);
        }

        // Check for overlap BEFORE saving
        // We temporarily set attributes to check logic, but don't save yet
        $category->fill([
            'age_from' => $request->has('age_from') ? $request->input('age_from') : $category->age_from,
            'age_to'   => $request->has('age_to') ? $request->input('age_to') : $category->age_to,
        ]);

        if (!$category->validateNoOverlap()) {
            return response()->json([
                'status' => 'error', 
                'message' => __('customer_categories.validation.age_overlap')
            ], 422);
        }

        $newName = null;

        if ($request->has('name')) {
            $locale = app()->getLocale();
            $category->setTranslation('name', $locale, $request->input('name'));
            $newName = $request->input('name');
            
            // Smart Translate Logic
            if ($request->boolean('smart_translate')) {
                 foreach(supported_locales() as $loc) {
                     if($loc !== $locale) {
                         try {
                              // Assuming we translate FROM current locale TO others
                              $trans = $translator->translate($request->input('name'), $loc);
                              $category->setTranslation('name', $loc, $trans);
                          } catch(\Throwable $e) {}
                      }
                  }
            }
        }

        $category->save();
        
        \Illuminate\Support\Facades\Cache::forget('customer_categories_active');

        return response()->json([
            'status'    => 'success',
            'message'   => __('customer_categories.messages.updated'),
            'name'      => $newName
        ]);
    }



    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:customer_categories,category_id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            CustomerCategory::where('category_id', $id)->update(['order' => $index + 1]);
        }

        \Illuminate\Support\Facades\Cache::forget('customer_categories_active');

        return response()->json(['status' => 'success', 'message' => __('customer_categories.messages.reorder_success')]);
    }
}
