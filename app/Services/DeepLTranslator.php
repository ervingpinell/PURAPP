<?php

namespace App\Services;

use App\Services\Contracts\TranslatorInterface;
use DeepL\Translator as DeepL;
use DeepL\DeepLException;
use DeepL\TooManyRequestsException;

class DeepLTranslator implements TranslatorInterface
{
    private DeepL $client;
    private string $formality;
    private string $enVariant;
    private string $ptVariant;

    // Reintentos / backoff (ajusta a tu gusto)
    private int $maxRetries = 6;         // hasta ~6 reintentos
    private float $baseDelay = 0.5;      // segundos
    private float $maxDelay  = 8.0;      // tope de espera

    // Base mapping para targets
    private const BASE_TARGET_MAP = [
        'es'    => 'ES',
        'fr'    => 'FR',
        'de'    => 'DE',
        'en-us' => 'EN-US',
        'en-gb' => 'EN-GB',
        'pt-pt' => 'PT-PT',
        'pt-br' => 'PT-BR',
    ];

    // Normaliza detección a 2 letras
    private const BASE_LANG = [
        'EN' => 'en', 'EN-US' => 'en', 'EN-GB' => 'en',
        'ES' => 'es',
        'FR' => 'fr',
        'PT' => 'pt', 'PT-PT' => 'pt', 'PT-BR' => 'pt',
        'DE' => 'de',
    ];

    public function __construct(?string $apiKey = null)
    {
        $apiKey = $apiKey
            ?? config('services.deepl.auth_key')
            ?? env('DEEPL_AUTH_KEY');

        if (!$apiKey || $apiKey === '') {
            throw new \InvalidArgumentException('DEEPL_AUTH_KEY is missing. Set it in .env and config/services.php.');
        }

        $this->client    = new DeepL($apiKey);
        $this->formality = (string) config('services.deepl.formality', 'default');     // default|less|more
        $this->enVariant = strtolower((string) config('services.deepl.en_variant', 'en-US')); // en-US|en-GB
        $this->ptVariant = strtolower((string) config('services.deepl.pt_variant', 'pt-BR')); // pt-BR|pt-PT
    }

    /** Detecta idioma (opcional en tu flujo) */
    public function detect(string $text): ?string
    {
        $text = trim($text);
        if ($text === '') return null;

        $result = $this->withRetry(fn () => $this->client->detectLanguage($text));
        $code   = strtoupper($result->language);  // EN, EN-US, PT-BR, etc.
        return self::BASE_LANG[$code] ?? strtolower(substr($code, 0, 2));
    }

    /** Traduce un texto a un locale objetivo */
    public function translate(string $text, string $targetLocale): string
    {
        $text = (string) $text;
        if ($text === '') return '';

        $target = $this->mapTarget($targetLocale);

        $res = $this->withRetry(fn () =>
            $this->client->translateText($text, null, $target, [
                'formality' => $this->formality,
            ])
        );

        // El SDK devuelve DeepL\TextResult o array de ellos; aquí esperamos un único objeto
        return is_array($res) ? ($res[0]->text ?? $text) : ($res->text ?? $text);
    }

    /** Traduce el mismo texto a todos los locales soportados */
    public function translateAll(string $text): array
    {
        $text = (string) $text;
        if ($text === '') {
            return ['es' => '', 'en' => '', 'fr' => '', 'pt' => '', 'de' => ''];
        }

        $out = [];
        foreach (['es', 'en', 'fr', 'pt', 'de'] as $locale) {
            $out[$locale] = $this->translate($text, $locale); // ya incluye withRetry()
        }
        return $out;
    }

    /** Traduce preservando lo de FUERA de paréntesis */
    public function translatePreserveOutsideParentheses(string $text, string $targetLocale): string
    {
        $text = (string) $text;
        if ($text === '') return '';

        $parts = preg_split('/(\([^()]*\))/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false || count($parts) === 1) {
            return $this->translate($text, $targetLocale);
        }

        $out = '';
        foreach ($parts as $part) {
            if ($part === '') continue;

            if ($part[0] === '(' && substr($part, -1) === ')') {
                $inner = substr($part, 1, -1);
                $translatedInner = $this->translate($inner, $targetLocale); // withRetry()
                $out .= '(' . $translatedInner . ')';
            } else {
                // fuera de paréntesis NO se traduce
                $out .= $part;
            }
        }

        return $out;
    }

    /** Mapear locale app -> target DeepL */
    private function mapTarget(string $locale): string
    {
        $key = strtolower($locale);

        if ($key === 'en') return strtoupper($this->enVariant); // EN-US / EN-GB
        if ($key === 'pt') return strtoupper($this->ptVariant); // PT-BR / PT-PT

        if (isset(self::BASE_TARGET_MAP[$key])) {
            return self::BASE_TARGET_MAP[$key];
        }
        return strtoupper($key);
    }

    /**
     * Ejecuta una llamada al SDK con reintentos/backoff en 429/503 y errores de red.
     * - Jitter para evitar sincronización de oleadas (thundering herd).
     */
    private function withRetry(callable $fn)
    {
        $attempt = 0;
        beginning:
        try {
            return $fn();
        } catch (TooManyRequestsException|\GuzzleHttp\Exception\TransferException $e) {
            if ($attempt >= $this->maxRetries) {
                throw $e;
            }
            $this->sleepBackoff($attempt++);
            goto beginning;
        } catch (DeepLException $e) {
            // 503 u otros temporales: también reintentar
            if ($attempt >= $this->maxRetries) {
                throw $e;
            }
            $this->sleepBackoff($attempt++);
            goto beginning;
        }
    }

    private function sleepBackoff(int $attempt): void
    {
        $exp    = $this->baseDelay * (2 ** $attempt);
        $jitter = mt_rand(0, 1000) / 1000; // 0..1s
        $delay  = min($this->maxDelay, $exp + $jitter);
        usleep((int) round($delay * 1_000_000));
    }
}
