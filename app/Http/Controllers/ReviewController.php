<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function fetchReviews(Request $request)
    {
        $validated = $request->validate([
            'productCode' => 'required|string',
            'count'       => 'nullable|integer|min:1|max:50',
            'start'       => 'nullable|integer|min:1',
            'provider'    => 'nullable|in:VIATOR,TRIPADVISOR,ALL',
            'sortBy'      => 'nullable|string',
        ]);

        $payload = [
            'productCode'                => $validated['productCode'],
            'count'                      => $validated['count'] ?? 5,
            'start'                      => $validated['start'] ?? 1,
            'provider'                   => $validated['provider'] ?? 'ALL',
            'sortBy'                     => $validated['sortBy'] ?? 'MOST_RECENT',
            'reviewsForNonPrimaryLocale' => true,
            'showMachineTranslated'      => true,
        ];

        // Mapea locale app -> BCP47 para Viator
        $acceptLang = $this->mapLocale(app()->getLocale());

        try {
            $url = config('services.viator.reviews_base');
            $key = config('services.viator.key');

            $resp = Http::withHeaders([
                    'exp-api-key'     => $key,
                    'Accept'          => 'application/json;version=2.0',
                    'Accept-Language' => $acceptLang,
                ])
                ->timeout(12)
                ->retry(2, 300)
                ->post($url, $payload);

            if (!$resp->ok()) {
                Log::warning('Viator reviews non-200', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);

                return response()->json([
                    'reviews' => [],
                    'error'   => 'upstream_'.$resp->status(),
                ], 200);
            }

            $data = $resp->json();

            if (app()->environment('local')) {
                Log::debug('Sample upstream review payload', [
                    'keys'  => array_keys(($data['reviews'][0] ?? [])),
                    'first' => ($data['reviews'][0] ?? null),
                ]);
            }

            return response()->json([
                'reviews' => $this->mapReviews($data, $validated['productCode']),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Viator reviews exception', ['msg' => $e->getMessage()]);

            return response()->json([
                'reviews' => [],
                'error'   => 'exception',
            ], 200);
        }
    }

    /**
     * Adapta la respuesta al shape del JS:
     * avatarUrl, publishedDate (ISO), rating, userName, title, text, productTitle, productCode
     */
    private function mapReviews(array $data, string $requestedCode): array
    {
        $out = [];

        foreach (($data['reviews'] ?? []) as $r) {
            // Prioriza campos raíz
            $userName      = $r['userName']      ?? $this->displayName($r) ?? 'Anónimo';
            $publishedIso  = $r['publishedDate'] ?? null; // ya es ISO date-time
            $avatarUrl     = $r['avatarUrl']     ?? $this->firstNonEmpty($r, [
                'user.avatar', 'user.avatarUrl', 'avatar', 'profilePhoto', 'reviewer.avatarUrl', 'reviewer.avatar',
            ]);
            $rating        = (float)($r['rating']
                                ?? $this->firstNonEmpty($r, ['overallRating', 'score', 'ratingOverall'])
                                ?? 0);
            $title         = $r['title'] ?? $this->firstNonEmpty($r, ['headline', 'summary']);
            $text          = $r['text']  ?? $this->firstNonEmpty($r, ['content', 'reviewText', 'body', 'comments']);

            // Nombre del producto (para cabeceras del carrusel)
            $productTitle  = $r['productTitle']
                ?? $this->firstNonEmpty($r, ['product.title', 'productName', 'titleOfProduct'])
                ?? '';

            // Si no vino publishedDate, intenta normalizar de otras llaves
            if (!$publishedIso) {
                $rawDate = $this->firstNonEmpty($r, [
                    'published', 'publishDate', 'publishedDate', 'submissionDate', 'reviewDate',
                    'created', 'createdTime', 'createdAt', 'reviewSubmissionTime',
                    'dates.published', 'dates.reviewDate', 'dates.created',
                ]);
                $publishedIso = $this->normalizeIso($rawDate);
            }

            $out[] = [
                'avatarUrl'     => $avatarUrl ?: null,
                'publishedDate' => $publishedIso,
                'rating'        => $rating,
                'userName'      => $userName,
                'title'         => $title ?: null,
                'text'          => $text  ?: null,

                // 👇 añadidos clave para que el front tenga siempre algo útil
                'productTitle'  => $productTitle,                 // string ('' si no vino nada)
                'productCode'   => $r['productCode'] ?? $requestedCode,
            ];
        }

        return $out;
    }

    /** Intenta encontrar un nombre mostrando varios esquemas (fallback si no hay userName raíz). */
    private function displayName(array $r): ?string
    {
        // respeta posibles flags de anonimato
        $anon = $this->firstNonEmpty($r, ['anonymous', 'isAnonymous', 'user.anonymous', 'reviewer.anonymous']);
        if ($anon === true || $anon === 'true' || $anon === 1 || $anon === '1') {
            return 'Anónimo';
        }

        // candidatos directos (anidados)
        $direct = $this->firstNonEmpty($r, [
            'user.name', 'user.displayName',
            'reviewer.name', 'reviewer.displayName',
            'author', 'authorName', 'authorDisplayName',
            'consumerName', 'viatorConsumerName', 'reviewerName',
        ]);
        if (!empty($direct)) {
            return $direct;
        }

        // combinaciones first/last
        $first = $this->firstNonEmpty($r, [
            'user.firstName', 'reviewer.firstName', 'consumer.firstName', 'traveler.firstName',
        ]);
        $last = $this->firstNonEmpty($r, [
            'user.lastName', 'reviewer.lastName', 'consumer.lastName', 'traveler.lastName',
        ]);
        if ($first && $last) return trim($first.' '.$last);
        if ($first)         return $first;
        if ($last)          return $last;

        // iniciales
        $fi = $this->firstNonEmpty($r, ['user.firstInitial', 'reviewer.firstInitial']);
        $li = $this->firstNonEmpty($r, ['user.lastInitial',  'reviewer.lastInitial']);
        if ($fi || $li) return trim(($fi ?: '').($li ? ' '.$li : ''));

        return null;
    }

    /** Devuelve el primer valor no vacío buscando por varias llaves (soporta "a.b.c"). */
    private function firstNonEmpty(array $arr, array $keys)
    {
        foreach ($keys as $key) {
            $val = data_get($arr, $key);
            if (is_string($val)) $val = trim($val);
            if ($val !== null && $val !== '' && $val !== []) {
                return $val;
            }
        }
        return null;
    }

    /** Normaliza a ISO 8601 si es posible; acepta ISO, timestamps (s/ms) o strings parseables. */
    private function normalizeIso($raw): ?string
    {
        if ($raw === null || $raw === '') return null;

        if (is_numeric($raw)) {
            $ts = (int) $raw;
            if ($ts > 9999999999) { // ms
                $ts = (int) round($ts / 1000);
            }
            try {
                return Carbon::createFromTimestampUTC($ts)->toIso8601String();
            } catch (\Throwable $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($raw)->toIso8601String();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Mapea locales comunes a BCP47 aceptados por Viator */
    private function mapLocale(?string $appLocale): string
    {
        $appLocale = $appLocale ?: 'en';
        return match ($appLocale) {
            'es', 'es_CR', 'es_MX', 'es-CR', 'es-MX' => 'es-ES',
            'pt', 'pt_BR', 'pt-BR'                    => 'pt-BR',
            'fr', 'fr_FR', 'fr-FR'                    => 'fr-FR',
            'de', 'de_DE', 'de-DE'                    => 'de-DE',
            default                                   => 'en-US',
        };
    }
}
