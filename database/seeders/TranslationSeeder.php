<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\TourType;
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;
use App\Models\TourTypeTranslation;

use App\Models\Policy;                 // 👈
use App\Models\PolicyTranslation;      // 👈

use App\Services\Contracts\TranslatorInterface;

class TranslationSeeder extends Seeder
{
    protected array $locales = ['es', 'en', 'fr', 'pt', 'de'];

    public function run(): void
    {
        $this->clearTranslations();

        /** @var TranslatorInterface $translator */
        $translator = app(TranslatorInterface::class);

        $this->translateTourTypes($translator);
        $this->translatePolicies($translator);   // 👈 genera EN/FR/PT/DE tomando ES como fuente

        $this->translateTours($translator);
        $this->translateItineraries($translator);
        $this->translateItineraryItems($translator);
        $this->translateAmenities($translator);
        $this->translateFaqs($translator);

        $this->command?->info('✅ All translations regenerated successfully.');
    }

    protected function clearTranslations(): void
    {
        // Estas pueden truncarse sin problema
        TourTypeTranslation::truncate();
        TourTranslation::truncate();
        ItineraryTranslation::truncate();
        ItineraryItemTranslation::truncate();
        AmenityTranslation::truncate();
        FaqTranslation::truncate();

        // ❌ NO truncar policy_translations completo porque ES es la fuente.
        // ✅ Elimina solo los locales distintos de 'es'
        PolicyTranslation::where('locale', '!=', 'es')->delete();

        $this->command?->warn('🧹 Previous translations removed (policies ES preserved).');
    }

    protected function translatePolicies(TranslatorInterface $translator): void
    {
        $policies = Policy::where('is_active', true)->cursor();

        foreach ($policies as $policy) {
            // Fuente ES (debe existir porque la crea tu PoliciesSeeder)
            $src = PolicyTranslation::where('policy_id', $policy->getKey())
                ->where('locale', 'es')
                ->first();

            if (!$src) {
                $this->command?->warn("⚠️ Policy {$policy->policy_id} has no ES source. Skipping.");
                continue;
            }

            $titleSrc   = (string) ($src->title ?? '');
            $contentSrc = (string) ($src->content ?? '');

            // Solo traducimos a locales ≠ ES para no tocar la fuente
            $targets = array_diff($this->locales, ['es']);

            foreach ($targets as $locale) {
                $titleTr   = $translator->translate($titleSrc, $locale);
                $contentTr = $translator->translate($contentSrc, $locale);

                PolicyTranslation::updateOrCreate(
                    ['policy_id' => $policy->getKey(), 'locale' => $locale],
                    [
                        'title'   => $titleTr,
                        'content' => $contentTr,
                    ]
                );
                // (Opcional) Usar un pequeño throttle:
                // usleep(150000);
            }
        }

        $this->command?->info('📑 Policies translated (kept ES as source).');
    }

    /* ------ lo demás igual que ya tenías ------ */

    protected function translateTourTypes(TranslatorInterface $translator): void
    {
        $this->translateCollection(
            TourType::where('is_active', true)->cursor(),
            ['name', 'description', 'duration'],
            TourTypeTranslation::class,
            'tour_type_id',
            $translator
        );
        $this->command?->info('🏷️ Tour types translated.');
    }

    protected function translateTours(TranslatorInterface $translator): void
    {
        $collection = Tour::where('is_active', true)->cursor();

        foreach ($collection as $tour) {
            $origName = (string) ($tour->name ?? '');
            $origOverview = (string) ($tour->overview ?? '');

            foreach ($this->locales as $locale) {
                $name     = $translator->translatePreserveOutsideParentheses($origName, $locale);
                $overview = $translator->translate($origOverview, $locale);

                TourTranslation::updateOrCreate(
                    ['tour_id' => $tour->getKey(), 'locale' => $locale],
                    ['name' => $name, 'overview' => $overview]
                );
            }
        }

        $this->command?->info('🎯 Tours translated (name preserves parentheses).');
    }

    protected function translateItineraries(TranslatorInterface $translator): void
    {
        $this->translateCollection(
            Itinerary::where('is_active', true)->cursor(),
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
            ItineraryItem::where('is_active', true)->cursor(),
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
            Amenity::where('is_active', true)->cursor(),
            ['name'],
            AmenityTranslation::class,
            'amenity_id',
            $translator
        );
        $this->command?->info('💠 Amenities translated.');
    }

    protected function translateFaqs(TranslatorInterface $translator): void
    {
        $this->translateCollection(
            Faq::where('is_active', true)->cursor(),
            ['question', 'answer'],
            FaqTranslation::class,
            'faq_id',
            $translator
        );
        $this->command?->info('❓ FAQs translated.');
    }

    protected function translateCollection($collection, array $fields, string $translationModel, string $foreignKey, TranslatorInterface $translator): void
    {
        foreach ($collection as $model) {
            $fieldTranslations = [];
            foreach ($fields as $field) {
                $original = (string) ($model->{$field} ?? '');
                $fieldTranslations[$field] = $translator->translateAll($original);
            }

            foreach ($this->locales as $locale) {
                $payload = [
                    $foreignKey => $model->getKey(),
                    'locale'    => $locale,
                ];
                foreach ($fields as $field) {
                    $original = (string) ($model->{$field} ?? '');
                    $payload[$field] = $fieldTranslations[$field][$locale] ?? $original;
                }

                $translationModel::updateOrCreate(
                    [$foreignKey => $model->getKey(), 'locale' => $locale],
                    $payload
                );
            }
        }
    }
}
