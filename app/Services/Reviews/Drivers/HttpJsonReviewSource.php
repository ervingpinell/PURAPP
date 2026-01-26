<?php

namespace App\Services\Reviews\Drivers;

use App\Models\ReviewProvider;
use App\Services\Reviews\Drivers\Contracts\ReviewSource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * HttpJsonReviewSource
 *
 * Handles httpjsonreviewsource operations.
 */
class HttpJsonReviewSource implements ReviewSource
{
    protected ?ReviewProvider $providerRow;
    protected array $settings;
    protected string $slug = 'generic';
    protected int $ttl;

    protected ?string $apiKey = null;

    // Request defaults
    protected string $method = 'GET';
    protected string $url    = '';
    protected array $headers = [];
    protected array $query   = [];
    protected array $payload = [];
    protected string $listPath = 'reviews';

    // Mapping defaults
    protected array $map = [
        'rating'             => 'rating',
        'title'              => 'title',
        'body'               => 'text',
        'author_name'        => 'author.name',
        'date'               => 'date',
        'provider_review_id' => 'id',
        'product_code'       => 'productCode',
        // opcional: 'language' => 'language'
    ];

    protected array $extrasMap = [];
    protected array $productMap = [];
    protected array $filters    = [];

    public function __construct(?ReviewProvider $provider = null)
    {
        $this->providerRow = $provider;
        $rawSettings       = $provider?->settings ?? [];
        $this->settings    = is_array($rawSettings) ? $rawSettings : $this->decodeJsonSafe($rawSettings);

        $this->slug    = $provider?->slug ?: 'generic';
        $this->ttl     = (int) ($provider?->cache_ttl_sec ?: 3600);
        $this->method  = strtoupper((string) ($this->settings['method'] ?? 'GET'));
        $this->url     = (string) ($this->settings['url'] ?? '');

        $this->headers    = (array) ($this->settings['headers'] ?? []);
        $this->query      = (array) ($this->settings['query'] ?? []);
        $this->payload    = (array) ($this->settings['payload'] ?? []);
        $this->listPath   = (string) ($this->settings['list_path'] ?? 'reviews');
        $this->map        = array_replace($this->map, (array) ($this->settings['map'] ?? []));
        $this->productMap = (array) ($this->settings['product_map'] ?? []);
        $this->extrasMap  = (array) ($this->settings['extras'] ?? []);
        $this->filters    = (array) ($this->settings['filters'] ?? []);

        // === Cargar api_key si está en settings (soporta {env:..} y {config:..})
        $this->apiKey = $provider?->getSetting('api_key');
        if (is_string($this->apiKey)) {
            $this->apiKey = $this->resolveDynamicString($this->apiKey);
        }

        // === Resolver tokens {env:VAR} y {config:path} recursivamente en url/headers/query/payload
        $this->url       = $this->resolveDynamicString($this->url);
        $this->headers   = $this->resolveDynamicTokens($this->headers);
        $this->query     = $this->resolveDynamicTokens($this->query);
        $this->payload   = $this->resolveDynamicTokens($this->payload);
        $this->map       = $this->resolveDynamicTokens($this->map);
        $this->extrasMap = $this->resolveDynamicTokens($this->extrasMap);
        $this->filters   = $this->resolveDynamicTokens($this->filters);

        // Reemplaza {api_key} si aparece en headers
        if (!empty($this->apiKey)) {
            foreach ($this->headers as $k => $v) {
                if (is_string($v)) {
                    $this->headers[$k] = str_replace('{api_key}', (string) $this->apiKey, $v);
                }
            }
        }
    }

