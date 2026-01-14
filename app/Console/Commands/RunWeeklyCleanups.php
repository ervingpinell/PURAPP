<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CartItem;

class RunWeeklyCleanups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weekly-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run weekly maintenance tasks (orphan items)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting weekly system cleanup...');

        // 1. Items huérfanos de carritos
        try {
            $this->info('Pruning orphan cart items...');
            $deleted = CartItem::query()
                ->whereDoesntHave('cart')
                ->delete();

            $this->info("✓ Deleted {$deleted} orphan cart items.");
        } catch (\Exception $e) {
            $this->error('x Error pruning orphan items: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✅ Weekly cleanups completed successfully.');

        return 0;
    }
}
