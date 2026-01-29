<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Base models
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\ProductType;
use App\Models\Policy;
use App\Models\PolicySection;

// Translation models
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;
use App\Models\ProductTypeTranslation;
use App\Models\PolicyTranslation;
use App\Models\PolicySectionTranslation;

use App\Services\Contracts\TranslatorInterface;

class TranslationSeeder extends Seeder
{
    /**
     * Trabajamos SIEMPRE con locales cortos en DB.
     * Si en algún sitio aún existiera pt_BR, este seeder lo ignora y
     * reescribe/crea usando 'pt'.
     */
    protected array $locales = ['es', 'en', 'fr', 'pt', 'de'];

    public function run(): void
    {
        $this->clearTranslations();

        /** @var TranslatorInterface $translator */
        $translator = app(TranslatorInterface::class);

        $this->translateProductTypes($translator);
        $this->translatePolicies($translator);
        $this->translatePolicySections($translator);
        $this->translateTours($translator);
        $this->translateItineraries($translator);
        $this->translateItineraryItems($translator);
        $this->translateAmenities($translator);
        $this->translateFaqs($translator);

        $this->command?->info('✅ All translations regenerated successfully (locales: es,en,fr,pt,de).');
    }

    protected function clearTranslations(): void
    {
        // Limpieza completa de tablas regulares
        // ProductTypeTranslation::truncate(); // Handled in translateProductTypes
        TourTranslation::truncate();
        ItineraryTranslation::truncate();
        ItineraryItemTranslation::truncate();
        AmenityTranslation::truncate();
        FaqTranslation::truncate();

        // Policies/Sections: preserva ES como fuente
        PolicyTranslation::where('locale', '!=', 'es')->delete();
        PolicySectionTranslation::where('locale', '!=', 'es')->delete();

        $this->command?->warn('🧹 Previous translations removed (policies ES preserved).');
    }

    /**
     * POLICIES: usar 'name' (no 'title')
     */
    protected function translatePolicies(TranslatorInterface $translator): void
    {
        $policies = Policy::where('is_active', true)->with('translations')->get();

        foreach ($policies as $policy) {
            $src = $policy->translations->firstWhere('locale', 'es');

            if (!$src) {
                $this->command?->warn("⚠️ Policy {$policy->policy_id} ({$policy->name}) has no ES source. Skipping.");
                continue;
            }

            $nameSrc    = (string) ($src->name ?? $policy->name ?? '');
            $contentSrc = (string) ($src->content ?? '');

            foreach ($this->locales as $locale) {
                if ($locale === 'es') { // ES ya existe, no traducir
                    continue;
                }

                $targetLocale = $this->normalizeLocaleForTranslation($locale);
                $nameTr    = $translator->translate($nameSrc, $targetLocale);
                $contentTr = $translator->translate($contentSrc, $targetLocale);

                PolicyTranslation::updateOrCreate(
                    ['policy_id' => $policy->policy_id, 'locale' => $locale],
                    ['name' => $nameTr, 'content' => $contentTr]
                );
            }

            $this->command?->info("📄 Policy '{$policy->name}' translated");
        }

        $this->command?->info('📑 Policies translated (kept ES as source).');
    }

    /**
     * POLICY SECTIONS: usar 'name' (no 'title')
     */
    protected function translatePolicySections(TranslatorInterface $translator): void
    {
        $sections = PolicySection::with('translations')->get();

        foreach ($sections as $section) {
            $src = $section->translations->firstWhere('locale', 'es');

            if (!$src) {
                $this->command?->warn("⚠️ Section {$section->section_id} ({$section->name}) has no ES source. Skipping.");
                continue;
            }

            $nameSrc    = (string) ($src->name ?? $section->name ?? '');
            $contentSrc = (string) ($src->content ?? '');

            foreach ($this->locales as $locale) {
                if ($locale === 'es') continue;

                $targetLocale = $this->normalizeLocaleForTranslation($locale);
                $nameTr    = $translator->translate($nameSrc, $targetLocale);
                $contentTr = $translator->translate($contentSrc, $targetLocale);

                PolicySectionTranslation::updateOrCreate(
                    ['section_id' => $section->section_id, 'locale' => $locale],
                    ['name' => $nameTr, 'content' => $contentTr]
                );
            }
        }

        $this->command?->info('🧾 Policy sections translated.');
    }

