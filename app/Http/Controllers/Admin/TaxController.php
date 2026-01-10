<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

/**
 * TaxController
 *
 * Handles tax operations.
 */

use App\Services\LoggerHelper;

/**
 * TaxController
 *
 * Handles tax operations.
 */
class TaxController extends Controller
{
    // ...

    public function store(Request $request)
    {
        // ... validation ...
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:taxes,code',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'apply_to' => 'required|in:per_person,subtotal,total',
            'is_inclusive' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_inclusive'] = $request->has('is_inclusive');
        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);

        $tax = Tax::create($validated);

        LoggerHelper::mutated('TaxController', 'store', 'Tax', $tax->tax_id);

        return redirect()->route('admin.taxes.index')
            ->with('success', __('Impuesto creado correctamente.'));
    }

    public function update(Request $request, Tax $tax)
    {
        // ... validation ...
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:taxes,code,' . $tax->tax_id . ',tax_id',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'apply_to' => 'required|in:per_person,subtotal,total',
            'is_inclusive' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_inclusive'] = $request->has('is_inclusive');
        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);

        $tax->update($validated);

        LoggerHelper::mutated('TaxController', 'update', 'Tax', $tax->tax_id);

        return redirect()->route('admin.taxes.index')
            ->with('success', __('Impuesto actualizado correctamente.'));
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        LoggerHelper::mutated('TaxController', 'destroy', 'Tax', $tax->tax_id);

        return redirect()->route('admin.taxes.index')
            ->with('success', __('Impuesto eliminado correctamente.'));
    }

    public function toggle(Tax $tax)
    {
        $tax->update(['is_active' => !$tax->is_active]);

        LoggerHelper::mutated('TaxController', 'toggle', 'Tax', $tax->tax_id, ['is_active' => $tax->is_active]);

        return back()->with('success', __('Estado actualizado correctamente.'));
    }
}
