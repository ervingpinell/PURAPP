<?php

namespace App\Services;

use App\Models\BrandingSetting;
use DeepL\TranslatorInterface;
use Illuminate\Support\Facades\Log;

class SeoTranslationService
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Translate a Spanish SEO field to all supported languages
     *
     * @param string $key The Spanish key (e.g., 'seo_home_title_es')
     * @param string $spanishText The Spanish text to translate
     * @return array Array of translation results ['lang' => 'translated_text']
     */
    public function translateSeoField(string $key, string $spanishText): array
    {
        $languages = ['en', 'fr', 'de', 'pt'];
        $results = [];

        foreach ($languages as $lang) {
            try {
                // Translate from Spanish to target language
                $targetLang = $lang === 'pt' ? 'pt-BR' : strtoupper($lang);
                
                $translated = $this->translator->translateText(
                    $spanishText,
                    'es',
                    $targetLang
                );

                $translatedText = (string) $translated;
                
                // Generate the translated key
                $translatedKey = str_replace('_es', "_{$lang}", $key);
                
                // Save the translation
                BrandingSetting::set($translatedKey, $translatedText);
                
                $results[$lang] = $translatedText;
                
                Log::info("SEO translated: {$key} -> {$translatedKey}", [
                    'original' => $spanishText,
                    'translated' => $translatedText,
                ]);

            } catch (\Exception $e) {
                Log::warning("SEO translation failed for {$key} to {$lang}: " . $e->getMessage());
                $results[$lang] = null;
            }
        }

        return $results;
    }

    /**
     * Translate all Spanish SEO fields in the request
     *
     * @param array $settings The settings array from the request
     * @return int Number of fields translated
     */
    public function translateAllSeoFields(array $settings): int
    {
        $count = 0;

        foreach ($settings as $key => $value) {
            // Only process Spanish SEO fields
            if (str_starts_with($key, 'seo_') && str_ends_with($key, '_es') && !empty($value)) {
                $this->translateSeoField($key, $value);
                $count++;
            }
        }

        return $count;
    }
}