    protected function translateProductTypes(TranslatorInterface $translator): void
    {
        $productTypes = ProductType::where('is_active', true)->get();

        foreach ($productTypes as $productType) {
            // Source is typically Spanish for fallback
            $sourceTranslation = $productType->translations->firstWhere('locale', 'es')
                ?? $productType->translations->firstWhere('locale', 'en');

            if (!$sourceTranslation) {
                continue;
            }

            $sourceName = $sourceTranslation->name;
            $sourceDesc = $sourceTranslation->description;
            // Duration is non-translatable text usually, but we keep it sync if needed, 
            // though InitialSetupSeeder sets it for ES/EN. 
            // If we want to carry it over to other languages as-is or translate:
            $sourceDuration = $sourceTranslation->duration;

            foreach ($this->locales as $locale) {
                // If translation exists and has a name, skip (preserve manual edits)
                $existing = $productType->translations->firstWhere('locale', $locale);
                if ($existing && !empty($existing->name)) {
                    continue;
                }

                // If existing is ES or EN, we definitely skip if it has data (already covered above)
                // but just double check to be safe
                if (($locale === 'es' || $locale === 'en') && $existing) {
                    continue;
                }

                $targetLocale = $this->normalizeLocaleForTranslation($locale);

                // Translate
                $nameTr = $translator->translate($sourceName, $targetLocale);
                $descTr = $translator->translate($sourceDesc, $targetLocale);
                // Duration usually numerical + word. Let's simple-copy or translate if simple.
                // For now, let's copy it or attempt translate if it's text.
                $durTr  = $sourceDuration ? $translator->translate($sourceDuration, $targetLocale) : null;

                ProductTypeTranslation::updateOrCreate(
                    ['product_type_id' => $productType->product_type_id, 'locale' => $locale],
                    [
                        'name' => $nameTr,
                        'description' => $descTr,
                        'duration' => $durTr
                    ]
                );
            }
        }

        $this->command?->info('🏷️ Product types translated (gaps filled, existing preserved).');
    }

    protected function translateTours(TranslatorInterface $translator): void
    {
        $products = Product::where('is_active', true)->get();

        foreach ($products as $product) {
            $origName     = (string) ($product->name ?? '');
            $origOverview = (string) ($product->overview ?? '');

            foreach ($this->locales as $locale) {
                $targetLocale = $this->normalizeLocaleForTranslation($locale);

                // preserva texto fuera de paréntesis para nombres
                $name     = $translator->translatePreserveOutsideParentheses($origName, $targetLocale);
                $overview = $translator->translate($origOverview, $targetLocale);

                TourTranslation::updateOrCreate(
                    ['product_id' => $product->tour_id, 'locale' => $locale],
                    ['name' => $name, 'overview' => $overview]
                );
            }

            $this->command?->info("🎯 Product '{$product->name}' translated");
        }

        $this->command?->info('🎯 Tours translated (name preserves parentheses).');
    }

    protected function translateItineraries(TranslatorInterface $translator): void
    {
        $this->translateCollection(
            Itinerary::where('is_active', true)->get(),
            ['name', 'description'],
            ItineraryTranslation::class,
            'itinerary_id',
            $translator
        );
        $this->command?->info('📘 Itineraries translated.');
    }

    protected function translateItineraryItems(TranslatorInterface $translator): void
    {
        $this->translateCollection(
            ItineraryItem::where('is_active', true)->get(),
            ['title', 'description'],
            ItineraryItemTranslation::class,
            'item_id',
            $translator
        );
        $this->command?->info('🧩 Itinerary items translated.');
    }

    protected function translateAmenities(TranslatorInterface $translator): void
    {
        $this->translateCollection(
            Amenity::where('is_active', true)->get(),
            ['name'],
            AmenityTranslation::class,
            'amenity_id',
            $translator
        );
        $this->command?->info('💎 Amenities translated.');
    }

    protected function translateFaqs(TranslatorInterface $translator): void
    {
        $this->translateCollection(
            Faq::where('is_active', true)->get(),
            ['question', 'answer'],
            FaqTranslation::class,
            'faq_id',
            $translator
        );
        $this->command?->info('❓ FAQs translated.');
    }

    /**
     * Método genérico para traducir colecciones
     */
    protected function translateCollection($collection, array $fields, string $translationModel, string $foreignKey, TranslatorInterface $translator): void
    {
        foreach ($collection as $model) {
            $fieldTranslations = [];

            // Pre-traducir todos los campos a todos los idiomas
            foreach ($fields as $field) {
                $original = (string) ($model->{$field} ?? '');
                $fieldTranslations[$field] = [];

                foreach ($this->locales as $locale) {
                    $targetLocale = $this->normalizeLocaleForTranslation($locale);
                    $fieldTranslations[$field][$locale] = $translator->translate($original, $targetLocale);
                }
            }

            // Guardar traducciones en locales cortos
            foreach ($this->locales as $locale) {
                $payload = [
                    $foreignKey => $model->getKey(),
                    'locale'    => $locale,
                ];

                foreach ($fields as $field) {
                    $payload[$field] = $fieldTranslations[$field][$locale] ?? (string) ($model->{$field} ?? '');
                }

                $translationModel::updateOrCreate(
                    [$foreignKey => $model->getKey(), 'locale' => $locale],
                    $payload
                );
            }
        }
    }

    /**
     * Normaliza el locale para el servicio de traducción (no para DB).
     * – DB siempre usa 'pt'
     * – DeepL: le pasamos 'pt' (tu DeepLTranslator ya decide pt-BR o pt-PT).
     */
    protected function normalizeLocaleForTranslation(string $locale): string
    {
        // El seeder siempre pide 'pt' al traductor para portugués
        return $locale === 'pt' ? 'pt' : $locale;
    }
}
