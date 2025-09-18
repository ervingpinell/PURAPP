<?php

namespace App\Services\Reviews\Drivers;

use App\Models\ReviewProvider;
use App\Services\Reviews\Drivers\Contracts\ReviewSource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ViatorReviewSource implements ReviewSource
{
    protected ?string $apiKey = null;
    protected array $productMap = [];
    protected string $baseUrl;
    protected string $apiKeyHeader = 'exp-api-key';
    protected bool $debug = false;

    public function __construct()
    {
        $this->baseUrl      = (string) (config('reviews.viator.base')   ?? env('VIATOR_REVIEWS_BASE', 'https://api.sandbox.viator.com/partner/reviews/product'));
        $this->apiKeyHeader = (string) (config('reviews.viator.header') ?? env('VIATOR_API_KEY_HEADER', 'exp-api-key'));
        $this->debug        = (bool) filter_var(env('VIATOR_REVIEWS_DEBUG', false), FILTER_VALIDATE_BOOL);

        $prov = ReviewProvider::where('slug', 'viator')->first();
        $this->apiKey = $prov ? (string) $prov->getSetting('api_key') : null;

        // Cachea el product_map 24h
        $this->productMap = Cache::remember('reviews:viator:product_map', 86400, function () use ($prov) {
            return (array) ($prov ? $prov->getSetting('product_map', []) : []);
        });
    }

    /**
     * @param array{tour_id?:int|string|null, language?:string|null, limit?:int, min?:int} $opts
     */
    public function fetch(array $opts = []): array
    {
        if (empty($this->apiKey)) {
            $this->log('warn', 'Falta API key para Viator.');
            return [];
        }

        $want    = max(1, (int)($opts['limit'] ?? 12));
        $minWant = max($want, (int)($opts['min'] ?? $want));

        // Códigos a consultar
        $codes = [];
        if (!empty($opts['tour_id'])) {
            $tid = (string) $opts['tour_id'];
            if (!empty($this->productMap[$tid])) $codes[] = $this->productMap[$tid];
        }
        if (empty($codes)) {
            foreach ($this->productMap as $c) if (is_string($c) && $c !== '') $codes[] = $c;
        }
        $codes = array_values(array_unique($codes));
        if (empty($codes)) {
            $this->log('info', 'product_map vacío o sin coincidencia.');
            return [];
        }

        $client = new Client([
            'timeout' => 12.0,
            'headers' => [
                'User-Agent' => 'GVCR-ReviewsBot/1.0 (+https://greenvacationscr.com)',
            ],
            'http_errors' => false,
        ]);

        $collected = [];
        $seen = [];

        foreach ($codes as $code) {
            $start = 1;
            $pages = 0;

            while (count($collected) < $minWant && $pages < 5) {
                $payload = [
                    'productCode'                => $code,
                    'count'                      => min(50, $want),
                    'start'                      => $start,
                    'provider'                   => 'ALL',
                    'sortBy'                     => 'MOST_RECENT',
                    'reviewsForNonPrimaryLocale' => true,
                    'showMachineTranslated'      => true,
                ];

                $headers = [
                    $this->apiKeyHeader => $this->apiKey,
                    'Accept'            => 'application/json;version=2.0',
                    'Content-Type'      => 'application/json',
                    'Accept-Language'   => 'en-US,es-ES;q=0.9', // sugerencia neutra
                ];

                [$rows, $status] = $this->postWithBackoff($client, $this->baseUrl, $headers, $payload);

                if ($status !== 200) {
                    $this->log('warn', 'Viator non-200', compact('status','code','start'));
                    break;
                }

                if (empty($rows)) break;

                foreach ($rows as $r) {
                    $norm = $this->normalizeRow($r, $code);
                    $rid  = (string) ($norm['provider_review_id'] ?? md5(json_encode($norm)));
                    if (isset($seen[$rid])) continue;
                    $seen[$rid] = true;
                    $collected[] = $norm;
                    if (count($collected) >= $minWant) break 3;
                }

                $start += (int) $payload['count'];
                $pages++;
            }
        }

        return array_slice($collected, 0, $minWant);
    }

    /** POST con backoff ligero */
    protected function postWithBackoff(Client $client, string $url, array $headers, array $payload): array
    {
        $attempts = 0;
        $delayMs = 250;

        while (true) {
            $attempts++;
            try {
                $res = $client->request('POST', rtrim($url, '/'), [
                    'headers' => $headers,
                    RequestOptions::JSON => $payload,
                ]);

                $status = $res->getStatusCode();
                if ($status !== 200) {
                    if ($this->debug) {
                        $this->log('debug', 'Respuesta no 200', [
                            'status' => $status,
                            'body'   => mb_substr((string) $res->getBody(), 0, 600),
                        ]);
                    }
                    // Reintenta solo ante 5xx
                    if ($status >= 500 && $status < 600 && $attempts < 3) {
                        usleep($delayMs * 1000);
                        $delayMs *= 2;
                        continue;
                    }
                    return [[], $status];
                }

                $data = json_decode((string) $res->getBody(), true);
                $rows = Arr::get($data, 'reviews', []);
                if (!is_array($rows)) $rows = [];

                return [$rows, 200];
            } catch (RequestException $e) {
                if ($attempts < 3) {
                    usleep($delayMs * 1000);
                    $delayMs *= 2;
                    continue;
                }
                $this->log('warn', 'Excepción HTTP', ['msg' => $e->getMessage()]);
                return [[], null];
            } catch (\Throwable $e) {
                $this->log('warn', 'Excepción desconocida', ['msg' => $e->getMessage()]);
                return [[], null];
            }
        }
    }

    protected function normalizeRow(array $r, string $requestedCode): array
    {
        $userName     = $this->displayName($r) ?? ($r['userName'] ?? __('reviews.anonymous'));
        $publishedIso = $r['publishedDate'] ?? $this->normalizeIso($this->firstNonEmpty($r, [
            'published','publishDate','submissionDate','reviewDate','created','createdAt','createdTime',
            'reviewSubmissionTime','dates.published','dates.reviewDate','dates.created'
        ]));

        $rating = (float)($r['rating']
            ?? $this->firstNonEmpty($r, ['overallRating','score','ratingOverall'])
            ?? 0);

        $title = $r['title'] ?? $this->firstNonEmpty($r, ['headline','summary']);
        $text  = $r['text']  ?? $this->firstNonEmpty($r, ['content','reviewText','body','comments']);

        return [
            'rating'             => (int) round($rating),
            'title'              => $title ?: null,
            'body'               => $text  ?: '',
            'author_name'        => $userName,
            'date'               => $publishedIso,
            'provider_review_id' => (string) ($r['reviewId'] ?? $r['id'] ?? md5(json_encode($r))),
            'product_code'       => $r['productCode'] ?? $requestedCode,
        ];
    }

    // ---------- utilidades ----------

    protected function firstNonEmpty(array $arr, array $keys)
    {
        foreach ($keys as $key) {
            $val = data_get($arr, $key);
            if (is_string($val)) $val = trim($val);
            if ($val !== null && $val !== '' && $val !== []) return $val;
        }
        return null;
    }

    protected function normalizeIso($raw): ?string
    {
        if ($raw === null || $raw === '') return null;
        if (is_numeric($raw)) {
            $ts = (int) $raw;
            if ($ts > 9999999999) $ts = (int) round($ts / 1000);
            try { return now()->setTimestamp($ts)->toIso8601String(); } catch (\Throwable) { return null; }
        }
        try { return \Carbon\Carbon::parse($raw)->toIso8601String(); } catch (\Throwable) { return null; }
    }

protected function displayName(array $r): ?string
{
    $anon = $this->firstNonEmpty($r, [
        'anonymous','isAnonymous','user.anonymous','reviewer.anonymous'
    ]);
    if (in_array($anon, [true, 'true', 1, '1'], true)) {
        return __('reviews.anonymous');
    }

    $direct = $this->firstNonEmpty($r, [
        'user.name','user.displayName','reviewer.name','reviewer.displayName',
        'author','authorName','authorDisplayName','consumerName','viatorConsumerName','reviewerName',
    ]);
    if ($direct) return $direct;

    $first = $this->firstNonEmpty($r, ['user.firstName','reviewer.firstName','consumer.firstName','traveler.firstName']);
    $last  = $this->firstNonEmpty($r,  ['user.lastName','reviewer.lastName','consumer.lastName','traveler.lastName']);
    if ($first && $last) return trim($first.' '.$last);
    if ($first) return $first;
    if ($last)  return $last;

    $fi = $this->firstNonEmpty($r, ['user.firstInitial','reviewer.firstInitial']);
    $li = $this->firstNonEmpty($r, ['user.lastInitial','reviewer.lastInitial']);
    if ($fi || $li) return trim(($fi ?: '') . ($li ? ' ' . $li : ''));
    return null;
}



    protected function log(string $level, string $msg, array $ctx = []): void
    {
        if ($level === 'warn') Log::warning('[Viator] '.$msg, $ctx);
        elseif ($level === 'debug') { if ($this->debug) Log::debug('[Viator] '.$msg, $ctx); }
        else Log::info('[Viator] '.$msg, $ctx);
    }
}
