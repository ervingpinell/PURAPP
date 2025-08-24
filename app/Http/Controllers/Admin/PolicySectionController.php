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

        return view('admin.policies.sections.index', compact('policy', 'sections'));
    }

    public function store(Request $request, Policy $policy)
    {
        $supportedLocales = array_keys(config('app.supported_locales', [
            'es' => 'Español', 'en' => 'English', 'pt_BR' => 'Português (Brasil)', 'fr' => 'Français', 'de' => 'Deutsch',
        ]));

        $request->validate([
            'key'        => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'in:0,1'],

            'locale'     => ['nullable', Rule::in($supportedLocales)],
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
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

            $this->createMissingTranslations($section, $baseLocale);

            return back()->with('success', __('policies.section_created'));
        });
    }

    public function update(Request $request, Policy $policy, PolicySection $section)
    {
        if ($section->policy_id !== $policy->policy_id) {
            abort(404);
        }

        $supportedLocales = array_keys(config('app.supported_locales', [
            'es' => 'Español', 'en' => 'English', 'pt_BR' => 'Português (Brasil)', 'fr' => 'Français', 'de' => 'Deutsch',
        ]));

        $request->validate([
            'key'        => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'in:0,1'],

            'locale'     => ['nullable', Rule::in($supportedLocales)],
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($request, $section) {
            $section->update([
                'key'        => $request->input('key'),
                'sort_order' => (int) $request->input('sort_order', 0),
                'is_active'  => $request->boolean('is_active', true),
            ]);

            $targetLocale = PolicySection::canonicalLocale(
                (string) ($request->input('locale') ?: app()->getLocale())
            );

            $translation = PolicySectionTranslation::firstOrNew([
                'section_id' => $section->section_id,
                'locale'     => $targetLocale,
            ]);

            $translation->title   = (string) $request->input('title');
            $translation->content = (string) $request->input('content');
            $translation->save();

            return back()->with('success', __('policies.section_updated'));
        });
    }

    public function toggle(Policy $policy, PolicySection $section)
    {
        if ($section->policy_id !== $policy->policy_id) {
            abort(404);
        }

        $section->update(['is_active' => ! $section->is_active]);

        return back()->with(
            'success',
            $section->is_active
                ? __('policies.section_activated')
                : __('policies.section_deactivated')
        );
    }

    public function destroy(Policy $policy, PolicySection $section)
    {
        if ($section->policy_id !== $policy->policy_id) {
            abort(404);
        }

        $section->delete();

        return back()->with('success', __('policies.section_deleted'));
    }

    /**
     * Auto-create missing translations for other supported locales using the translator service.
     */
    private function createMissingTranslations(PolicySection $section, string $baseLocale): void
    {
        $supportedLocales = array_keys(config('app.supported_locales', [
            'es' => 'Español', 'en' => 'English', 'pt_BR' => 'Português (Brasil)', 'fr' => 'Français', 'de' => 'Deutsch',
        ]));

        $base = $section->translations()->where('locale', $baseLocale)->first();
        if (! $base) {
            return;
        }

        foreach ($supportedLocales as $targetLocale) {
            if ($targetLocale === $baseLocale) {
                continue;
            }

            $alreadyExists = $section->translations()->where('locale', $targetLocale)->exists();
            if ($alreadyExists) {
                continue;
            }

            try {
                $translatedTitle = $this->translator->translate($base->title, $targetLocale);
            } catch (\Throwable $e) {
                $translatedTitle = $base->title;
            }

            try {
                $translatedContent = $this->translator->translate($base->content, $targetLocale);
            } catch (\Throwable $e) {
                $translatedContent = $base->content;
            }

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $section->section_id, 'locale' => $targetLocale],
                ['title' => $translatedTitle, 'content' => $translatedContent]
            );
        }
    }
}
