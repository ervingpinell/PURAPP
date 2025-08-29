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

class PolicySectionController extends Controller
{
    protected string $controller = 'PolicySectionController';

    /** Listado de secciones de una política */
    public function index(Policy $policy)
    {
        $sections = $policy->sections()
            ->withoutGlobalScopes()
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return view('admin.policies.sections.index', compact('policy', 'sections'));
    }

    /** Crear sección + traducciones (translateAll) */
    public function store(Request $request, Policy $policy, TranslatorInterface $translator)
    {
        $rules = [
            'name'       => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'in:0,1'],
        ];

        // Mensajes genéricos (Laravel validation.*) + atributos traducidos
        $messages = [
            'required'        => __('validation.required', ['attribute' => ':attribute']),
            'string'          => __('validation.string',   ['attribute' => ':attribute']),
            'max.string'      => __('validation.max.string', ['attribute' => ':attribute', 'max' => ':max']),
            'integer'         => __('validation.integer',  ['attribute' => ':attribute']),
            'min'             => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => ':min']),
            'in'              => __('validation.in',       ['attribute' => ':attribute']),
        ];

        $attributes = [
            'name'       => __('m_config.policies.name'),
            'content'    => __('m_config.policies.translation_content'),
            'sort_order' => __('m_config.policies.order'),
            'is_active'  => __('m_config.policies.active'),
        ];

        $request->validate($rules, $messages, $attributes);

        $supportedLocales = ['es','en','fr','pt','de'];

        try {
            DB::transaction(function () use ($request, $policy, $translator, $supportedLocales) {
                $baseName    = $request->string('name')->trim();
                $baseContent = $request->string('content')->trim();

                // Base (tabla policy_sections)
                $section = PolicySection::create([
                    'policy_id'  => $policy->policy_id,
                    'name'       => $baseName,
                    'sort_order' => (int) $request->input('sort_order', 0),
                    'is_active'  => (bool) $request->boolean('is_active', true),
                ]);

                // Traducción automática (fallback al original si falla)
                try { $nameTranslations    = (array) $translator->translateAll($baseName); }    catch (\Throwable $e) { $nameTranslations = []; }
                try { $contentTranslations = (array) $translator->translateAll($baseContent); } catch (\Throwable $e) { $contentTranslations = []; }

                foreach ($supportedLocales as $locale) {
                    PolicySectionTranslation::updateOrCreate(
                        ['section_id' => $section->section_id, 'locale' => $locale],
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
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /** Actualizar solo base (name/sort_order/is_active). No retraduce. */
    public function update(Request $request, Policy $policy, PolicySection $section)
    {
        $rules = [
            'name'       => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'in:0,1'],
        ];

        $messages = [
            'string'     => __('validation.string',   ['attribute' => ':attribute']),
            'max.string' => __('validation.max.string', ['attribute' => ':attribute', 'max' => ':max']),
            'integer'    => __('validation.integer',  ['attribute' => ':attribute']),
            'min'        => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => ':min']),
            'in'         => __('validation.in',       ['attribute' => ':attribute']),
        ];

        $attributes = [
            'name'       => __('m_config.policies.name'),
            'sort_order' => __('m_config.policies.order'),
            'is_active'  => __('m_config.policies.active'),
        ];

        $request->validate($rules, $messages, $attributes);

        try {
            $updateAttributes = [
                'sort_order' => $request->filled('sort_order') ? (int) $request->input('sort_order') : $section->sort_order,
                'is_active'  => $request->has('is_active') ? (bool) $request->boolean('is_active') : $section->is_active,
            ];

            if ($request->filled('name')) {
                $updateAttributes['name'] = (string) $request->string('name')->trim();
            }

            $section->update($updateAttributes);

            LoggerHelper::mutated($this->controller, 'update', 'policy_section', $section->section_id, [
                'policy_id' => $policy->policy_id,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

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

    /** Ordenar secciones (drag & drop) */
    public function sort(Request $request, Policy $policy)
    {
        $validated = $request->validate([
            'orders'                 => ['required', 'array'],
            'orders.*.section_id'    => ['required', 'integer'],
            'orders.*.sort_order'    => ['required', 'integer', 'min:0'],
        ], [
            'required'  => __('validation.required', ['attribute' => ':attribute']),
            'array'     => __('validation.array',    ['attribute' => ':attribute']),
            'integer'   => __('validation.integer',  ['attribute' => ':attribute']),
            'min'       => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => ':min']),
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
