<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\PolicyTranslation;
use App\Services\Contracts\TranslatorInterface;

class PolicyController extends Controller
{
    public function index()
    {
        // Carga relaciones para usar accessors display_* en Blade
        $policies = Policy::query()
            ->with(['translations', 'sections'])
            ->withCount('sections')
            ->orderByDesc('effective_from')
            ->get();

        return view('admin.policies.index', compact('policies'));
    }

    /** Crear base + traducción ES y (opcional) propagar a otros idiomas */
    public function store(Request $request, TranslatorInterface $translator)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:policies,slug'],
            'content'        => ['required', 'string'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
            'propagate'      => ['sometimes', 'boolean'], // <- checkbox opcional
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        // 1) Crea la base
        $policy = Policy::create($data);

        // 2) Asegura traducción ES
        PolicyTranslation::updateOrCreate(
            ['policy_id' => $policy->policy_id, 'locale' => 'es'],
            ['name' => $data['name'], 'content' => $data['content']]
        );

        // 3) Propagación opcional a EN/FR/DE/PT (pt_BR en DB)
        if (!empty($data['propagate'])) {
            try { $nameAll = (array)($translator->translateAll($data['name']) ?? []); }
            catch (\Throwable $e) { $nameAll = []; }
            try { $contAll = (array)($translator->translateAll($data['content']) ?? []); }
            catch (\Throwable $e) { $contAll = []; }

            foreach (['en','fr','de','pt'] as $lang) {
                $norm = Policy::canonicalLocale($lang);
                PolicyTranslation::updateOrCreate(
                    ['policy_id' => $policy->policy_id, 'locale' => $norm],
                    [
                        'name'    => $nameAll[$lang]  ?? $data['name'],
                        'content' => $contAll[$lang] ?? $data['content'],
                    ]
                );
            }
        }

        return back()->with('success', 'm_config.policies.created');
    }

    /** Editar base + traducción del locale actual, con propagación opcional */
    public function update(
        Request $request,
        $policyId,
        TranslatorInterface $translator
    ) {
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();

        $validated = $request->validate([
            'locale'         => ['nullable', 'in:es,en,fr,pt,de'],
            'name'           => ['nullable', 'string', 'max:255'], // traducción o base (si update_base)
            'content'        => ['nullable', 'string'],            // idem
            'slug'           => ['nullable', 'string', 'max:255', 'unique:policies,slug,' . $policy->policy_id . ',policy_id'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
            'propagate'      => ['sometimes', 'boolean'],
            'update_base'    => ['sometimes', 'boolean'],
        ]);

        $locale      = $validated['locale'] ?? app()->getLocale();
        $localeNorm  = Policy::canonicalLocale($locale);
        $propagate   = (bool) ($validated['propagate']   ?? false);
        $updateBase  = (bool) ($validated['update_base'] ?? false);

        // 1) Actualizar base (sin tocar traducción salvo que update_base=true)
        $baseUpdates = [];
        foreach (['slug','is_active','effective_from','effective_to'] as $k) {
            if (array_key_exists($k, $validated)) {
                $baseUpdates[$k] = $validated[$k];
            }
        }
        if ($updateBase) {
            if (isset($validated['name']))    $baseUpdates['name']    = $validated['name'];
            if (isset($validated['content'])) $baseUpdates['content'] = $validated['content'];
        }
        if (!empty($baseUpdates)) {
            // casts/booleans
            if (array_key_exists('is_active', $baseUpdates)) {
                $baseUpdates['is_active'] = (bool)$baseUpdates['is_active'];
            }
            $policy->update($baseUpdates);
        }

        // 2) Actualizar / crear traducción del locale actual
        $trName    = $validated['name']    ?? null;
        $trContent = $validated['content'] ?? null;
        if ($trName !== null || $trContent !== null) {
            $tr = PolicyTranslation::firstOrNew([
                'policy_id' => $policy->policy_id,
                'locale'    => $localeNorm,
            ]);
            if ($trName !== null)    $tr->name    = $trName;
            if ($trContent !== null) $tr->content = $trContent;

            if (!$tr->exists) {
                if ($trName === null)    $tr->name    = (string) ($policy->name ?? '');
                if ($trContent === null) $tr->content = (string) ($policy->content ?? '');
            }
            $tr->save();
        }

        // 3) Propagar a los demás idiomas si se solicitó
        if ($propagate) {
            $sourceName    = $trName    ?? (string) ($policy->name ?? '');
            $sourceContent = $trContent ?? (string) ($policy->content ?? '');

            try { $nameAll    = (array) ($translator->translateAll($sourceName)    ?? []); }
            catch (\Throwable $e) { $nameAll = []; }

            try { $contentAll = (array) ($translator->translateAll($sourceContent) ?? []); }
            catch (\Throwable $e) { $contentAll = []; }

            $targets = ['es','en','fr','pt','de'];
            $targets = array_values(array_diff($targets, [$locale]));

            foreach ($targets as $lang) {
                $norm = Policy::canonicalLocale($lang);
                $ptr = PolicyTranslation::firstOrNew([
                    'policy_id' => $policy->policy_id,
                    'locale'    => $norm,
                ]);

                $ptr->name    = $nameAll[$lang]    ?? $sourceName;
                $ptr->content = $contentAll[$lang] ?? $sourceContent;
                $ptr->save();
            }
        }

        return back()->with('success', 'm_config.policies.updated');
    }

    public function toggle($policyId)
    {
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();
        $policy->update(['is_active' => ! $policy->is_active]);

        return back()->with(
            'success',
            $policy->is_active
                ? 'm_config.policies.category_activated'
                : 'm_config.policies.category_deactivated'
        );
    }

    public function destroy($policyId)
    {
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();
        $policy->delete();

        return back()->with('success', 'm_config.policies.deleted');
    }
}
