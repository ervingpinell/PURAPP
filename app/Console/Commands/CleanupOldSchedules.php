<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Services\LoggerHelper;

class CleanupOldSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:schedules {--days=30 : Number of days to keep trashed schedules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted schedules older than X days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Cleaning up schedules deleted more than {$days} days ago...");

        try {
            // Utilizar el scope del modelo
            $query = Schedule::olderThan($days);
            $count = $query->count();

            if ($count > 0) {
                // Force delete
                $query->forceDelete();

                $message = "Permanently deleted {$count} old schedules.";
                $this->info($message);

                LoggerHelper::info('System', 'Cleanup', 'schedules', null, [
                    'count' => $count,
                    'days_threshold' => $days
                ]);
            } else {
                $this->info("No old schedules found to delete.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error cleaning up schedules: " . $e->getMessage());

            LoggerHelper::exception('System', 'Cleanup', 'schedules', null, $e, [
                'days_threshold' => $days
            ]);

            return 1;
        }
    }
}
