<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-taxes'])->only(['index']);
        $this->middleware(['can:create-taxes'])->only(['create', 'store']);
        $this->middleware(['can:edit-taxes'])->only(['edit', 'update']);
        $this->middleware(['can:publish-taxes'])->only(['toggle']);
        $this->middleware(['can:delete-taxes'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        // Checkbox handling
        $validated['is_inclusive'] = $request->has('is_inclusive');
        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);

        Tax::create($validated);

        return redirect()->route('admin.taxes.index')
            ->with('success', __('Impuesto creado correctamente.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
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

        // Checkbox handling
        $validated['is_inclusive'] = $request->has('is_inclusive');
        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);

        $tax->update($validated);

        return redirect()->route('admin.taxes.index')
            ->with('success', __('Impuesto actualizado correctamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('admin.taxes.index')
            ->with('success', __('Impuesto eliminado correctamente.'));
    }

    /**
     * Toggle active status
     */
    public function toggle(Tax $tax)
    {
        $tax->update(['is_active' => !$tax->is_active]);

        return back()->with('success', __('Estado actualizado correctamente.'));
    }
}
