<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\TourType;                     // 👈
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\ItineraryItemTranslation;
use App\Models\AmenityTranslation;
use App\Models\FaqTranslation;
use App\Models\TourTypeTranslation;          // 👈
use App\Services\Contracts\TranslatorInterface;

class TranslationSeeder extends Seeder
{
    protected array $locales = ['es', 'en', 'fr', 'pt', 'de'];

    public function run(): void
    {
        $this->clearTranslations();

        /** @var TranslatorInterface $translator */
        $translator = app(TranslatorInterface::class);

        // 👇 Nuevo
        $this->translateTourTypes($translator);

        $this->translateTours($translator);
        $this->translateItineraries($translator);
        $this->translateItineraryItems($translator);
        $this->translateAmenities($translator);
        $this->translateFaqs($translator);

        $this->command?->info('✅ All translations regenerated successfully.');
    }

    protected function clearTranslations(): void
    {
        // Si tus tablas tienen FK con cascade está OK truncar
        TourTypeTranslation::truncate();      // 👈 Nuevo
        TourTranslation::truncate();
        ItineraryTranslation::truncate();
        ItineraryItemTranslation::truncate();
        AmenityTranslation::truncate();
        FaqTranslation::truncate();

        $this->command?->warn('🧹 Previous translations removed.');
    }

    // 👇 Nuevo: TourType (name, description, duration)
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
            // 👇 Name: preserva lo de fuera de los paréntesis
            $name = $translator->translatePreserveOutsideParentheses($origName, $locale);
            // 👇 Overview: traducción normal
            $overview = $translator->translate($origOverview, $locale);

            \App\Models\TourTranslation::updateOrCreate(
                ['tour_id' => $tour->getKey(), 'locale' => $locale],
                [
                    'tour_id'  => $tour->getKey(),
                    'locale'   => $locale,
                    'name'     => $name,
                    'overview' => $overview,
                ]
            );
        }
    }

    $this->command?->info('🎯 Tours translated (name preserves text outside parentheses).');
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

    /**
     * Generic translator/persister for any model + translation model.
     */
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
