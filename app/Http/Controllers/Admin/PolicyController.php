<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Models\Policy;
use App\Models\PolicyTranslation;
use App\Services\Contracts\TranslatorInterface;

class PolicyController extends Controller
{
    public function __construct(
        protected TranslatorInterface $translator
    ) {}

    public function index(Request $request)
    {
        $query = Policy::query()
            ->withCount('sections')
            ->with('translations');

        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }
        if ($request->filled('from')) {
            $query->whereDate('effective_from', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('effective_to', '<=', $request->date('to'));
        }

        $policies = $query->orderByDesc('policy_id')->get();

        return view('admin.policies.index', compact('policies'));
    }

    public function store(Request $request)
    {
        $supportedLocales = array_keys(config('app.supported_locales', [
            'es'    => 'Español',
            'en'    => 'English',
            'pt_BR' => 'Português (Brasil)',
            'fr'    => 'Français',
            'de'    => 'Deutsch',
        ]));

        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_active'      => ['nullable', 'in:0,1'],

            'locale'         => ['nullable', Rule::in($supportedLocales)],
            'title'          => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($request) {
            $policy = Policy::create([
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_active'      => $request->boolean('is_active', true),
            ]);

            $baseLocale = Policy::canonicalLocale(
                (string) ($request->input('locale') ?: app()->getLocale())
            );

            PolicyTranslation::create([
                'policy_id' => $policy->policy_id,
                'locale'    => $baseLocale,
                'title'     => (string) $request->input('title'),
                'content'   => (string) $request->input('content'),
            ]);

            $this->translatePolicyIfMissing($policy, $baseLocale);

            return redirect()
                ->route('admin.policies.index')
                ->with('success', __('policies.category_created'));
        });
    }

    public function update(Request $request, Policy $policy)
    {
        $supportedLocales = array_keys(config('app.supported_locales', [
            'es'    => 'Español',
            'en'    => 'English',
            'pt_BR' => 'Português (Brasil)',
            'fr'    => 'Français',
            'de'    => 'Deutsch',
        ]));

        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_active'      => ['nullable', 'in:0,1'],

            'locale'         => ['nullable', Rule::in($supportedLocales)],
            'title'          => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($request, $policy) {
            $policy->update([
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_active'      => $request->boolean('is_active', true),
            ]);

            $targetLocale = Policy::canonicalLocale(
                (string) ($request->input('locale') ?: app()->getLocale())
            );

            $translation = PolicyTranslation::firstOrNew([
                'policy_id' => $policy->policy_id,
                'locale'    => $targetLocale,
            ]);

            $translation->title   = (string) $request->input('title');
            $translation->content = (string) $request->input('content');
            $translation->save();

            return back()->with('success', __('policies.category_updated'));
        });
    }

    public function toggle(Policy $policy)
    {
        $policy->update(['is_active' => ! $policy->is_active]);

        return back()->with(
            'success',
            $policy->is_active
                ? __('policies.category_activated')
                : __('policies.category_deactivated')
        );
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();

        return back()->with('success', __('policies.category_deleted'));
    }

    /**
     * Auto-translate to other supported locales when missing.
     */
    private function translatePolicyIfMissing(Policy $policy, string $baseLocale): void
    {
        $supportedLocales = array_keys(config('app.supported_locales', [
            'es'    => 'Español',
            'en'    => 'English',
            'pt_BR' => 'Português (Brasil)',
            'fr'    => 'Français',
            'de'    => 'Deutsch',
        ]));

        $baseTranslation = $policy->translations()->where('locale', $baseLocale)->first();
        if (! $baseTranslation) {
            return;
        }

        foreach ($supportedLocales as $targetLocale) {
            if ($targetLocale === $baseLocale) {
                continue;
            }

            $exists = $policy->translations()->where('locale', $targetLocale)->exists();
            if ($exists) {
                continue;
            }

            try {
                $translatedTitle = $this->translator->translate($baseTranslation->title, $targetLocale);
            } catch (\Throwable $e) {
                $translatedTitle = $baseTranslation->title;
            }

            try {
                $translatedContent = $this->translator->translate($baseTranslation->content, $targetLocale);
            } catch (\Throwable $e) {
                $translatedContent = $baseTranslation->content;
            }

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $policy->policy_id, 'locale' => $targetLocale],
                ['title' => $translatedTitle, 'content' => $translatedContent]
            );
        }
    }
}
