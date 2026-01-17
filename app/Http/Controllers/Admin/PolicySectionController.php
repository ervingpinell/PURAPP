<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use App\Models\PolicySection;
use App\Models\PolicySectionTranslation;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;

/**
 * PolicySectionController
 *
 * Handles policysection operations.
 */
class PolicySectionController extends Controller
{
    protected string $controller = 'PolicySectionController';

    public function __construct()
    {
        $this->middleware(['can:view-policy-sections'])->only(['index']);
        $this->middleware(['can:create-policy-sections'])->only(['store']);
        $this->middleware(['can:edit-policy-sections'])->only(['update', 'sort']);
        $this->middleware(['can:publish-policy-sections'])->only(['toggle']);
        $this->middleware(['can:publish-policy-sections'])->only(['toggle']);
        $this->middleware(['can:delete-policy-sections'])->only(['destroy', 'restore', 'forceDestroy']);
    }

    /** Listado de secciones de una política */
    public function index(Request $request, Policy $policy)
    {
        $status = $request->get('status', 'active');

        $query = $policy->sections()
            ->with('translations');

        if ($status === 'archived') {
            $query->onlyTrashed()->with('deletedBy');
        } elseif ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        // 'all' includes both active and inactive (but not trashed unless strictly needed, usually 'all' in admin means everything non-deleted + deleted? No, usually just non-deleted unless specified)
        // If 'all' means everything including trashed, we'd use withTrashed(). But usually 'all' implies current registry.
        // Let's stick to standard behavior: 'all' = users can see active and inactive.

        $sections = $query->orderBy('sort_order')->get();

        return view('admin.policies.sections.index', compact('policy', 'sections', 'status'));
    }

