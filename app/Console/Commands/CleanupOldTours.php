<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;

class CleanupOldTours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:cleanup {--days=30 : Number of days after which to permanently delete tours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete tours that have been in trash for more than X days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Looking for tours deleted more than {$days} days ago...");

        $oldTours = Tour::onlyTrashed()
            ->olderThan($days)
            ->get();

        if ($oldTours->isEmpty()) {
            $this->info('No tours found for cleanup.');
            return 0;
        }

        $this->info("Found {$oldTours->count()} tour(s) to permanently delete.");

        foreach ($oldTours as $tour) {
            $tourName = $tour->name ?? $tour->tour_id;
            $tour->forceDelete();
            $this->line("✓ Permanently deleted: {$tourName}");
        }

        $this->info("✅ Cleanup completed. {$oldTours->count()} tour(s) permanently deleted.");

        return 0;
    }
}