    /**
     * @param array{product_id?:int|string|null,language?:string|null,limit?:int,min?:int,start?:int,min_rating?:int} $opts
     */
    public function fetch(array $opts = []): array
    {
        if ($this->url === '') {
            Log::warning('[HttpJsonReviewSource] URL faltante', ['slug' => $this->slug]);
            return [];
        }

        $limit     = max(1, (int) ($opts['limit'] ?? 12));
        $minWanted = max(1, (int) ($opts['min'] ?? $limit));
        $lang      = (string) ($opts['language'] ?? app()->getLocale());
        $start     = max(1, (int) ($opts['start'] ?? 1));
        $minRating = max(0, (int) ($opts['min_rating'] ?? ($this->filters['min_rating'] ?? ($this->settings['min_rating'] ?? 0))));

        $codes = $this->codesFor($opts['product_id'] ?? null);
        if (empty($codes)) $codes = [null];

        $client = new Client([
            'timeout'     => 12.0,
            'http_errors' => false,
            'headers'     => array_merge(['User-Agent' => 'GVCR-ReviewsBot/1.0'], $this->headers),
        ]);

        $out  = [];
        $seen = [];

        foreach ($codes as $code) {
            [$url, $query, $json] = $this->buildRequestParts($code, $lang, $limit, $start);
            $cacheKey = 'reviews:httpjson:'.$this->slug.':'.sha1(json_encode([$this->method, $url, $query, $json]));

            $rows = Cache::remember($cacheKey, $this->ttl, function () use ($client, $url, $query, $json) {
                try {
                    $opts = [];
                    if (!empty($query)) $opts['query'] = $query;
                    if (!empty($json) && $this->method !== 'GET') $opts['json'] = $json;

                    $res = $client->request($this->method, $url, $opts);
                    if ($res->getStatusCode() !== 200) {
                        if (config('app.debug')) {
                            Log::warning('[HttpJsonReviewSource] Non-200', [
                                'slug' => $this->slug,
                                'code' => $res->getStatusCode(),
                                'body' => mb_substr((string) $res->getBody(), 0, 400),
                            ]);
                        }
                        return [];
                    }

                    $data = json_decode((string) $res->getBody(), true);
                    if (!is_array($data)) return [];
                    $list = Arr::get($data, $this->listPath, []);
                    return is_array($list) ? $list : [];
                } catch (RequestException $e) {
                    Log::warning('[HttpJsonReviewSource] HTTP error', ['slug' => $this->slug, 'err' => $e->getMessage()]);
                    return [];
                } catch (\Throwable $e) {
                    Log::warning('[HttpJsonReviewSource] Unknown error', ['slug' => $this->slug, 'err' => $e->getMessage()]);
                    return [];
                }
            });

            foreach ($rows as $r) {
                $norm = $this->mapRow($r, $code);

                if ($minRating > 0 && (int)($norm['rating'] ?? 0) < $minRating) continue;
                if (!$this->passesProviderFilter($r)) continue;

                $rid = (string)($norm['provider_review_id'] ?? md5(json_encode($norm)));
                if (isset($seen[$rid])) continue;
                $seen[$rid] = true;

                $out[] = $norm;
                if (count($out) >= $minWanted) break 2;
            }
        }

        return array_slice($out, 0, $minWanted);
    }

    /* ================= Helpers ================= */

    protected function buildRequestParts(?string $code, string $lang, int $limit, int $start): array
    {
        $repl = [
            '{product_code}' => (string)($code ?? ''),
            '{language}'     => (string)$lang,
            '{api_key}'      => (string)($this->apiKey ?? ''),
            '{limit}'        => (string)$limit,
            '{start}'        => (string)$start,
        ];

        $url   = strtr($this->url, $repl);
        $query = $this->deepReplace($this->query, $repl);
        $json  = $this->deepReplace($this->payload, $repl);

        return [$url, $query, $json];
    }

    protected function mapRow(array $row, ?string $code): array
    {
        $get = fn(string $path, $default = null) => Arr::get($row, $path, $default);

        $rating = (int) round((float) ($get($this->map['rating'], 0)));
        $rating = max(0, min(5, $rating));

        $author = null;
        $authorPath = $this->map['author_name'] ?? null;
        if (is_array($authorPath)) {
            foreach ($authorPath as $p) {
                $val = trim((string)($get($p) ?? ''));
                if ($val !== '') { $author = $val; break; }
            }
        } else {
            $author = trim((string)($get((string)$authorPath) ?? ''));
        }

        $title  = trim((string)($get($this->map['title']) ?? ''));
        $body   = trim((string)($get($this->map['body']) ?? ''));
        $date   = $this->normalizeIso($get($this->map['date']) ?? null);
        $rid    = (string)($get($this->map['provider_review_id']) ?? md5(json_encode($row)));
        $pcode  = (string)($get($this->map['product_code']) ?? ($code ?? ''));

        $tourId = null;
        if ($pcode !== '' && !empty($this->productMap)) {
            $rev = array_flip($this->productMap);
            if (isset($rev[$pcode])) $tourId = (int)$rev[$pcode];
        }

        $lang = null;
        if (!empty($this->map['language'])) {
            $lang = $get($this->map['language']) ?? null;
        }

        $norm = [
            'rating'             => $rating,
            'title'              => $title ?: null,
            'body'               => $body,
            'author_name'        => $author ?: null,
            'date'               => $date,
            'provider_review_id' => $rid,
            'product_code'       => $pcode ?: null,
            'product_id'            => $tourId,
            'language'           => $lang,
        ];

        foreach ($this->extrasMap as $key => $path) {
            $norm[$key] = Arr::get($row, $path);
        }

        return $norm;
    }

