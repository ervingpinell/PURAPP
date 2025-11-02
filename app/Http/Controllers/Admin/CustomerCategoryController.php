<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use App\Http\Requests\Tour\CustomerCategory\StoreCustomerCategoryRequest;
use Illuminate\Http\Request;

class CustomerCategoryController extends Controller
{
    public function index()
    {
        $categories = CustomerCategory::ordered()->paginate(20);

        return view('admin.customer_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.customer_categories.create');
    }

    public function store(StoreCustomerCategoryRequest $request)
    {
        $category = CustomerCategory::create($request->validated());

        return redirect()
            ->route('admin.customer_categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(CustomerCategory $category)
    {
        return view('admin.customer_categories.edit', compact('category'));
    }

    public function update(StoreCustomerCategoryRequest $request, CustomerCategory $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.customer_categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function toggle(CustomerCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy(CustomerCategory $category)
    {
        // Verificar si está en uso (esto lo validaremos en pasos posteriores)
        $category->delete();

        return redirect()
            ->route('admin.customer_categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}
