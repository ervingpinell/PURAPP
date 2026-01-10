<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for SEO';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Idiomas soportados
        $locales = ['es', 'en', 'fr', 'de', 'pt_BR'];

        // 1. Agregar home en todos los idiomas
        foreach ($locales as $locale) {
            $url = $locale === 'es'
                ? url('/')
                : url('/' . ($locale === 'pt_BR' ? 'pt' : $locale));

            $sitemap->add(
                Url::create($url)
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(1.0)
            );
        }

        // 2. Agregar página de contacto en todos los idiomas
        foreach ($locales as $locale) {
            $localeSlug = $locale === 'pt_BR' ? 'pt' : $locale;
            $url = $locale === 'es'
                ? url('/contacto')
                : url("/{$localeSlug}/contact");

            $sitemap->add(
                Url::create($url)
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.8)
            );
        }

        // 3. Agregar todos los tours en todos los idiomas
        $tours = Tour::where('is_active', true)->get();
        $this->info("Found {$tours->count()} active tours");

        foreach ($tours as $tour) {
            foreach ($locales as $locale) {
                try {
                    // Usar el helper localized_route si existe
                    if (function_exists('localized_route')) {
                        $url = localized_route('tours.show', [$tour], $locale);
                    } else {
                        // Fallback manual
                        $localeSlug = $locale === 'pt_BR' ? 'pt' : $locale;
                        $tourSlug = $tour->slug ?? $tour->id;
                        $url = $locale === 'es'
                            ? url("/tours/{$tourSlug}")
                            : url("/{$localeSlug}/tours/{$tourSlug}");
                    }

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate($tour->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.9)
                    );
                } catch (\Exception $e) {
                    $this->warn("Could not add tour {$tour->id} for locale {$locale}: {$e->getMessage()}");
                }
            }
        }

        // 4. Guardar sitemap
        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap generated successfully at: {$path}");

        return Command::SUCCESS;
    }
}
