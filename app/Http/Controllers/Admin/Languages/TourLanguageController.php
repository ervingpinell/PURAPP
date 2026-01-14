<?php

namespace App\Http\Controllers\Admin\Languages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourLanguage;
use Illuminate\Validation\Rule;

/**
 * TourLanguageController
 *
 * Handles tourlanguage operations.
 */
class TourLanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-tour-languages'])->only(['index']);
        $this->middleware(['can:edit-tour-languages'])->only(['update']);
        $this->middleware(['can:publish-tour-languages'])->only(['toggle']);
    }
    public function index()
    {
        $languages = TourLanguage::orderBy('name')->get();
        return view('admin.languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tour_languages,name',
            ],
        ], [
            'name.required' => __('m_tours.language.validation.name.required'),
            'name.unique'   => __('m_tours.language.validation.name.unique'),
        ]);

        TourLanguage::create([
            'name'      => $request->string('name')->trim(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_tours.language.success.created'));
    }

    public function update(Request $request, TourLanguage $language)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tour_languages', 'name')
                    ->ignore($language->getKey(), 'tour_language_id'),
            ],
        ], [
            'name.required' => __('m_tours.language.validation.name.required'),
            'name.unique'   => __('m_tours.language.validation.name.unique'),
        ]);

        $language->update([
            'name' => $request->string('name')->trim(),
        ]);

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_tours.language.success.updated'));
    }

    public function toggle(TourLanguage $language)
    {
        $language->update([
            'is_active' => ! $language->is_active,
        ]);

        $key = $language->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_tours.language.success.' . $key));
    }

    public function trash(Request $request)
    {
        $this->authorize('restore-tour-languages');

        $trashedLanguages = TourLanguage::onlyTrashed()
            ->with(['deletedBy'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.languages.trash', compact('trashedLanguages'));
    }

    public function destroy(TourLanguage $language)
    {
        // Soft delete
        $language->update(['deleted_by' => auth()->id()]);
        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_tours.language.success.deleted'));
    }

    public function restore($id)
    {
        $this->authorize('restore-tour-languages');

        $language = TourLanguage::onlyTrashed()->findOrFail($id);
        $language->restore();

        return redirect()
            ->route('admin.languages.trash')
            ->with('success', __('m_tours.language.success.restored') ?? 'Language restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->authorize('force-delete-tour-languages');

        $language = TourLanguage::onlyTrashed()->findOrFail($id);
        $language->forceDelete();

        return redirect()
            ->route('admin.languages.trash')
            ->with('success', __('m_tours.language.success.force_deleted') ?? 'Language permanently deleted.');
    }
}
