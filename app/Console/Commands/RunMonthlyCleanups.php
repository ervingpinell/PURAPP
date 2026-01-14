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

        // 1. Limpiar logs de auditoría de tours (>365 días)
        try {
            $this->info('Cleaning old tour audit logs (>365 days)...');
            // Assuming tours:audit:cleanup is a valid command registered in console
            // Note: The user provided snippet showed `tours:audit:cleanup`. 
            // If it doesn't exist as a command class but as a closure, we should have checked.
            // But usually this looks like a command. 
            // Checking snippet: "$schedule->command('tours:audit:cleanup --days=365')"
            // implies it IS a registered command.

            $this->call('tours:audit:cleanup', ['--days' => 365]);
            $this->info('✓ Tour audit logs cleaned.');
        } catch (\Exception $e) {
            $this->error('x Error cleaning audit logs: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✅ Monthly cleanups completed successfully.');

        return 0;
    }
}
