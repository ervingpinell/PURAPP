<?php

namespace App\Console\Commands;

use App\Models\Product;
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

        // 3. Agregar URLs de categorías en todos los idiomas
        $categories = \App\Helpers\ProductCategoryHelper::getAllCategories();
        
        foreach ($categories as $categoryKey => $config) {
            $urlPrefix = $config['url_prefix'];
            
            foreach ($locales as $locale) {
                $localeSlug = $locale === 'pt_BR' ? 'pt' : $locale;
                
                // Category listing URL
                $url = $locale === 'es'
                    ? url("/{$urlPrefix}")
                    : url("/{$localeSlug}/{$urlPrefix}");
                
                $sitemap->add(
                    Url::create($url)
                        ->setLastModificationDate(now())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                        ->setPriority(0.9)
                );
                
                // Subcategory URLs
                foreach ($config['subcategories'] ?? [] as $subKey => $subConfig) {
                    $subUrl = $locale === 'es'
                        ? url("/{$urlPrefix}/{$subKey}")
                        : url("/{$localeSlug}/{$urlPrefix}/{$subKey}");
                    
                    $sitemap->add(
                        Url::create($subUrl)
                            ->setLastModificationDate(now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );
                }
            }
        }

        // 4. Agregar todos los productos activos en todos los idiomas
        $products = Product::where('is_active', true)->get();
        $this->info("Found {$products->count()} active products");

        foreach ($products as $product) {
            // Determinar URL prefix por categoría (por ahora todos usan 'products')
            $urlPrefix = 'products'; // TODO: mapear product_type_id a category
            
            foreach ($locales as $locale) {
                try {
                    $localeSlug = $locale === 'pt_BR' ? 'pt' : $locale;
                    $productSlug = $product->slug ?? $product->id;
                    
                    $url = $locale === 'es'
                        ? url("/{$urlPrefix}/{$productSlug}")
                        : url("/{$localeSlug}/{$urlPrefix}/{$productSlug}");

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate($product->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.9)
                    );
                } catch (\Exception $e) {
                    $this->warn("Could not add product {$product->id} for locale {$locale}: {$e->getMessage()}");
                }
            }
        }

        // 5. Guardar sitemap
        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap generated successfully at: {$path}");

        return Command::SUCCESS;
    }
}
