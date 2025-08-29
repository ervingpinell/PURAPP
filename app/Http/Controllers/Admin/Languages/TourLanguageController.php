<?php

namespace App\Http\Controllers\Admin\Languages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourLanguage;
use Illuminate\Validation\Rule;

class TourLanguageController extends Controller
{
    public function index()
    {
        $languages = TourLanguage::orderBy('name')->get();
        return view('admin.languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
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
                'required', 'string', 'max:255',
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

    /**
     * (Opcional) Borrado real – dejado comentado.
     */
    /*
    public function destroy(TourLanguage $language)
    {
        $name = $language->name;
        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('success', __('m_tours.language.ui.flash.deleted_title') . " «{$name}»");
    }
    */
}
