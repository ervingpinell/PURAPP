<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Policy;
use App\Models\PolicySection;
use App\Models\PolicySectionTranslation;

class PolicySectionController extends Controller
{
    /**
     * Lista todas las secciones de una política (activas e inactivas)
     */
    public function index(Policy $policy)
    {
        $sections = PolicySection::where('policy_id', $policy->policy_id)
            ->orderBy('sort_order')
            ->orderBy('section_id')
            ->with('translations')
            ->get();

        return view('admin.policies.sections.index', compact('policy', 'sections'));
    }

    /**
     * Crea una sección y su traducción para el locale enviado
     */
    public function store(Request $request, Policy $policy)
    {
        $allowedLocales = ['es','en','fr','pt','de'];

        // Normaliza locale y checkbox antes de validar
        $rawLocale  = $request->input('locale', app()->getLocale());
        $normLocale = substr(strtolower(str_replace('_','-', $rawLocale)), 0, 2);

        $request->merge([
            'locale'    => $normLocale,
            'is_active' => $request->boolean('is_active'),
        ]);

        $validated = $request->validate([
            'key'        => ['nullable','string','max:100'],
            'sort_order' => [
                'nullable','integer','min:0',
                Rule::unique('policy_sections', 'sort_order')
                    ->where(fn($q) => $q->where('policy_id', $policy->policy_id)),
            ],
            'is_active'  => ['nullable','boolean'],
            'locale'     => ['required', Rule::in($allowedLocales)],
            'title'      => ['required','string','max:255'],
            'content'    => ['required','string'],
        ], [
            'sort_order.min'    => 'El orden debe ser 0 o mayor.',
            'sort_order.unique' => 'Ya existe otra sección con ese orden en esta política.',
        ]);

        $locale    = $validated['locale'];
        $isActive  = (bool)($validated['is_active'] ?? true);

        // Si no se envía sort_order, coloca el siguiente bloque (+10)
        $sortOrder = $validated['sort_order']
            ?? ((int) PolicySection::where('policy_id', $policy->policy_id)->max('sort_order') + 10);

        DB::transaction(function () use ($policy, $validated, $locale, $isActive, $sortOrder) {
            $section = PolicySection::create([
                'policy_id'  => $policy->policy_id,
                'key'        => $validated['key'] ?? null,
                'sort_order' => $sortOrder,
                'is_active'  => $isActive,
            ]);

            PolicySectionTranslation::create([
                'section_id' => $section->section_id,
                'locale'     => $locale,
                'title'      => $validated['title'],
                'content'    => $validated['content'],
            ]);
        });

        return redirect()
            ->route('admin.policies.sections.index', $policy)
            ->with('success', '✅ Sección creada correctamente.');
    }

    /**
     * Actualiza metadatos de la sección y hace upsert de traducción para el locale enviado
     */
    public function update(Request $request, Policy $policy, PolicySection $section)
    {
        // Asegura que la sección pertenece a la política
        if ((int)$section->policy_id !== (int)$policy->policy_id) {
            abort(404);
        }

        $allowedLocales = ['es','en','fr','pt','de'];

        // Normaliza locale y checkbox antes de validar
        $rawLocale  = $request->input('locale', app()->getLocale());
        $normLocale = substr(strtolower(str_replace('_','-', $rawLocale)), 0, 2);

        $request->merge([
            'locale'    => $normLocale,
            'is_active' => $request->boolean('is_active'),
        ]);

        $validated = $request->validate([
            'key'        => ['nullable','string','max:100'],
            'sort_order' => [
                'nullable','integer','min:0',
                Rule::unique('policy_sections', 'sort_order')
                    ->ignore($section->section_id, 'section_id')
                    ->where(fn($q) => $q->where('policy_id', $policy->policy_id)),
            ],
            'is_active'  => ['nullable','boolean'],
            'locale'     => ['required', Rule::in($allowedLocales)],
            'title'      => ['nullable','string','max:255'],
            'content'    => ['nullable','string'],
        ], [
            'sort_order.min'    => 'El orden debe ser 0 o mayor.',
            'sort_order.unique' => 'Ya existe otra sección con ese orden en esta política.',
        ]);

        $locale    = $validated['locale'];
        $isActive  = array_key_exists('is_active', $validated)
            ? (bool)$validated['is_active']
            : (bool)$section->is_active;

        $sortOrder = $validated['sort_order'] ?? $section->sort_order;

        DB::transaction(function () use ($section, $validated, $locale, $isActive, $sortOrder) {
            // Actualiza metadatos de la sección
            $section->update([
                'key'        => $validated['key'] ?? $section->key,
                'sort_order' => $sortOrder,
                'is_active'  => $isActive,
            ]);

            // Upsert de traducción del locale (solo si viene contenido)
            $hasTitle   = array_key_exists('title', $validated);
            $hasContent = array_key_exists('content', $validated);

            if ($hasTitle || $hasContent) {
                $tr = PolicySectionTranslation::firstOrNew([
                    'section_id' => $section->section_id,
                    'locale'     => $locale,
                ]);

                if ($hasTitle)   $tr->title   = $validated['title'];
                if ($hasContent) $tr->content = $validated['content'];

                $tr->save();
            }
        });

        return redirect()
            ->route('admin.policies.sections.index', $section->policy_id)
            ->with('success', '✅ Sección actualizada correctamente.');
    }

    /**
     * Alterna activo/inactivo
     */
    public function toggle(Policy $policy, PolicySection $section)
    {
        if ((int)$section->policy_id !== (int)$policy->policy_id) {
            abort(404);
        }

        $section->is_active = ! $section->is_active;
        $section->save();

        return redirect()
            ->route('admin.policies.sections.index', $policy)
            ->with('success', 'Estado de la sección actualizado.');
    }

    /**
     * Elimina la sección (sus traducciones se borran por FK ON DELETE CASCADE)
     */
    public function destroy(Policy $policy, PolicySection $section)
    {
        if ((int)$section->policy_id !== (int)$policy->policy_id) {
            abort(404);
        }

        $section->delete();

        return redirect()
            ->route('admin.policies.sections.index', $policy)
            ->with('success', '🗑️ Sección eliminada.');
    }
}
