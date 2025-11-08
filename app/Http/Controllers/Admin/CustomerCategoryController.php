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

class CustomerCategoryController extends Controller
{
    public function index()
    {
        $categories = CustomerCategory::with('translations')->ordered()->paginate(20);
        return view('admin.customer_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.customer_categories.create');
    }

    public function store(StoreCustomerCategoryRequest $request, DeepLTranslator $translator)
    {
        $locales = supported_locales();
        $names   = $request->input('names', []);
        $auto    = (bool) $request->boolean('auto_translate', true);

        DB::transaction(function () use ($request, $translator, $locales, $names, $auto) {
            // 1) Crear categoría base
            $category = CustomerCategory::create($request->safe()->only([
                'slug','age_from','age_to','order','is_active'
            ]));

            // 2) Preparar nombres: completar los vacíos si se pidió auto-translate
            $firstLocale = $locales[0] ?? 'es';
            $seedText    = trim((string)($names[$firstLocale] ?? ''));
            if ($seedText === '') {
                // fallback: primera clave no vacía
                foreach ($locales as $l) {
                    if (!empty($names[$l])) { $seedText = trim($names[$l]); $firstLocale = $l; break; }
                }
            }

            if ($auto && $seedText !== '') {
                $detected = $translator->detect($seedText) ?? $firstLocale;
                foreach ($locales as $l) {
                    if (empty($names[$l])) {
                        // Si el target es igual al detectado, no traducir (usar seed)
                        $names[$l] = ($l === substr($detected,0,2))
                            ? $seedText
                            : $translator->translate($seedText, $l);
                    }
                }
            }

            // 3) Insertar traducciones (solo las que tengan contenido)
            foreach ($locales as $l) {
                $val = trim((string)($names[$l] ?? ''));
                if ($val === '') continue;

                CustomerCategoryTranslation::updateOrCreate(
                    ['category_id' => $category->category_id, 'locale' => $l],
                    ['name' => $val]
                );
            }
        });

        return redirect()
            ->route('admin.customer_categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(CustomerCategory $category)
    {
        $category->load('translations');
        return view('admin.customer_categories.edit', compact('category'));
    }

    public function update(StoreCustomerCategoryRequest $request, CustomerCategory $category, DeepLTranslator $translator)
    {
        $locales = supported_locales();
        $names   = $request->input('names', []);
        $regen   = (bool) $request->boolean('regen_missing', false);

        DB::transaction(function () use ($request, $category, $translator, $locales, $names, $regen) {
            // 1) Actualizar base
            $category->update($request->safe()->only([
                'slug','age_from','age_to','order','is_active'
            ]));

            // 2) Guardar/actualizar traducciones provistas
            foreach ($locales as $l) {
                $val = trim((string)($names[$l] ?? ''));
                if ($val === '') continue;

                CustomerCategoryTranslation::updateOrCreate(
                    ['category_id' => $category->category_id, 'locale' => $l],
                    ['name' => $val]
                );
            }

            // 3) Rellenar vacíos (opcional) sin pisar existentes
            if ($regen) {
                // Texto semilla: prioriza el primero que exista en DB o request
                $seedText = null;
                foreach ($locales as $l) {
                    $seedText = $seedText
                        ?? trim((string)($names[$l] ?? ''))
                        ?: optional($category->translations->firstWhere('locale',$l))->name;
                    if ($seedText) { $seedLocale = $l; break; }
                }

                if (!empty($seedText)) {
                    $detected = $translator->detect($seedText) ?? ($seedLocale ?? 'es');
                    foreach ($locales as $l) {
                        $exists = $category->translations->firstWhere('locale', $l);
                        if ($exists && !empty($exists->name)) continue; // no pisar
                        if (!empty($names[$l])) continue; // ya vino en request

                        $val = ($l === substr($detected,0,2))
                            ? $seedText
                            : $translator->translate($seedText, $l);

                        CustomerCategoryTranslation::updateOrCreate(
                            ['category_id' => $category->category_id, 'locale' => $l],
                            ['name' => $val]
                        );
                    }
                }
            }
        });

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
        $category->delete();
        return redirect()
            ->route('admin.customer_categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}
