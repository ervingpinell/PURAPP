<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CleanupOldProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:cleanup {--days=30 : Number of days after which to permanently delete products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete products that have been in trash for more than X days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Looking for products deleted more than {$days} days ago...");

        $oldProducts = Product::onlyTrashed()
            ->olderThan($days)
            ->get();

        if ($oldProducts->isEmpty()) {
            $this->info('No products found for cleanup.');
            return 0;
        }

        $this->info("Found {$oldProducts->count()} product(s) to permanently delete.");

        foreach ($oldProducts as $product) {
            $productName = $product->name ?? $product->product_id;
            $product->forceDelete();
            $this->line("✓ Permanently deleted: {$productName}");
        }

        $this->info("✅ Cleanup completed. {$oldProducts->count()} product(s) permanently deleted.");

        return 0;
    }
}
