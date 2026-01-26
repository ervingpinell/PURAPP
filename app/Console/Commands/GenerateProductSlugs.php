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
    protected $description = 'Genera slugs únicos para todos los tours que no tienen slug';

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

        $tours = $query->get();

        if ($tours->isEmpty()) {
            $this->info('No tours to process.');
            return Command::SUCCESS;
        }

        $this->info("Procesando {$tours->count()} tours...");
        $bar = $this->output->createProgressBar($tours->count());
        $bar->start();

        $updated = 0;

        foreach ($tours as $tour) {
            try {
                $tour->slug = Product::generateUniqueSlug($tour->name, $tour->product_id);
                $tour->save();
                $updated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error en tour #{$tour->product_id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("{$updated} slugs generated successfully");

        return Command::SUCCESS;
    }
}
