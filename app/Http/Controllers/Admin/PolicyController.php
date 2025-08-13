<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\PolicyTranslation;
use Illuminate\Support\Facades\DB;

class PolicyController extends Controller
{
    /**
     * Admin: listado y gestión de políticas.
     */
    public function index()
    {
        $policies = Policy::orderBy('policy_id')
            ->with([
                'translations',
                'sections' => fn($q) => $q->orderBy('sort_order')->orderBy('section_id'),
                'sections.translations',
            ])
            ->get();

        return view('admin.policies.index', compact('policies'));
    }

    /**
     * Admin: crear política.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'           => ['required','string','max:255'],
            'name'           => ['required','string','max:255'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'is_default'     => ['nullable','boolean'],

            // Traducción inicial (opcional; normalmente ES o locale activo)
            'locale'         => ['nullable','string','max:10'],
            'title'          => ['nullable','string','max:255'],
            'content'        => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($request) {
            $policy = Policy::create([
                'type'           => $request->string('type')->trim(),
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_default'     => $request->boolean('is_default'),
                'is_active'      => true,
            ]);

            // ✅ Dejar una sola default por TIPO
            if ($policy->is_default) {
                Policy::where('policy_id', '!=', $policy->policy_id)
                    ->where('type', $policy->type)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // Upsert de traducción inicial si se envía
            $locale  = $request->input('locale', app()->getLocale());
            $title   = $request->input('title');
            $content = $request->input('content');

            if (filled($title) || filled($content)) {
                PolicyTranslation::updateOrCreate(
                    ['policy_id' => $policy->policy_id, 'locale' => $locale],
                    ['title' => $title ?? '', 'content' => $content ?? '']
                );
            }

            return redirect()->route('admin.policies.index')
                ->with('success', 'Política creada correctamente.');
        });
    }

    /**
     * Admin: actualizar política.
     */
    public function update(Request $request, Policy $policy)
    {
        $request->validate([
            'type'           => ['required','string','max:255'],
            'name'           => ['required','string','max:255'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'is_default'     => ['nullable','boolean'],

            // Traducción editable (opcional)
            'locale'         => ['nullable','string','max:10'],
            'title'          => ['nullable','string','max:255'],
            'content'        => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($request, $policy) {
            $policy->update([
                'type'           => $request->string('type')->trim(),
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_default'     => $request->boolean('is_default'),
            ]);

            // ✅ Dejar una sola default por TIPO
            if ($policy->is_default) {
                Policy::where('policy_id', '!=', $policy->policy_id)
                    ->where('type', $policy->type)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // Upsert de traducción en el locale indicado
            $locale  = $request->input('locale', app()->getLocale());
            $title   = $request->input('title', null);
            $content = $request->input('content', null);

            if (!is_null($title) || !is_null($content)) {
                $tr = PolicyTranslation::firstOrNew([
                    'policy_id' => $policy->policy_id,
                    'locale'    => $locale,
                ]);

                if (!is_null($title))   $tr->title   = $title;
                if (!is_null($content)) $tr->content = $content;

                $tr->save();
            }

            return redirect()->route('admin.policies.index')
                ->with('success', 'Política actualizada correctamente.');
        });
    }

    /**
     * Admin: activar/desactivar política.
     */
    public function toggleStatus(Policy $policy)
    {
        $policy->is_active = !$policy->is_active;
        $policy->save();

        return redirect()->route('admin.policies.index')
            ->with('success', 'Estado de la política actualizado.');
    }

    /**
     * Admin: eliminar política.
     */
    public function destroy(Policy $policy)
    {
        $policy->delete();

        return redirect()->route('admin.policies.index')
            ->with('success', 'Política eliminada correctamente.');
    }

    /**
     * Público: listado de todas las políticas con secciones.
     */
    public function publicIndex()
    {
        $policies = Policy::with([
                'translations',
                'sections' => function ($q) {
                    $q->where('is_active', true)
                      ->orderBy('sort_order')
                      ->orderBy('section_id');
                },
                'sections.translations',
            ])
            ->where('is_active', true)
            ->orderBy('type')           // agrupa visualmente por categoría
            ->orderByDesc('is_default') // muestra primero la default de ese tipo
            ->orderBy('policy_id')
            ->get();

        return view('policies.index', compact('policies'));
    }

    /**
     * Público: ver una política por ID, con secciones.
     */
    public function showPublic(int $policyId)
    {
        $policy = Policy::with([
                'translations',
                'sections' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'sections.translations',
            ])
            ->where('policy_id', $policyId)
            ->where('is_active', true)
            ->firstOrFail();

        return view('policies.show', compact('policy'));
    }
}
