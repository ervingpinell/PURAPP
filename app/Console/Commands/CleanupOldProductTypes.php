<?php

namespace App\Console\Commands;

use App\Models\TourType;
use Illuminate\Console\Command;

class CleanupOldProductTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product-types:cleanup {--days=30 : Number of days after which to permanently delete items}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete tour types older than X days from trash';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Looking for tour types deleted more than {$days} days ago...");

        $oldItems = TourType::onlyTrashed()
            ->olderThan($days)
            ->get();

        if ($oldItems->isEmpty()) {
            $this->info('No tour types found for cleanup.');
            return 0;
        }

        $this->info("Found {$oldItems->count()} item(s) to permanently delete.");

        foreach ($oldItems as $item) {
            $name = $item->name ?? $item->tour_type_id;
            try {
                // Verificar si tiene tours asociados antes de forzar borrado
                if ($item->tours()->exists()) {
                    $this->warn("! Skipped {$name}: Has associated tours.");
                    continue;
                }

                $item->forceDelete();
                $this->line("✓ Permanently deleted: {$name}");
            } catch (\Exception $e) {
                $this->error("x Failed to delete {$name}: {$e->getMessage()}");
            }
        }

        $this->info("✅ Cleanup completed.");
        return 0;
    }
}
