<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;

class GenerateTourSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:generate-slugs {--force : Regenerar slugs existentes}';

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

        $query = Tour::query();

        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('slug')
                  ->orWhere('slug', '');
            });
        }

        $tours = $query->get();

        if ($tours->isEmpty()) {
            $this->info('✓ No hay tours para procesar.');
            return Command::SUCCESS;
        }

        $this->info("Procesando {$tours->count()} tours...");
        $bar = $this->output->createProgressBar($tours->count());
        $bar->start();

        $updated = 0;

        foreach ($tours as $tour) {
            try {
                $tour->slug = Tour::generateUniqueSlug($tour->name, $tour->tour_id);
                $tour->save();
                $updated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error en tour #{$tour->tour_id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ {$updated} slugs generados correctamente");

        return Command::SUCCESS;
    }
}
