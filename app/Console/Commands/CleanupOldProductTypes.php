<?php

namespace App\Console\Commands;

use App\Models\ProductType;
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
    protected $description = 'Permanently delete product types older than X days from trash';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Looking for product types deleted more than {$days} days ago...");

        $oldItems = ProductType::onlyTrashed()
            ->olderThan($days)
            ->get();

        if ($oldItems->isEmpty()) {
            $this->info('No product types found for cleanup.');
            return 0;
        }

        $this->info("Found {$oldItems->count()} item(s) to permanently delete.");

        foreach ($oldItems as $item) {
            $name = $item->name ?? $item->product_type_id;
            try {
                // Verificar si tiene products asociados antes de forzar borrado
                if ($item->products()->exists()) {
                    $this->warn("! Skipped {$name}: Has associated products.");
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
