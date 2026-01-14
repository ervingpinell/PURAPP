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

    // ... (create, store, edit, update, toggle maintained as is) ...

    public function destroy(CustomerCategory $customer_category)
    {
        // En Laravel Route Model Binding, si el nombre del parámetro no coincide con la ruta resource, puede fallar si no se typea bien.
        // Pero aquí $category viene injectado.
        // La variable en el argumento del método original era $category. Voy a mantener $category para consistencia interna si puedo,
        // pero replace_file_content reemplaza el bloque.
        // Espera, el argumento original era `destroy(CustomerCategory $category)`.

        $customer_category->deleted_by = auth()->id();
        $customer_category->save();
        $customer_category->delete();

        LoggerHelper::mutated('CustomerCategoryController', 'destroy', 'CustomerCategory', $customer_category->category_id);

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

        // Opcional: Eliminar info relacionada
        // $category->tourPrices()->forceDelete();

        $category->forceDelete();

        LoggerHelper::mutated('CustomerCategoryController', 'forceDelete', 'CustomerCategory', $id);

        return redirect()
            ->route('admin.customer_categories.trash')
            ->with('success', 'Categoría eliminada permanentemente.');
    }
}