    /** Crear sección + traducciones (translateAll) */
    public function store(Request $request, Policy $policy, TranslatorInterface $translator)
    {
        $rules = [
            // Traducción base de entrada (ES por defecto en UI)
            'name'       => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],

            // Base
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'in:0,1'],
        ];

        $messages = [
            'required'   => __('validation.required', ['attribute' => ':attribute']),
            'string'     => __('validation.string',   ['attribute' => ':attribute']),
            'max.string' => __('validation.max.string', ['attribute' => ':attribute', 'max' => ':max']),
            'integer'    => __('validation.integer',  ['attribute' => ':attribute']),
            'min'        => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => ':min']),
            'in'         => __('validation.in',       ['attribute' => ':attribute']),
        ];

        $attributes = [
            'name'       => __('m_config.policies.section_name'),
            'content'    => __('m_config.policies.section_content'),
            'sort_order' => __('m_config.policies.order'),
            'is_active'  => __('m_config.policies.active'),
        ];

        $validated = $request->validate($rules, $messages, $attributes);

        $supportedLocales = ['es', 'en', 'fr', 'pt', 'de'];

        try {
            DB::transaction(function () use ($validated, $policy, $translator, $supportedLocales) {
                $baseName    = trim((string) $validated['name']);
                $baseContent = trim((string) $validated['content']);

                // 1) Crear sección en tabla base (sin name/content)
                $section = PolicySection::create([
                    'policy_id'  => $policy->policy_id,
                    'sort_order' => (int) ($validated['sort_order'] ?? 0),
                    'is_active'  => array_key_exists('is_active', $validated)
                        ? (bool) ((int) $validated['is_active'])
                        : true,
                ]);

                // 2) Traducir y crear filas en policy_section_translations
                try {
                    $nameTranslations    = (array) $translator->translateAll($baseName);
                } catch (\Throwable $e) {
                    $nameTranslations = [];
                }
                try {
                    $contentTranslations = (array) $translator->translateAll($baseContent);
                } catch (\Throwable $e) {
                    $contentTranslations = [];
                }

                foreach ($supportedLocales as $locale) {
                    $norm = Policy::canonicalLocale($locale);
                    PolicySectionTranslation::updateOrCreate(
                        ['section_id' => $section->section_id, 'locale' => $norm],
                        [
                            'name'    => $nameTranslations[$locale]    ?? $baseName,
                            'content' => $contentTranslations[$locale] ?? $baseContent,
                        ]
                    );
                }

                LoggerHelper::mutated($this->controller, 'store', 'policy_section', $section->section_id, [
                    'policy_id'     => $policy->policy_id,
                    'locales_saved' => count($supportedLocales),
                    'user_id'       => optional(request()->user())->getAuthIdentifier(),
                ]);
            });

            return redirect()
                ->route('admin.policies.sections.index', $policy)
                ->with('success', 'm_config.policies.section_created');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'policy_section', null, $e, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional(request()->user())->getAuthIdentifier(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /**
     * Actualizar:
     * - Base: sort_order / is_active
     * - Traducciones: array de traducciones (es, en, fr...)
     */
    public function update(Request $request, Policy $policy, PolicySection $section)
    {
        $rules = [
            // Traducciones
            'translations'             => ['nullable', 'array'],
            'translations.*.name'      => ['nullable', 'string', 'max:255'],
            'translations.*.content'   => ['nullable', 'string'],

            // Base
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'in:0,1'],
        ];

        $validated = $request->validate($rules);

        try {
            DB::transaction(function () use ($request, $validated, $section, $policy) {
                // 1) Base
                $baseUpdates = [];
                if ($request->filled('sort_order')) {
                    $baseUpdates['sort_order'] = (int) $validated['sort_order'];
                }
                if ($request->has('is_active')) {
                    $baseUpdates['is_active'] = (bool) ((int) $validated['is_active']);
                }
                if (!empty($baseUpdates)) {
                    $section->update($baseUpdates);
                }

                // 2) Traducciones
                if (!empty($validated['translations'])) {
                    foreach ($validated['translations'] as $locale => $data) {
                        $norm = Policy::canonicalLocale($locale);
                        
                        // Solo guardar si hay algo
                        if (empty($data['name']) && empty($data['content'])) {
                            continue;
                        }

                        PolicySectionTranslation::updateOrCreate(
                            ['section_id' => $section->section_id, 'locale' => $norm],
                            [
                                'name'    => trim($data['name'] ?? ''),
                                'content' => trim($data['content'] ?? ''),
                            ]
                        );
                    }
                }

                LoggerHelper::mutated($this->controller, 'update', 'policy_section', $section->section_id, [
                    'policy_id' => $policy->policy_id,
                    'user_id'   => optional($request->user())->getAuthIdentifier(),
                ]);
            });

            return redirect()
                ->route('admin.policies.sections.index', $policy)
                ->with('success', 'm_config.policies.section_updated');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'update', 'policy_section', $section->section_id, $e, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /** Activar/Desactivar una sección */
    public function toggle(Request $request, Policy $policy, PolicySection $section)
    {
        try {
            $section->update(['is_active' => ! $section->is_active]);
            $section->refresh();

            LoggerHelper::mutated($this->controller, 'toggle', 'policy_section', $section->section_id, [
                'policy_id' => $policy->policy_id,
                'is_active' => $section->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $feedbackKey = $section->is_active
                ? 'm_config.policies.section_activated'
                : 'm_config.policies.section_deactivated';

            return back()->with('success', $feedbackKey);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'policy_section', $section->section_id, $e, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /** Borrar sección */
    public function destroy(Request $request, Policy $policy, PolicySection $section)
    {
        try {
            $deletedId = $section->section_id;
            $section->update(['deleted_by' => auth()->id()]); // Track deletion
            $section->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'policy_section', $deletedId, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()->with('success', 'm_config.policies.section_deleted');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'policy_section', $section->section_id ?? null, $e, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /** Restore section */
    public function restore(Request $request, Policy $policy, $sectionId)
    {
        try {
            $section = PolicySection::withTrashed()->where('section_id', $sectionId)->firstOrFail();
            $section->restore();

            // Clear deleted_by on restore? Optional, but good practice.
            $section->update(['deleted_by' => null]);

            LoggerHelper::mutated($this->controller, 'restore', 'policy_section', $sectionId, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('success', 'm_config.policies.section_restored');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'restore', 'policy_section', $sectionId, $e);
            return back()->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /** Force delete section */
    public function forceDestroy(Request $request, Policy $policy, $sectionId)
    {
        try {
            $section = PolicySection::withTrashed()->where('section_id', $sectionId)->firstOrFail();
            $section->forceDelete();

            LoggerHelper::mutated($this->controller, 'forceDestroy', 'policy_section', $sectionId, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('success', 'm_config.policies.section_force_deleted');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'forceDestroy', 'policy_section', $sectionId, $e);
            return back()->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /** Ordenar secciones (drag & drop) */
    public function sort(Request $request, Policy $policy)
    {
        $validated = $request->validate([
            'orders'              => ['required', 'array'],
            'orders.*.section_id' => ['required', 'integer'],
            'orders.*.sort_order' => ['required', 'integer', 'min:0'],
        ], [
            'required' => __('validation.required', ['attribute' => ':attribute']),
            'array'    => __('validation.array',    ['attribute' => ':attribute']),
            'integer'  => __('validation.integer',  ['attribute' => ':attribute']),
            'min'      => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => ':min']),
        ], [
            'orders'              => __('m_config.policies.order'),
            'orders.*.section_id' => __('m_config.policies.id'),
            'orders.*.sort_order' => __('m_config.policies.order'),
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['orders'] as $orderRow) {
                PolicySection::where('section_id', $orderRow['section_id'])
                    ->update(['sort_order' => $orderRow['sort_order']]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
