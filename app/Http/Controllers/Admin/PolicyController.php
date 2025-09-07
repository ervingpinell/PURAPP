<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use App\Models\PolicyTranslation;
use App\Services\Contracts\TranslatorInterface;
use App\Services\LoggerHelper;

class PolicyController extends Controller
{
    protected string $controller = 'PolicyController';

    /** Listado de políticas con filtros simples */
    public function index(Request $request)
    {
        $policiesQuery = Policy::query()
            ->with('translations')
            ->withCount('sections');

        if ($request->filled('q')) {
            $searchTerm = trim((string) $request->input('q'));

            $policiesQuery->where(function ($policyQuery) use ($searchTerm) {
                $policyQuery->where('name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('translations', function ($translationsQuery) use ($searchTerm) {
                        $translationsQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->filled('from')) {
            $policiesQuery->whereDate('effective_from', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $policiesQuery->whereDate('effective_to', '<=', $request->date('to'));
        }

        $policies = $policiesQuery->orderByDesc('policy_id')->get();

        return view('admin.policies.index', compact('policies'));
    }

    /** Crear política + traducciones (translateAll) */
    public function store(Request $request, TranslatorInterface $translator)
    {
        $rules = [
            'is_active'      => ['nullable', 'in:0,1'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
            'name'           => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string'],
        ];

        $messages = [
            'required'                     => __('validation.required', ['attribute' => ':attribute']),
            'string'                       => __('validation.string',   ['attribute' => ':attribute']),
            'max.string'                   => __('validation.max.string', ['attribute' => ':attribute', 'max' => ':max']),
            'date'                         => __('validation.date',     ['attribute' => ':attribute']),
            'in'                           => __('validation.in',       ['attribute' => ':attribute']),
            'effective_to.after_or_equal'  => __('validation.after_or_equal', ['attribute' => ':attribute', 'date' => __('m_config.policies.valid_from')]),
        ];

        $attributes = [
            'name'           => __('m_config.policies.name'),
            'content'        => __('m_config.policies.content_label'),
            'is_active'      => __('m_config.policies.active'),
            'effective_from' => __('m_config.policies.valid_from'),
            'effective_to'   => __('m_config.policies.valid_to'),
        ];

        $request->validate($rules, $messages, $attributes);

        $locales = ['es','en','fr','pt','de'];

        try {
            DB::transaction(function () use ($request, $translator, $locales) {
                $baseName    = $request->string('name')->trim();
                $baseContent = $request->string('content')->trim();

                $policy = Policy::create([
                    'name'           => $baseName,
                    'is_active'      => (bool) $request->boolean('is_active', true),
                    'effective_from' => $request->date('effective_from'),
                    'effective_to'   => $request->date('effective_to'),
                ]);

                // Traducciones automáticas (fallback al original si falla)
                try { $nameTranslations    = (array) $translator->translateAll($baseName); }    catch (\Throwable $e) { $nameTranslations = []; }
                try { $contentTranslations = (array) $translator->translateAll($baseContent); } catch (\Throwable $e) { $contentTranslations = []; }

                foreach ($locales as $locale) {
                    PolicyTranslation::updateOrCreate(
                        ['policy_id' => $policy->policy_id, 'locale' => $locale],
                        [
                            'name'    => $nameTranslations[$locale]    ?? $baseName,
                            'content' => $contentTranslations[$locale] ?? $baseContent,
                        ]
                    );
                }

                LoggerHelper::mutated($this->controller, 'store', 'policy', $policy->policy_id, [
                    'locales_saved' => count($locales),
                    'user_id'       => optional(request()->user())->getAuthIdentifier(),
                ]);
            });

            return redirect()
                ->route('admin.policies.index')
                ->with('success', 'm_config.policies.category_created');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'store', 'policy', null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'm_config.policies.unexpected_error');
        }
    }

    /** Actualizar solo campos base (no retraduce) */
// app/Http/Controllers/Admin/PolicyController.php

public function update(Request $request, Policy $policy)
{
    $rules = [
        'name'           => ['nullable', 'string', 'max:255'],
        'is_active'      => ['nullable', 'in:0,1'],
        'effective_from' => ['nullable', 'date'],
        'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],

        // NUEVO: permitimos actualizar contenido de la traducción actual
        'content'        => ['nullable', 'string'],
        'locale'         => ['nullable', 'string', 'in:es,en,fr,pt,de'], // ajusta si tienes más
    ];

    $messages = [
        'string'                      => __('validation.string',   ['attribute' => ':attribute']),
        'max.string'                  => __('validation.max.string', ['attribute' => ':attribute', 'max' => ':max']),
        'date'                        => __('validation.date',     ['attribute' => ':attribute']),
        'in'                          => __('validation.in',       ['attribute' => ':attribute']),
        'effective_to.after_or_equal' => __('validation.after_or_equal', ['attribute' => ':attribute', 'date' => __('m_config.policies.valid_from')]),
    ];

    $attributes = [
        'name'           => __('m_config.policies.name'),
        'is_active'      => __('m_config.policies.active'),
        'effective_from' => __('m_config.policies.valid_from'),
        'effective_to'   => __('m_config.policies.valid_to'),
        'content'        => __('m_config.policies.content_label'),
        'locale'         => 'locale',
    ];

    $request->validate($rules, $messages, $attributes);

    try {
        // === 1) Actualiza campos base en policies ===
        $updateAttributes = [
            'is_active'      => (bool) $request->boolean('is_active', $policy->is_active),
            'effective_from' => $request->filled('effective_from') ? $request->date('effective_from') : $policy->effective_from,
            'effective_to'   => $request->filled('effective_to')   ? $request->date('effective_to')   : $policy->effective_to,
        ];

        if ($request->filled('name')) {
            $updateAttributes['name'] = (string) $request->string('name')->trim();
        }

        $policy->update($updateAttributes);

        // === 2) Actualiza/crea la traducción para el locale indicado (o el actual) ===
        $locale = $request->input('locale', app()->getLocale());

        // Cargamos la traducción actual (si existe)
        $currentT = $policy->translations()->where('locale', $locale)->first();

        // Si vino "content" (o "name"), persistimos en policy_translations para ese locale
        if ($request->filled('content') || $request->filled('name')) {
            \App\Models\PolicyTranslation::updateOrCreate(
                ['policy_id' => $policy->policy_id, 'locale' => $locale],
                [
                    // Si enviaste "name" lo replicamos en la traducción del locale actual.
                    // Si no, conservamos el existente (o caemos al base name del policy).
                    'name'    => $request->filled('name')
                                    ? (string) $request->string('name')->trim()
                                    : ($currentT->name ?? $policy->name),

                    // Content sólo si lo enviaste (si no, mantenemos el que ya hay)
                    'content' => $request->filled('content')
                                    ? (string) $request->string('content')->trim()
                                    : ($currentT->content ?? null),
                ]
            );
        }

        LoggerHelper::mutated($this->controller, 'update', 'policy', $policy->policy_id, [
            'is_active' => $policy->is_active,
            'locale'    => $locale,
            'user_id'   => optional($request->user())->getAuthIdentifier(),
        ]);

        return back()->with('success', 'm_config.policies.category_updated');
    } catch (\Exception $e) {
        LoggerHelper::exception($this->controller, 'update', 'policy', $policy->policy_id, $e, [
            'user_id' => optional($request->user())->getAuthIdentifier(),
        ]);
        return back()->with('error', 'm_config.policies.unexpected_error');
    }
}


    public function destroy(Request $request, Policy $policy)
    {
        try {
            $deletedId = $policy->policy_id;
            $policy->delete();

            LoggerHelper::mutated($this->controller, 'destroy', 'policy', $deletedId, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);

            return redirect()
                ->route('admin.policies.index')
                ->with('success', 'm_config.policies.category_deleted');
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'destroy', 'policy', $policy->policy_id ?? null, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'm_config.policies.unexpected_error');
        }
    }

    public function toggle(Request $request, Policy $policy)
    {
        try {
            $policy->update(['is_active' => ! $policy->is_active]);
            $policy->refresh();

            LoggerHelper::mutated($this->controller, 'toggle', 'policy', $policy->policy_id, [
                'is_active' => $policy->is_active,
                'user_id'   => optional($request->user())->getAuthIdentifier(),
            ]);

            $feedbackKey = $policy->is_active
                ? 'm_config.policies.category_activated'
                : 'm_config.policies.category_deactivated';

            return back()->with('success', $feedbackKey);
        } catch (Exception $e) {
            LoggerHelper::exception($this->controller, 'toggle', 'policy', $policy->policy_id, $e, [
                'user_id' => optional($request->user())->getAuthIdentifier(),
            ]);
            return back()->with('error', 'm_config.policies.unexpected_error');
        }
    }
}
