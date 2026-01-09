<?php

namespace App\Services;

use App\Services\Contracts\TranslatorInterface;
use DeepL\Translator as DeepL;
use DeepL\DeepLException;
use DeepL\TooManyRequestsException;
use Illuminate\Support\Facades\Log;

/**
 * DeepLTranslator
 *
 * Handles deepltranslator operations.
 */
class DeepLTranslator implements TranslatorInterface
{
    private ?DeepL $client = null;

    private string $formality;
    private string $enVariant;
    private string $ptVariant;

    private bool $enabled;
    private ?string $apiKey;

    private int $maxAttempts = 2;

    private const BASE_TARGET_MAP = [
        'es'    => 'ES',
        'fr'    => 'FR',
        'de'    => 'DE',
        'en-us' => 'EN-US',
        'en-gb' => 'EN-GB',
        'pt-pt' => 'PT-PT',
        'pt-br' => 'PT-BR',
    ];

    private const BASE_LANG = [
        'EN' => 'en', 'EN-US' => 'en', 'EN-GB' => 'en',
        'ES' => 'es',
        'FR' => 'fr',
        'PT' => 'pt', 'PT-PT' => 'pt', 'PT-BR' => 'pt',
        'DE' => 'de',
    ];

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey    = $apiKey
            ?? config('services.deepl.auth_key')
            ?? env('DEEPL_AUTH_KEY');

        $this->enabled   = (bool) (config('services.deepl.enabled', env('DEEPL_ENABLED', true)));
        $this->formality = (string) config('services.deepl.formality', 'default');            // default|less|more
        $this->enVariant = strtolower((string) config('services.deepl.en_variant', 'en-US')); // en-US|en-GB
        $this->ptVariant = strtolower((string) config('services.deepl.pt_variant', 'pt-BR')); // pt-BR|pt-PT

        if ($this->enabled && !empty($this->apiKey)) {
            $this->client = new DeepL($this->apiKey);
        }
    }

    /**
     * Normaliza cualquier variante a la etiqueta corta que usaremos en DB/UI.
     * pt, pt-br, pt_BR => pt
     */
    public static function normalizeLocaleCode(string $locale): string
    {
        $l = strtolower(trim($locale));
        $l = str_replace('-', '_', $l);

        return match ($l) {
            'pt', 'pt_br', 'pt-pt', 'pt_pt' => 'pt',
            'en', 'en_us', 'en-gb', 'en_gb' => 'en',
            'fr', 'fr_fr'                    => 'fr',
            'de', 'de_de'                    => 'de',
            'es', 'es_es', 'es_cr'           => 'es',
            default                          => substr($l, 0, 2),
        };
    }

    public function detect(string $text): ?string
    {
        $text = trim($text);
        if ($text === '' || !$this->client) return null;

        try {
            $attempt = 0;
            while (true) {
                try {
                    $result = $this->client->detectLanguage($text);
                    $code   = strtoupper($result->language);  // EN, EN-US, PT-BR, etc.
                    $base   = self::BASE_LANG[$code] ?? strtolower(substr($code, 0, 2));
                    return self::normalizeLocaleCode($base);
                } catch (TooManyRequestsException|DeepLException $e) {
                    if (++$attempt >= $this->maxAttempts) throw $e;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('DeepL detect failed', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function translate(string $text, string $targetLocale): string
    {
        $text = (string) $text;
        if ($text === '' || !$this->client) return $text;

        // Mapeamos a un objetivo DeepL, pero nuestra etiqueta **resultante** será 'pt' si es portugués
        $target = $this->mapTarget($targetLocale);

        $attempt = 0;
        while (true) {
            try {
                $res = $this->client->translateText($text, null, $target, [
                    'formality' => $this->formality,
                ]);

                if (is_array($res)) {
                    $first = $res[0]->text ?? null;
                    return is_string($first) ? $first : $text;
                }
                return $res->text ?? $text;

            } catch (TooManyRequestsException|DeepLException|\GuzzleHttp\Exception\TransferException $e) {
                if (++$attempt >= $this->maxAttempts) {
                    Log::warning('DeepL translate failed', [
                        'target' => $target, 'msg' => $e->getMessage()
                    ]);
                    return $text;
                }
            } catch (\Throwable $e) {
                Log::error('DeepL translate unexpected error', ['msg' => $e->getMessage()]);
                return $text;
            }
        }
    }

    public function translateAll(string $text): array
    {
        $text = (string) $text;
        // 💡 Siempre devolvemos claves cortas: es,en,fr,pt,de
        $locales = ['es','en','fr','pt','de'];

        $out = [];
        foreach ($locales as $loc) {
            $out[$loc] = $this->translate($text, $loc);
        }
        return $out;
    }

    public function translatePreserveOutsideParentheses(string $text, string $targetLocale): string
    {
        $text = (string) $text;
        if ($text === '' || !$this->client) return $text;

        $parts = preg_split('/(\([^()]*\))/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false || count($parts) === 1) {
            return $this->translate($text, $targetLocale);
        }

        $out = '';
        foreach ($parts as $part) {
            if ($part === '') continue;

            if ($part[0] === '(' && substr($part, -1) === ')') {
                $inner = substr($part, 1, -1);
                $out  .= '(' . $this->translate($inner, $targetLocale) . ')';
            } else {
                $out  .= $part;
            }
        }

        return $out;
    }

    /**
     * DeepL target: usamos variantes para mejor calidad,
     * pero nuestras claves/DB quedan en corto (pt).
     */
    private function mapTarget(string $locale): string
    {
        $key = strtolower(str_replace('_', '-', $locale));

        if ($key === 'en') return strtoupper($this->enVariant); // EN-US / EN-GB
        if ($key === 'pt') return strtoupper($this->ptVariant); // PT-BR / PT-PT

        return self::BASE_TARGET_MAP[$key] ?? strtoupper($key);
    }
}
