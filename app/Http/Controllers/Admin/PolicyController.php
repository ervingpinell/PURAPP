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
        $slug = $data['slug'] ?? null;
        if (empty($slug) && !empty($data['name'])) {
            $slug = \Illuminate\Support\Str::slug($data['name']);
        }

        $base = [
            'slug'           => $slug,
            'type'           => !empty($data['type']) ? $data['type'] : null,
            'is_active'      => (bool)($data['is_active'] ?? false),
            'effective_from' => $data['effective_from'] ?? null,
            'effective_to'   => $data['effective_to'] ?? null,
        ];

        // 1) Crea la base (solo columnas de policies)
        $policy = Policy::create($base);

        // 2) Generar traducciones (auto-translate)
        // Asumimos que el input 'name' y 'content' están en ES (o idioma base)
        $names    = $translator->translateAll($data['name']);       // ['es'=>'...', 'en'=>'...', ...]
        $contents = $translator->translateAll($data['content']);    // ['es'=>'...', 'en'=>'...', ...]

        foreach ($names as $locale => $transName) {
             $transContent = $contents[$locale] ?? '';
             
             if (!empty($transName)) {
                 PolicyTranslation::updateOrCreate(
                     ['policy_id' => $policy->policy_id, 'locale' => $locale],
                     ['name' => $transName, 'content' => $transContent]
                 );
             }
        }



        LoggerHelper::mutated('PolicyController', 'store', 'Policy', $policy->policy_id);

        return back()->with('success', 'm_config.policies.created');
    }

    /** Editar base + traducciones (ES, EN, FR, PT, DE) */
    public function update(
        Request $request,
        $policyId,
        TranslatorInterface $translator
    ) {
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();

        $validated = $request->validate([
            // Validar array de traducciones
            'translations'             => ['required', 'array'],
            'translations.*.name'      => ['nullable', 'string', 'max:255'],
            'translations.*.content'   => ['nullable', 'string'],

            // Base (policies)
            'slug'           => ['nullable', 'string', 'max:255', 'unique:policies,slug,' . $policy->policy_id . ',policy_id'],
            'type'           => ['nullable', 'string', 'max:50', 'unique:policies,type,' . $policy->policy_id . ',policy_id'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
        ]);

        // 1) Actualizar base (policies)
        $baseUpdates = [];
        foreach (['slug', 'type', 'is_active', 'effective_from', 'effective_to'] as $k) {
            if (array_key_exists($k, $validated)) {
                $baseUpdates[$k] = $validated[$k];
            }
        }

        // Auto-generate slug if cleared or empty
        if (array_key_exists('slug', $baseUpdates) && empty($baseUpdates['slug'])) {
            // Try to get name from input update or fallback to existing translation
            $nameForSlug = $validated['translations']['es']['name'] 
                ?? $policy->translation('es')?->name 
                ?? null;

            if ($nameForSlug) {
                $baseUpdates['slug'] = \Illuminate\Support\Str::slug($nameForSlug);
            }
        }

        if (!empty($baseUpdates)) {
            if (array_key_exists('is_active', $baseUpdates)) {
                $baseUpdates['is_active'] = (bool)$baseUpdates['is_active'];
            }
            $policy->update($baseUpdates);
        }

        // 2) Actualizar traducciones
        $inputTrans = $validated['translations'] ?? [];

        // --- Auto-translate logic (fill gaps) ---
        // Find source (prefer 'es')
        $sourceName = $inputTrans['es']['name'] ?? null;
        $sourceContent = $inputTrans['es']['content'] ?? null;

        // If 'es' missing, try first available
        if (!$sourceName) {
            foreach ($inputTrans as $locale => $d) {
                if (!empty($d['name'])) {
                    $sourceName = $d['name'];
                    $sourceContent = $d['content'] ?? '';
                    break; 
                }
            }
        }

        if ($sourceName) {
            $transNames = $translator->translateAll($sourceName);
            // Translate content only if we have source content
            $transContents = $sourceContent ? $translator->translateAll($sourceContent) : [];

            // Fill gaps in inputTrans
            foreach ($transNames as $loc => $val) {
                // If input for this locale is missing 'name', fill it
                if (empty($inputTrans[$loc]['name']) && !empty($val)) {
                    $inputTrans[$loc]['name'] = $val;
                    // Also fill content if available
                    if (empty($inputTrans[$loc]['content']) && !empty($transContents[$loc])) {
                        $inputTrans[$loc]['content'] = $transContents[$loc];
                    }
                }
            }
        }

        if (!empty($inputTrans)) {
            foreach ($inputTrans as $locale => $data) {
                // Normalizar locale (aunque venga como 'es', 'en', etc.)
                $norm = Policy::canonicalLocale($locale);

                $tName = $data['name'] ?? null;
                $tContent = $data['content'] ?? null;

                // Si al menos uno tiene valor, guardamos/actualizamos
                if ($tName || $tContent) {
                    PolicyTranslation::updateOrCreate(
                        ['policy_id' => $policy->policy_id, 'locale' => $norm],
                        [
                            'name'    => $tName ?? '',
                            'content' => $tContent ?? '',
                        ]
                    );
                }
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
        $policy->update(['deleted_by' => auth()->id()]);
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
