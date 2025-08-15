<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Policy;
use App\Models\PolicyTranslation;
use App\Services\Contracts\TranslatorInterface;

class PolicyController extends Controller
{
    public function __construct(
        protected TranslatorInterface $translator
    ) {
        // Opcional: middleware de admin
        // $this->middleware(['auth', 'can:manage-policies']);
    }

    /**
     * Listado de categorías con conteo de secciones (ADMIN).
     */
    public function index(Request $request): View
    {
        $q = Policy::query()
            ->withCount('sections')
            ->with('translations');

        if ($request->filled('active')) {
            $q->where('is_active', $request->boolean('active'));
        }
        if ($request->filled('from')) {
            $q->whereDate('effective_from', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('effective_to', '<=', $request->date('to'));
        }

        $policies = $q->orderByDesc('policy_id')->get();

        return view('admin.policies.index', compact('policies'));
    }

    /**
     * Crear categoría + traducción base (DeepL SOLO en create).
     */
    public function store(Request $request): RedirectResponse
    {
        $allowedLocales = array_keys(config('app.supported_locales', [
            'es' => 'Español', 'en' => 'English', 'pt_BR' => 'Português (Brasil)', 'fr' => 'Français', 'de' => 'Deutsch',
        ]));

        $request->validate([
            'name'           => ['required','string','max:255'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'is_active'      => ['nullable','in:0,1'],

            'locale'         => ['nullable', Rule::in($allowedLocales)],
            'title'          => ['required','string','max:255'],
            'content'        => ['required','string'],
        ]);

        return DB::transaction(function () use ($request) {
            $policy = Policy::create([
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_active'      => $request->boolean('is_active', true),
            ]);

            $baseLocale = (string) ($request->input('locale') ?: app()->getLocale());

            PolicyTranslation::create([
                'policy_id' => $policy->policy_id,
                'locale'    => $baseLocale,
                'title'     => (string) $request->input('title'),
                'content'   => (string) $request->input('content'),
            ]);

            // DeepL SOLO aquí
            $this->translatePolicyIfMissing($policy, $baseLocale);

            return redirect()
                ->route('admin.policies.index')
                ->with('success', '✅ Categoría creada y traducida.');
        });
    }

    /**
     * Editar categoría + traducción del locale actual (SIN DeepL).
     */
    public function update(Request $request, Policy $policy): RedirectResponse
    {
        $allowedLocales = array_keys(config('app.supported_locales', [
            'es'=>'Español','en'=>'English','pt_BR'=>'Português (Brasil)','fr'=>'Français','de'=>'Deutsch',
        ]));

        $request->validate([
            'name'           => ['required','string','max:255'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'is_active'      => ['nullable','in:0,1'],

            'locale'         => ['nullable', Rule::in($allowedLocales)],
            'title'          => ['required','string','max:255'],
            'content'        => ['required','string'],
        ]);

        return DB::transaction(function () use ($request, $policy) {
            $policy->update([
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_active'      => $request->boolean('is_active', true),
            ]);

            $locale = (string) ($request->input('locale') ?: app()->getLocale());

            $tr = PolicyTranslation::firstOrNew([
                'policy_id' => $policy->policy_id,
                'locale'    => $locale,
            ]);
            $tr->title   = (string) $request->input('title');
            $tr->content = (string) $request->input('content');
            $tr->save();

            // SIN DeepL en update
            return back()->with('success', '✅ Categoría actualizada.');
        });
    }

    /**
     * Activar/Desactivar categoría (ADMIN).
     */
    public function toggle(Policy $policy): RedirectResponse
    {
        $policy->update(['is_active' => !$policy->is_active]);

        return back()->with(
            'success',
            $policy->is_active ? '✅ Categoría activada' : '⚠️ Categoría desactivada'
        );
    }

    /**
     * Eliminar categoría (borra también secciones por FK) (ADMIN).
     */
    public function destroy(Policy $policy): RedirectResponse
    {
        $policy->delete();
        return back()->with('success', '🗑️ Categoría eliminada.');
    }

    /**
     * DeepL helper: crear traducciones faltantes (sólo en store()).
     */
    private function translatePolicyIfMissing(Policy $policy, string $baseLocale): void
    {
        $supported = array_keys(config('app.supported_locales', [
            'es'=>'Español','en'=>'English','pt_BR'=>'Português (Brasil)','fr'=>'Français','de'=>'Deutsch',
        ]));

        $base = $policy->translations()->where('locale', $baseLocale)->first();
        if (!$base) return;

        foreach ($supported as $target) {
            if ($target === $baseLocale) continue;

            $existing = $policy->translations()->where('locale', $target)->first();
            if ($existing) continue;

            try { $titleT = $this->translator->translate($base->title, $target); }
            catch (\Throwable $e) { $titleT = $base->title; }

            try { $contentT = $this->translator->translate($base->content, $target); }
            catch (\Throwable $e) { $contentT = $base->content; }

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $policy->policy_id, 'locale' => $target],
                ['title' => $titleT, 'content' => $contentT]
            );
        }
    }
}