    protected function passesProviderFilter(array $rawRow): bool
    {
        $provFilter = (array)($this->filters['provider'] ?? []);
        if (empty($provFilter)) return true;

        $path = (string)($provFilter['path'] ?? 'provider');
        $include = array_map('strval', (array)($provFilter['include'] ?? []));
        if (empty($include)) return true;

        $val = Arr::get($rawRow, $path);
        if ($val === null) return false;

        $v = mb_strtolower(trim((string)$val));
        foreach ($include as $acc) {
            if ($v === mb_strtolower(trim((string)$acc))) return true;
        }
        return false;
    }

    protected function codesFor($tourId): array
    {
        if (!empty($tourId)) {
            $key = (string)$tourId;
            if (!empty($this->productMap[$key])) {
                return [(string)$this->productMap[$key]];
            }
        }

        $codes = [];
        foreach ($this->productMap as $c) {
            if (is_string($c) && trim($c) !== '') $codes[] = trim($c);
        }
        return array_values(array_unique($codes));
    }

    protected function deepReplace(array $arr, array $repl): array
    {
        $out = [];
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $out[$k] = $this->deepReplace($v, $repl);
            } elseif (is_string($v)) {
                $val = strtr($v, $repl);
                // Coerción simple
                if (preg_match('/^-?\d+$/', $val)) {
                    $out[$k] = (int) $val;
                } else {
                    $out[$k] = $val;
                }
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    protected function normalizeIso($raw): ?string
    {
        if ($raw === null || $raw === '') return null;
        if (is_numeric($raw)) {
            $ts = (int)$raw;
            if ($ts > 9999999999) $ts = (int) round($ts / 1000);
            try { return now()->setTimestamp($ts)->toIso8601String(); } catch (\Throwable) { return null; }
        }
        try { return \Carbon\Carbon::parse($raw)->toIso8601String(); } catch (\Throwable) { return null; }
    }

    protected function decodeJsonSafe($raw): array
    {
        if (is_array($raw)) return $raw;
        if (!is_string($raw) || $raw === '') return [];
        try { return (array) json_decode($raw, true, 512, JSON_THROW_ON_ERROR); }
        catch (\Throwable) { return []; }
    }

    /* ====== Token resolvers ====== */

    /** Reemplaza strings tipo "{env:FOO}" o "{config:services.x.y}" por su valor. */
    protected function resolveDynamicString(?string $v): ?string
    {
        if (!is_string($v) || $v === '') return $v;

        // {env:VAR}
        if (str_starts_with($v, '{env:') && str_ends_with($v, '}')) {
            $key = substr($v, 5, -1); // quita "{env:" y "}"
            return env($key, '');
        }

        // {config:path}
        if (str_starts_with($v, '{config:') && str_ends_with($v, '}')) {
            $path = substr($v, 8, -1); // quita "{config:" y "}"
            $val = config($path, '');
            return is_scalar($val) ? (string) $val : '';
        }

        return $v;
    }
/** Aplica resolveDynamicString() recursivamente a arrays (resuelve claves y valores). */
protected function resolveDynamicTokens(array $arr): array
{
    $out = [];
    foreach ($arr as $k => $v) {
        // Resolver la clave si viene con tokens {env:...} o {config:...}
        $newKey = is_string($k) ? $this->resolveDynamicString($k) : $k;
        if ($newKey === '' || $newKey === null) {
            // Evita claves vacías/nulas; conserva la original como fallback
            $newKey = $k;
        }

        // Resolver el valor (recursivo si es array)
        if (is_array($v)) {
            $out[$newKey] = $this->resolveDynamicTokens($v);
        } elseif (is_string($v)) {
            $out[$newKey] = $this->resolveDynamicString($v);
        } else {
            $out[$newKey] = $v;
        }
    }
    return $out;
}

}
