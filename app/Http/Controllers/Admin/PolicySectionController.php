<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Policy;
use App\Models\PolicySection;
use App\Models\PolicySectionTranslation;
use App\Services\Contracts\TranslatorInterface;

class PolicySectionController extends Controller
{
    public function __construct(
        protected TranslatorInterface $translator
    ) {}

    public function index(Policy $policy)
    {
        $sections = $policy->sections()
            ->withoutGlobalScopes()
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return view('admin.policies.sections.index', compact('policy','sections'));
    }

    public function store(Request $request, Policy $policy)
    {
        $allowedLocales = array_keys(config('app.supported_locales', [
            'es'=>'Español','en'=>'English','pt_BR'=>'Português (Brasil)','fr'=>'Français','de'=>'Deutsch',
        ]));

        $request->validate([
            'key'        => ['nullable','string','max:100'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['nullable','in:0,1'],

            'locale'     => ['nullable', Rule::in($allowedLocales)],
            'title'      => ['required','string','max:255'],
            'content'    => ['required','string'],
        ]);

        return DB::transaction(function () use ($request, $policy) {
            $section = PolicySection::create([
                'policy_id'  => $policy->policy_id,
                'key'        => $request->input('key'),
                'sort_order' => (int) $request->input('sort_order', 0),
                'is_active'  => $request->boolean('is_active', true),
            ]);

            $baseLocale = PolicySection::canonicalLocale(
                (string) ($request->input('locale') ?: app()->getLocale())
            );

            PolicySectionTranslation::create([
                'section_id' => $section->section_id,
                'locale'     => $baseLocale,
                'title'      => (string) $request->input('title'),
                'content'    => (string) $request->input('content'),
            ]);

            $this->translateSectionIfMissing($section, $baseLocale);

            return back()->with('success', '✅ Sección creada y traducida.');
        });
    }

    public function update(Request $request, Policy $policy, PolicySection $section)
    {
        if ($section->policy_id !== $policy->policy_id) abort(404);

        $allowedLocales = array_keys(config('app.supported_locales', [
            'es'=>'Español','en'=>'English','pt_BR'=>'Português (Brasil)','fr'=>'Français','de'=>'Deutsch',
        ]));

        $request->validate([
            'key'        => ['nullable','string','max:100'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_active'  => ['nullable','in:0,1'],

            'locale'     => ['nullable', Rule::in($allowedLocales)],
            'title'      => ['required','string','max:255'],
            'content'    => ['required','string'],
        ]);

        return DB::transaction(function () use ($request, $section) {
            $section->update([
                'key'        => $request->input('key'),
                'sort_order' => (int) $request->input('sort_order', 0),
                'is_active'  => $request->boolean('is_active', true),
            ]);

            $locale = PolicySection::canonicalLocale(
                (string) ($request->input('locale') ?: app()->getLocale())
            );

            $tr = PolicySectionTranslation::firstOrNew([
                'section_id' => $section->section_id,
                'locale'     => $locale,
            ]);
            $tr->title   = (string) $request->input('title');
            $tr->content = (string) $request->input('content');
            $tr->save();

            return back()->with('success', '✅ Sección actualizada.');
        });
    }

    public function toggle(Policy $policy, PolicySection $section)
    {
        if ($section->policy_id !== $policy->policy_id) abort(404);

        $section->update(['is_active' => !$section->is_active]);

        return back()->with(
            'success',
            $section->is_active ? '✅ Sección activada' : '⚠️ Sección desactivada'
        );
    }

    public function destroy(Policy $policy, PolicySection $section)
    {
        if ($section->policy_id !== $policy->policy_id) abort(404);

        $section->delete();

        return back()->with('success', '🗑️ Sección eliminada.');
    }

    private function translateSectionIfMissing(PolicySection $section, string $baseLocale): void
    {
        $supported = array_keys(config('app.supported_locales', [
            'es'=>'Español','en'=>'English','pt_BR'=>'Português (Brasil)','fr'=>'Français','de'=>'Deutsch',
        ]));

        $base = $section->translations()->where('locale', $baseLocale)->first();
        if (!$base) return;

        foreach ($supported as $target) {
            if ($target === $baseLocale) continue;

            $existing = $section->translations()->where('locale', $target)->first();
            if ($existing) continue;

            try { $titleT = $this->translator->translate($base->title, $target); }
            catch (\Throwable $e) { $titleT = $base->title; }

            try { $contentT = $this->translator->translate($base->content, $target); }
            catch (\Throwable $e) { $contentT = $base->content; }

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $section->section_id, 'locale' => $target],
                ['title' => $titleT, 'content' => $contentT]
            );
        }
    }
}
