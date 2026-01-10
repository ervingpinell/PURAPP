<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\PolicyTranslation;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;

/**
 * PolicyController
 *
 * Handles policy operations.
 */
class PolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-policies'])->only('index');
        $this->middleware(['can:create-policies'])->only(['create', 'store']);
        $this->middleware(['can:edit-policies'])->only(['edit', 'update']);
        $this->middleware(['can:publish-policies'])->only(['toggle']);
        $this->middleware(['can:delete-policies'])->only(['destroy', 'restore', 'forceDestroy']);
    }
    /**
     * Listado de categorías de políticas con filtros de estado:
     * - active (por defecto)
     * - inactive
     * - archived (solo papelera)
     * - all (incluye eliminadas)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');

        $base = Policy::query()
            ->with(['translations', 'sections'])
            ->withCount('sections');

        if ($status === 'archived') {
            $base->onlyTrashed();
        } elseif ($status === 'all') {
            $base->withTrashed();
        } elseif ($status === 'inactive') {
            $base->where('is_active', false);
        } else {
            // default: active
            $base->where('is_active', true);
        }

        $policies = $base
            ->orderByDesc('effective_from')
            ->orderBy('policy_id')
            ->get();

        return view('admin.policies.index', compact('policies', 'status'));
    }

    /** Crear base + traducción ES y (opcional) propagar a otros idiomas */
    public function store(Request $request, TranslatorInterface $translator)
    {
        $data = $request->validate([
            // Traducción inicial (ES)
            'name'           => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string'],

            // Campos base (tabla policies)
            'slug'           => ['nullable', 'string', 'max:255', 'unique:policies,slug'],
            'type'           => ['nullable', 'string', 'max:50', 'unique:policies,type'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],

            // Opcional
            'propagate'      => ['sometimes', 'boolean'],
        ]);

        // Normalizar base
        $base = [
            'slug'           => $data['slug'] ?? null,
            'type'           => !empty($data['type']) ? $data['type'] : null,
            'is_active'      => (bool)($data['is_active'] ?? false),
            'effective_from' => $data['effective_from'] ?? null,
            'effective_to'   => $data['effective_to'] ?? null,
        ];

        // 1) Crea la base (solo columnas de policies)
        $policy = Policy::create($base);

        // 2) Traducción ES en policies_translations
        PolicyTranslation::updateOrCreate(
            ['policy_id' => $policy->policy_id, 'locale' => 'es'],
            ['name' => $data['name'], 'content' => $data['content']]
        );

        // 3) Propagación opcional a EN/FR/DE/PT (pt_BR en DB)
        if (!empty($data['propagate'])) {
            try {
                $nameAll = (array)($translator->translateAll($data['name']) ?? []);
            } catch (\Throwable $e) {
                $nameAll = [];
            }

            try {
                $contAll = (array)($translator->translateAll($data['content']) ?? []);
            } catch (\Throwable $e) {
                $contAll = [];
            }

            foreach (['en', 'fr', 'de', 'pt'] as $lang) {
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



        LoggerHelper::mutated('PolicyController', 'store', 'Policy', $policy->policy_id);

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

            // Traducción (policies_translations)
            'name'           => ['nullable', 'string', 'max:255'],
            'content'        => ['nullable', 'string'],

            // Base (policies)
            'slug'           => ['nullable', 'string', 'max:255', 'unique:policies,slug,' . $policy->policy_id . ',policy_id'],
            'type'           => ['nullable', 'string', 'max:50', 'unique:policies,type,' . $policy->policy_id . ',policy_id'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],

            // Opcional
            'propagate'      => ['sometimes', 'boolean'],
        ]);

        $locale     = $validated['locale'] ?? app()->getLocale();
        $localeNorm = Policy::canonicalLocale($locale);
        $propagate  = (bool)($validated['propagate'] ?? false);

        // 1) Actualizar base (policies) — sin tocar traducciones
        $baseUpdates = [];
        foreach (['slug', 'type', 'is_active', 'effective_from', 'effective_to'] as $k) {
            if (array_key_exists($k, $validated)) {
                $baseUpdates[$k] = $validated[$k];
            }
        }
        if (!empty($baseUpdates)) {
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
            $tr->save();
        }

        // 3) Propagar a los demás idiomas si se solicitó.
        if ($propagate) {
            // Tomamos como fuente la traducción del locale actual (recién guardada si se envió)
            $source = PolicyTranslation::where([
                'policy_id' => $policy->policy_id,
                'locale'    => $localeNorm,
            ])->first();

            $sourceName    = $source?->name    ?? '';
            $sourceContent = $source?->content ?? '';

            try {
                $nameAll    = (array) ($translator->translateAll($sourceName)    ?? []);
            } catch (\Throwable $e) {
                $nameAll = [];
            }

            try {
                $contentAll = (array) ($translator->translateAll($sourceContent) ?? []);
            } catch (\Throwable $e) {
                $contentAll = [];
            }

            $targets = ['es', 'en', 'fr', 'pt', 'de'];
            $targets = array_values(array_diff($targets, [$locale]));

            foreach ($targets as $lang) {
                $norm = Policy::canonicalLocale($lang);
                PolicyTranslation::updateOrCreate(
                    ['policy_id' => $policy->policy_id, 'locale' => $norm],
                    [
                        'name'    => $nameAll[$lang]    ?? $sourceName,
                        'content' => $contentAll[$lang] ?? $sourceContent,
                    ]
                );
            }
        }



        LoggerHelper::mutated('PolicyController', 'update', 'Policy', $policy->policy_id);

        return back()->with('success', 'm_config.policies.updated');
    }

    /** Activar / desactivar categoría */
    public function toggle($policyId)
    {
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();
        $policy->update(['is_active' => ! $policy->is_active]);

        LoggerHelper::mutated('PolicyController', 'toggle', 'Policy', $policy->policy_id, ['is_active' => $policy->is_active]);

        return back()->with(
            'success',
            $policy->is_active
                ? 'm_config.policies.category_activated'
                : 'm_config.policies.category_deactivated'
        );
    }

    /**
     * Destroy = Soft delete → mover a papelera
     */
    public function destroy($policyId)
    {
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();
        $policy->delete(); // Soft delete

        LoggerHelper::mutated('PolicyController', 'destroy', 'Policy', $policy->policy_id);

        return redirect()
            ->route('admin.policies.index', ['status' => 'archived'])
            ->with('success', 'm_config.policies.moved_to_trash');
    }

    /**
     * Restaurar una policy desde la papelera
     */
    public function restore($policyId)
    {
        $policy = Policy::withTrashed()
            ->where('policy_id', $policyId)
            ->firstOrFail();

        $policy->restore();

        LoggerHelper::mutated('PolicyController', 'restore', 'Policy', $policy->policy_id);

        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'm_config.policies.restored_ok');
    }

    /**
     * Borrado definitivo (solo admins, protegido por Gate y ruta)
     */
    public function forceDestroy($policyId)
    {
        $policy = Policy::onlyTrashed()
            ->where('policy_id', $policyId)
            ->firstOrFail();

        // Limpiar relaciones dependientes antes del delete definitivo
        try {
            $policy->sections()->delete();
        } catch (\Throwable $e) {
        }

        try {
            PolicyTranslation::where('policy_id', $policyId)->delete();
        } catch (\Throwable $e) {
        }

        $policy->forceDelete();

        LoggerHelper::mutated('PolicyController', 'forceDestroy', 'Policy', $policyId);

        return redirect()
            ->route('admin.policies.index', ['status' => 'archived'])
            ->with('success', 'm_config.policies.deleted_permanently');
    }
}
