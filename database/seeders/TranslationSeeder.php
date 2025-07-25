<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
use App\Services\GoogleTranslationService;

class TranslationSeeder extends Seeder
{
    /**
     * Idiomas de destino (incluye 'es' para guardar versión original si no está en español).
     */
    protected $locales = ['en', 'pt', 'fr', 'de', 'es'];

    public function run(): void
    {
        $this->clearTranslations();

        $this->translateTours();
        $this->translateItineraries();
        $this->translateItineraryItems();
        $this->translateAmenities();
        $this->translateFaqs();

        $this->command->info('✅ Todas las traducciones se han regenerado correctamente.');
    }

    protected function clearTranslations(): void
    {
        TourTranslation::truncate();
        ItineraryTranslation::truncate();
        ItineraryItemTranslation::truncate();
        AmenityTranslation::truncate();
        FaqTranslation::truncate();

        $this->command->warn('🧹 Traducciones anteriores eliminadas.');
    }

    protected function translateTours(): void
    {
        Tour::where('is_active', true)->each(function ($tour) {
            GoogleTranslationService::translateAndSaveForLocales(
                $tour,
                ['name', 'overview'],
                TourTranslation::class,
                'tour_id',
                $this->locales
            );
        });

        $this->command->info('🎯 Tours traducidos');
    }

    protected function translateItineraries(): void
    {
        Itinerary::where('is_active', true)->each(function ($itinerary) {
            GoogleTranslationService::translateAndSaveForLocales(
                $itinerary,
                ['name', 'description'],
                ItineraryTranslation::class,
                'itinerary_id',
                $this->locales
            );
        });

        $this->command->info('📘 Itinerarios traducidos');
    }

    protected function translateItineraryItems(): void
    {
        ItineraryItem::where('is_active', true)->each(function ($item) {
            GoogleTranslationService::translateAndSaveForLocales(
                $item,
                ['title', 'description'],
                ItineraryItemTranslation::class,
                'item_id',
                $this->locales
            );
        });

        $this->command->info('🧩 Ítems de itinerario traducidos');
    }

    protected function translateAmenities(): void
    {
        Amenity::where('is_active', true)->each(function ($amenity) {
            GoogleTranslationService::translateAndSaveForLocales(
                $amenity,
                ['name'],
                AmenityTranslation::class,
                'amenity_id',
                $this->locales
            );
        });

        $this->command->info('💠 Amenidades traducidas');
    }

    protected function translateFaqs(): void
    {
        Faq::where('is_active', true)->each(function ($faq) {
            GoogleTranslationService::translateAndSaveForLocales(
                $faq,
                ['question', 'answer'],
                FaqTranslation::class,
                'faq_id',
                $this->locales
            );
        });

        $this->command->info('❓ FAQs traducidas');
    }
}
