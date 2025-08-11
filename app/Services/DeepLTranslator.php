<?php

namespace App\Services;

use App\Services\Contracts\TranslatorInterface;
use DeepL\Translator as DeepL;
use DeepL\DeepLException;

class DeepLTranslator implements TranslatorInterface
{
    private DeepL $client;
    private string $formality;
    private string $enVariant;
    private string $ptVariant;

    // Base mapping for DeepL target codes (overridable by config for EN/PT)
    private const BASE_TARGET_MAP = [
        'es'    => 'ES',
        'fr'    => 'FR',
        'de'    => 'DE',
        // Variants handled dynamically for 'en' and 'pt'
        'en-us' => 'EN-US',
        'en-gb' => 'EN-GB',
        'pt-pt' => 'PT-PT',
        'pt-br' => 'PT-BR',
    ];

    // Normalize detection to 2-letter app locales
    private const BASE_LANG = [
        'EN' => 'en', 'EN-US' => 'en', 'EN-GB' => 'en',
        'ES' => 'es',
        'FR' => 'fr',
        'PT' => 'pt', 'PT-PT' => 'pt', 'PT-BR' => 'pt',
        'DE' => 'de',
    ];

    public function __construct(?string $apiKey = null)
    {
        // Read from config/services.php -> 'deepl.auth_key' OR .env('DEEPL_AUTH_KEY')
        $apiKey = $apiKey
            ?? config('services.deepl.auth_key')
            ?? env('DEEPL_AUTH_KEY');

        if (!$apiKey || $apiKey === '') {
            throw new \InvalidArgumentException('DEEPL_AUTH_KEY is missing. Set it in .env and config/services.php.');
        }

        $this->client    = new DeepL($apiKey);
        $this->formality = (string) config('services.deepl.formality', 'default'); // default|less|more
        $this->enVariant = strtolower((string) config('services.deepl.en_variant', 'en-US')); // en-US|en-GB
        $this->ptVariant = strtolower((string) config('services.deepl.pt_variant', 'pt-BR')); // pt-BR|pt-PT
    }

public function detect(string $text): ?string
{
    $text = trim($text);
    if ($text === '') return null;

    $result = $this->client->detectLanguage($text); // <- exacto: detectLanguage
    $code   = strtoupper($result->language);        // e.g. EN, EN-US, PT-BR
    return self::BASE_LANG[$code] ?? strtolower(substr($code, 0, 2));
}


    public function translate(string $text, string $targetLocale): string
    {
        $text = (string) $text;
        if ($text === '') return '';

        $target = $this->mapTarget($targetLocale);

        // Allow auto source detection; pass formality when applicable
        $res = $this->client->translateText($text, null, $target, [
            'formality' => $this->formality,
        ]);

        return $res->text ?? $text;
    }

    public function translateAll(string $text): array
    {
        $text = (string) $text;
        if ($text === '') {
            return ['es' => '', 'en' => '', 'fr' => '', 'pt' => '', 'de' => ''];
        }

        $out = [];
        foreach (['es', 'en', 'fr', 'pt', 'de'] as $locale) {
            $out[$locale] = $this->translate($text, $locale);
        }
        return $out;
    }

    private function mapTarget(string $locale): string
    {
        $key = strtolower($locale);

        // dynamic variants for English & Portuguese
        if ($key === 'en') {
            return strtoupper($this->enVariant); // EN-US or EN-GB
        }
        if ($key === 'pt') {
            return strtoupper($this->ptVariant); // PT-BR or PT-PT
        }

        // fixed mappings and fallbacks
        if (isset(self::BASE_TARGET_MAP[$key])) {
            return self::BASE_TARGET_MAP[$key];
        }

        // fallback: attempt uppercase (e.g., 'es' => 'ES')
        return strtoupper($key);
    }

    // ... dentro de la clase DeepLTranslator

public function translatePreserveOutsideParentheses(string $text, string $targetLocale): string
{
    $text = (string) $text;
    if ($text === '') return '';

    // Divide manteniendo los grupos "(...)" en el array
    $parts = preg_split('/(\([^()]*\))/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

    // Si no hay paréntesis => traducir todo normal
    if ($parts === false || count($parts) === 1) {
        return $this->translate($text, $targetLocale);
    }

    $out = '';
    foreach ($parts as $part) {
        if ($part === '') continue;

        // Si es "(...)" => traduce solo el interior
        if ($part[0] === '(' && substr($part, -1) === ')') {
            $inner = substr($part, 1, -1);
            $translatedInner = $this->translate($inner, $targetLocale);
            $out .= '(' . $translatedInner . ')';
        } else {
            // Fuera de paréntesis: se deja exacto
            $out .= $part;
        }
    }

    return $out;
}


}
