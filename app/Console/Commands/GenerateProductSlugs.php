<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class GenerateProductSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-slugs {--force : Regenerar slugs existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera slugs únicos para todos los productos que no tienen slug';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $query = Product::query();

        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('slug')
                    ->orWhere('slug', '');
            });
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            $this->info('No products to process.');
            return Command::SUCCESS;
        }

        $this->info("Procesando {$products->count()} productos...");
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $updated = 0;

        foreach ($products as $product) {
            try {
                $product->slug = Product::generateUniqueSlug($product->name, $product->product_id);
                $product->save();
                $updated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error en producto #{$product->product_id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("{$updated} slugs generated successfully");

        return Command::SUCCESS;
    }
}
