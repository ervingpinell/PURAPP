<?php

namespace App\Services\Policies;

use App\Models\Policy;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class PolicySnapshotService
{
    /**
     * Genera un snapshot de políticas directamente desde la BD
     * (policies, sections y sus traducciones) para el locale actual.
     *
     * @param  string|null $locale  (por defecto app()->getLocale())
     * @return array{versions: array, snapshot: array, sha256: string, meta: array}
     */
    public function make(?string $locale = null): array
    {
        $loc = $this->canonicalLocale($locale ?: app()->getLocale());
        $fb  = $this->canonicalLocale((string) config('app.fallback_locale', 'es'));

        // Cargar por tipo habitual; ajusta los slugs si usas otros.
        $map = [
            'terms'        => 'terminos',
            'privacy'      => 'privacidad',
            'cancellation' => 'cancelacion',
            'refunds'      => 'reembolso',
            'warranty'     => 'garantia',
            'payments'     => 'pagos',
        ];

        // Traemos todas las policies necesarias, con sections y translations
        $policies = collect($map)
            ->mapWithKeys(fn ($slug, $key) => [$key => Policy::byType($slug)
                ?->load([
                    // traducciones de la policy
                    'translations',
                    // secciones con traducciones
                    'sections.translations',
                ])])
            ->filter();

        // Armar bloque serializable por locale
        $blocks = $policies->map(function (Policy $p) use ($loc, $fb) {
            $pt = $p->translation($loc) ?: $p->translation($fb);
            $ver = $this->formatVersion($p);

            // Secciones ordenadas por sort_order (si existe)
            $sections = $p->sections()
                ->when(schema()->hasColumn('policy_sections', 'sort_order'), fn($q) => $q->orderBy('sort_order'))
                ->get();

            $secBlocks = $sections->map(function ($s) use ($loc, $fb) {
                $st = method_exists($s, 'translation')
                    ? ($s->translation($loc) ?: $s->translation($fb))
                    : null;
                return [
                    'section_id'  => $s->section_id,
                    'sort_order'  => (int) $s->sort_order,
                    'is_active'   => (bool) $s->is_active,
                    'locale'      => $st?->locale,
                    'name'        => (string) ($st?->name ?? ''),
                    'content_html'=> (string) ($st?->content ?? ''),
                ];
            })->values()->all();

            return [
                'policy_id'    => $p->policy_id,
                'slug'         => $p->slug,
                'type'         => $p->type ?? null, // si lo tienes
                'version'      => $ver,             // vYYYY-MM-DD o '—'
                'effective_from'=> optional($p->effective_from)->toDateString(),
                'effective_to'  => optional($p->effective_to)->toDateString(),
                'is_active'     => (bool) $p->is_active,
                'locale'        => $pt?->locale,
                'title'         => (string) ($pt?->name ?? $p->name ?? ''),
                'content_html'  => (string) ($pt?->content ?? $p->content ?? ''),
                'sections'      => $secBlocks,
            ];
        });

        // Versión "corta" que pide tu controlador actual (terms/privacy)
        $versions = [
            'terms'   => $policies->has('terms')   ? ($this->formatVersion($policies['terms']))   : 'v1',
            'privacy' => $policies->has('privacy') ? ($this->formatVersion($policies['privacy'])) : 'v1',
        ];

        // SHA256 determinístico de todo el HTML (policy + secciones)
        $normalized = $blocks->map(function ($b) {
            $html = trim((string) $b['content_html']);
            $sec  = collect($b['sections'])->pluck('content_html')->implode('|');
            $all  = trim(preg_replace('/\s+/', ' ', $html . '|' . $sec) ?? '');
            return $all;
        })->implode('||');
        $sha = hash('sha256', $normalized);

        return [
            'versions' => $versions,
            'snapshot' => $blocks->toArray(),
            'sha256'   => $sha,
            'meta'     => [
                'locale' => $loc,
                'count'  => $blocks->count(),
            ],
        ];
    }

    private function canonicalLocale(string $v): string
    {
        $v = str_replace('-', '_', strtolower($v));
        if ($v === 'pt') return 'pt_BR'; // tu caso común
        return $v;
    }

    private function formatVersion(?Policy $p): string
    {
        if (!$p) return '—';
        if ($p->effective_from) {
            try {
                return 'v' . \Illuminate\Support\Carbon::parse($p->effective_from)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }
        // Si tuvieras un campo version explícito, úsalo aquí.
        return '—';
    }
}

/**
 * Helper: chequeo de columnas con cache simple.
 * Evita romper si no existe sort_order (entornos distintos).
 */
if (! function_exists('schema')) {
    function schema() {
        static $schema;
        return $schema ??= app('db.schema');
    }
}
