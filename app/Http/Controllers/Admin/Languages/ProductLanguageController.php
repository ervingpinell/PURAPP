<?php

namespace App\Http\Controllers\Admin\Languages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductLanguage;
use Illuminate\Validation\Rule;

/**
 * ProductLanguageController
 *
 * Handles productlanguage operations.
 */
class ProductLanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-product-languages'])->only(['index']);
        $this->middleware(['can:edit-product-languages'])->only(['update']);
        $this->middleware(['can:publish-product-languages'])->only(['toggle']);
    }
    public function index()
    {
        $languages = ProductLanguage::orderBy('name')->get();
        return view('admin.languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:product_languages,name',
            ],
        ], [
            'name.required' => __('m_products.language.validation.name.required'),
            'name.unique'   => __('m_products.language.validation.name.unique'),
        ]);

        ProductLanguage::create([
            'name'      => $request->string('name')->trim(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_products.language.success.created'));
    }

    public function update(Request $request, ProductLanguage $language)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_languages', 'name')
                    ->ignore($language->getKey(), 'product_language_id'),
            ],
        ], [
            'name.required' => __('m_products.language.validation.name.required'),
            'name.unique'   => __('m_products.language.validation.name.unique'),
        ]);

        $language->update([
            'name' => $request->string('name')->trim(),
        ]);

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_products.language.success.updated'));
    }

    public function toggle(ProductLanguage $language)
    {
        $language->update([
            'is_active' => ! $language->is_active,
        ]);

        $key = $language->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_products.language.success.' . $key));
    }

    public function trash(Request $request)
    {
        $this->authorize('restore-product-languages');

        $trashedLanguages = ProductLanguage::onlyTrashed()
            ->with(['deletedBy'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.languages.trash', compact('trashedLanguages'));
    }

    public function destroy(ProductLanguage $language)
    {
        // Soft delete
        $language->update(['deleted_by' => auth()->id()]);
        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_products.language.success.deleted'));
    }

    public function restore($id)
    {
        $this->authorize('restore-product-languages');

        $language = ProductLanguage::onlyTrashed()->findOrFail($id);
        $language->restore();

        return redirect()
            ->route('admin.languages.trash')
            ->with('success', __('m_products.language.success.restored') ?? 'Language restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->authorize('force-delete-product-languages');

        $language = ProductLanguage::onlyTrashed()->findOrFail($id);
        $language->forceDelete();

        return redirect()
            ->route('admin.languages.trash')
            ->with('success', __('m_products.language.success.force_deleted') ?? 'Language permanently deleted.');
    }
}
