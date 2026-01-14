<?php

// app/Http/Controllers/Admin/CustomerCategoryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use App\Models\CustomerCategoryTranslation;
use App\Http\Requests\Tour\CustomerCategory\StoreCustomerCategoryRequest;
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
        $this->middleware(['can:force-delete-customer-categories'])->only(['forceDelete']);
    }

    public function index()
    {
        $categories = CustomerCategory::with('translations')->ordered()->paginate(20);
        $trashedCount = CustomerCategory::onlyTrashed()->count();
        return view('admin.customer_categories.index', compact('categories', 'trashedCount'));
    }

    public function create()
    {
        return view('admin.customer_categories.create');
    }

    public function store(StoreCustomerCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $category = CustomerCategory::create($request->validated());

            // Handle translations
            foreach ($request->input('names', []) as $locale => $name) {
                if (!empty($name)) {
                    CustomerCategoryTranslation::create([
                        'category_id' => $category->category_id,
                        'locale'      => $locale,
                        'name'        => $name,
                    ]);
                }
            }

            DB::commit();

            LoggerHelper::mutated('CustomerCategoryController', 'store', 'CustomerCategory', $category->category_id);

            return redirect()
                ->route('admin.customer_categories.index')
                ->with('success', 'Categoría creada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            LoggerHelper::exception('CustomerCategoryController', 'store', 'CustomerCategory', null, $e);
            return back()->with('error', 'Error al crear la categoría: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(CustomerCategory $category)
    {
        $category->load('translations');
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
                    $category->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $name]
                    );
                }
            }

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
            ->with(['translations', 'deletedBy'])
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
}
