<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Category::create([
            'name' => $request->name,
            'is_active' => true
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría creada exitosamente.')
            ->with('alert_type', 'creado');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada correctamente.')
            ->with('alert_type', 'actualizado');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $accion = $category->is_active ? 'activado' : 'desactivado';

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría ' . $accion . ' correctamente.')
            ->with('alert_type', $accion);
    }
}
