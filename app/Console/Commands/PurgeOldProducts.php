<?php 
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class PurgeOldProducts extends Command
{
    protected $signature = 'products:purge-old';
    protected $description = 'Hard-delete products archived >=90d ago with no bookings';

    public function handle(): int
    {
        $count = Product::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(90))
            ->whereDoesntHave('bookings')
            ->forceDelete();

        $this->info("Purged {$count} products.");
        return self::SUCCESS;
    }
}
