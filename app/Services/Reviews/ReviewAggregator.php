<?php

namespace App\Services\Reviews;

use App\Services\Reviews\Drivers\Contracts\ReviewSource;
use Illuminate\Support\Collection;

/**
 * Agregador neutral (sin caché interno; los controladores cachean).
 * Inyecta las fuentes por el contenedor (Service Provider).
 */
class ReviewAggregator
{
    /** @var array<string, ReviewSource> */
    protected array $sources;

    /**
     * @param iterable<string, ReviewSource> $sources  // p.ej. ['local' => LocalReviewSource, 'viator' => ViatorReviewSource]
     */
    public function __construct(iterable $sources = [])
    {
        $this->sources = is_array($sources) ? $sources : iterator_to_array($sources);
    }

    /**
     * @param array{
     *   provider?: string|null,
     *   tour_id?: int|string|null,
     *   limit?: int,
     *   only_indexable?: bool
     * } $opts
     */
    public function aggregate(array $opts = []): Collection
    {
        $want          = max(1, (int)($opts['limit'] ?? 50));
        $onlyIndexable = (bool) ($opts['only_indexable'] ?? false);
        $provider      = $opts['provider'] ?? null;

        $enabled = $this->enabledProviders($provider);
        $rows    = collect();

        foreach ($enabled as $slug => $driver) {
            $fetched = $driver->fetch($opts) ?? [];
            if (!is_array($fetched)) $fetched = [];

            foreach ($fetched as $r) {
                $rows->push($this->normalizeShape($slug, $r));
            }
        }

// DEDUPE 1: por provider + provider_review_id (como ya lo tienes)
$rows = $rows->unique(function ($r) {
    $rid = (string) ($r['provider_review_id'] ?? '');
    $p   = (string) ($r['provider'] ?? 'p');
    if ($rid !== '') return $p . '#' . $rid;

    return $p . '#' . md5(
        mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
        mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
        trim((string)($r['date'] ?? ''))
    );
});

// DEDUPE 2: por firma de contenido (mismo proveedor)
// — Esto elimina duplicados “por producto” con el mismo texto/autor/fecha.
$rows = $rows->unique(function ($r) {
    $p = (string) ($r['provider'] ?? 'p');
    return $p . '#' . md5(
        mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
        mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
        trim((string)($r['date'] ?? ''))
    );
});

        // Filtrar indexables si se pide
        if ($onlyIndexable) {
            $rows = $rows->filter(fn ($r) => !empty($r['indexable']));
        }

        // Pequeño shuffle para variar la home
        $rows = $rows->shuffle()->take($want)->values();

        return $rows;
    }

    /** @return array<string, ReviewSource> */
    protected function enabledProviders(?string $only = null): array
    {
        if ($only) {
            $only = strtolower($only);
            return array_key_exists($only, $this->sources) ? [$only => $this->sources[$only]] : [];
        }
        return $this->sources;
    }

    /**
     * Normaliza campos esperados por blades/JS.
     * Marca `indexable` en base a config.
     */
    protected function normalizeShape(string $provider, array $r): array
    {
        $provider = strtolower($provider);

        $rating = (int) max(0, min(5, (int)($r['rating'] ?? 0)));
        $title  = trim((string) ($r['title'] ?? ''));
        $body   = trim((string) ($r['body'] ?? ''));
        $author = trim((string) ($r['author_name'] ?? ''));
        $date   = $r['date'] ?? null;

        $tourId = $r['tour_id'] ?? null;

        $indexableProviders = config('reviews.indexable_providers', ['local']); // ← ajustable
        $indexable = in_array($provider, $indexableProviders, true);

        return [
            'provider'           => $provider,
            'indexable'          => $indexable,
            'provider_review_id' => $r['provider_review_id'] ?? null,
            'rating'             => $rating,
            'title'              => $title !== '' ? $title : null,
            'body'               => $body,
            'author_name'        => $author !== '' ? $author : null,
            'date'               => $date ?: null,
            'tour_id'            => $tourId ? (int) $tourId : null,
            // opcionales:
            'tour_name'          => $r['tour_name'] ?? null,
            'avatar_url'         => $r['avatar_url'] ?? null,
            'nth'                => $r['nth'] ?? null,
            'iframe_limit'       => $r['iframe_limit'] ?? null,
        ];
    }
}
