<?php

namespace App\Console\Commands;

use App\Models\CustomerCategory;
use Illuminate\Console\Command;

class CleanupOldCustomerCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer_categories:cleanup {--days=30 : Number of days after which to permanently delete items}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete customer categories older than X days from trash';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Looking for customer categories deleted more than {$days} days ago...");

        $oldItems = CustomerCategory::onlyTrashed()
            ->olderThan($days)
            ->get();

        if ($oldItems->isEmpty()) {
            $this->info('No customer categories found for cleanup.');
            return 0;
        }

        $this->info("Found {$oldItems->count()} item(s) to permanently delete.");

        foreach ($oldItems as $item) {
            $name = $item->slug ?? $item->category_id;
            try {
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
