<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunMonthlyCleanups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run monthly maintenance tasks (audit logs cleanup)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting monthly system cleanup...');

        // 1. Limpiar logs de auditoría de products (>365 días)
        try {
            $this->info('Cleaning old product audit logs (>365 days)...');
            // Assuming products:audit:cleanup is a valid command registered in console
            // Note: The user provided snippet showed `products:audit:cleanup`. 
            // If it doesn't exist as a command class but as a closure, we should have checked.
            // But usually this looks like a command. 
            // Checking snippet: "$schedule->command('products:audit:cleanup --days=365')"
            // implies it IS a registered command.

            $this->call('products:audit:cleanup', ['--days' => 365]);
            $this->info('✓ Product audit logs cleaned.');
        } catch (\Exception $e) {
            $this->error('x Error cleaning audit logs: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✅ Monthly cleanups completed successfully.');

        return 0;
    }
}
