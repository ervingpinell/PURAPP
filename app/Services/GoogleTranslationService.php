<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;
use Stichoza\GoogleTranslate\GoogleTranslate;

class GoogleTranslationService
{
    public static function translate($text, $targetLanguage = 'es', $sourceLanguage = null)
    {
        try {
            if (empty($text)) return $text;

            $tr = new GoogleTranslate();
            $tr->setTarget($targetLanguage);
            if ($sourceLanguage) $tr->setSource($sourceLanguage);

            return $tr->translate($text);
        } catch (\Exception $e) {
            return $text;
        }
    }

    public static function detectLanguage($text): string
    {
        $spanishWords = ['el', 'la', 'de', 'que', 'y', 'en', 'un', 'una', 'es', 'por'];
        $lower = mb_strtolower($text);
        $score = 0;

        foreach ($spanishWords as $word) {
            if (str_contains($lower, " $word ") || str_starts_with($lower, "$word ") || str_ends_with($lower, " $word")) {
                $score++;
            }
        }

        return $score >= 2 ? 'es' : 'en';
    }

    public static function matchCase(string $original, string $translated): string
    {
        if (mb_strtoupper($original) === $original) return mb_strtoupper($translated);
        if (mb_strtolower($original) === $original) return mb_strtolower($translated);
        if (ucfirst(mb_strtolower($original)) === $original) return ucfirst(mb_strtolower($translated));

        return $translated;
    }

public static function preserveStructure(string $original, string $targetLang, string $sourceLang = null): string
{
    if (preg_match('/^(.*?)\((.*?)\)$/', $original, $matches)) {
        $before = trim($matches[1]);   // ❗ Esto se mantiene igual, no se traduce
        $inside = trim($matches[2]);   // ✅ Solo esto se traduce

        $translatedInside = self::translate($inside, $targetLang, $sourceLang);
        $translatedInside = self::matchCase($inside, $translatedInside);

        return "{$before} ({$translatedInside})";
    }

    // En caso de que no tenga paréntesis, se traduce todo normal
    $translated = self::translate($original, $targetLang, $sourceLang);
    return self::matchCase($original, $translated);
}



    public static function translateAndSaveForLocales($model, $fields, $translationModel, $foreignKey, $locales = ['en', 'pt', 'fr', 'de', 'es'])
    {
        $originalLocale = null;

        foreach ($fields as $field) {
            if (!empty($model->$field)) {
                $originalLocale = self::detectLanguage($model->$field);
                break;
            }
        }

        if (!$originalLocale) return;

        // Guardar versión original
        self::translateAndSave($model, $fields, $originalLocale, $translationModel, $foreignKey, $isOriginal = true);

        // Traducir a los otros idiomas
        foreach ($locales as $locale) {
            if ($locale === $originalLocale) continue;

            self::translateAndSave($model, $fields, $locale, $translationModel, $foreignKey, false, $originalLocale);
        }
    }

    public static function translateAndSave($model, $fields, $locale, $translationModel, $foreignKey, $isOriginal = false, $sourceLang = null)
    {
        $existing = $translationModel::where($foreignKey, $model->getKey())
            ->where('locale', $locale)
            ->first();

        if ($existing) return $existing;

        $data = [
            $foreignKey => $model->getKey(),
            'locale' => $locale,
        ];

        foreach ($fields as $field) {
            $original = $model->$field;

            if ($isOriginal) {
                $data[$field] = $original;
            } else {
                $data[$field] = self::preserveStructure($original, $locale, $sourceLang);
            }
        }

        return $translationModel::create($data);
    }

    // Específicos
    public static function translateAndSaveTour(Tour $tour, string $locale)
    {
        return self::translateAndSave($tour, ['name', 'overview'], $locale, TourTranslation::class, 'tour_id');
    }

    public static function translateAndSaveItinerary(Itinerary $itinerary, string $locale)
    {
        return self::translateAndSave($itinerary, ['name', 'description'], $locale, ItineraryTranslation::class, 'itinerary_id');
    }

    public static function translateAndSaveItineraryItem(ItineraryItem $item, string $locale)
    {
        return self::translateAndSave($item, ['title', 'description'], $locale, ItineraryItemTranslation::class, 'item_id');
    }

    public static function translateAndSaveAmenity(Amenity $amenity, string $locale)
    {
        return self::translateAndSave($amenity, ['name'], $locale, AmenityTranslation::class, 'amenity_id');
    }

    public static function translateAndSaveFaq(Faq $faq, string $locale)
    {
        return self::translateAndSave($faq, ['question', 'answer'], $locale, FaqTranslation::class, 'faq_id');
    }
}
