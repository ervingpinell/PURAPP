<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunDailyCleanups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all daily cleanup tasks (carts, overrides, soft deletes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting daily system cleanup...');

        // 1. Purgar carritos inactivos antiguos (>7 días)
        try {
            $this->info('Pruning old inactive carts (>7 days)...');
            $cutoff = now()->subDays(7);

            $count = 0;
            \App\Models\Cart::query()
                ->where('is_active', false)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', $cutoff)
                ->orderBy('cart_id')
                ->chunkById(500, function ($carts) use (&$count) {
                    foreach ($carts as $cart) {
                        $cart->items()->delete();
                        $cart->delete();
                        $count++;
                    }
                });
            $this->info("✓ Pruned {$count} old carts.");
        } catch (\Exception $e) {
            $this->error('x Error pruning carts: ' . $e->getMessage());
        }

        // 2. Purga de overrides de capacidad (anteriores a HOY)
        try {
            $this->newLine();
            $this->info('Dispatching availability overrides purge...');
            \App\Jobs\PurgeOldAvailabilityOverrides::dispatch([
                'daysAgo'      => 0,
                'onlyInactive' => false,
                'keepBlocked'  => true,
                'limit'        => 20000,
                'chunk'        => 1000,
                'dryRun'       => false,
            ])->onQueue('maintenance');
            $this->info('✓ Overrides purge job dispatched.');
        } catch (\Exception $e) {
            $this->error('x Error dispatching overrides purge: ' . $e->getMessage());
        }

        // 3. Meeting Points
        try {
            $this->newLine();
            $this->info('Cleaning up meeting points...');
            $this->call('meetingpoints:cleanup', ['--days' => 30]);
            $this->info('✓ Meeting Points cleaned.');
        } catch (\Exception $e) {
            $this->error('x Error cleaning meeting points: ' . $e->getMessage());
        }

        // 4. Tours
        try {
            $this->newLine();
            $this->info('Cleaning up tours...');
            $this->call('tours:cleanup', ['--days' => 30]);
            $this->info('✓ Tours cleaned.');
        } catch (\Exception $e) {
            $this->error('x Error cleaning tours: ' . $e->getMessage());
        }

        // 5. Customer Categories
        try {
            $this->newLine();
            $this->info('Cleaning up customer categories...');
            $this->call('customer_categories:cleanup', ['--days' => 30]);
            $this->info('✓ Customer Categories cleaned.');
        } catch (\Exception $e) {
            $this->error('x Error cleaning customer categories: ' . $e->getMessage());
        }

        // 6. Tour Types
        try {
            $this->newLine();
            $this->info('Cleaning up tour types...');
            $this->call('tourtypes:cleanup', ['--days' => 30]);
            $this->info('✓ Tour Types cleaned.');
        } catch (\Exception $e) {
            $this->error('x Error cleaning tour types: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✅ All daily cleanups completed successfully.');

        return 0;
    }
}
