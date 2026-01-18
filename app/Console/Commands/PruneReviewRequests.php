<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneReviewRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:prune-requests {--days=30 : Number of days to keep deleted requests}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted review requests older than X days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $date = now()->subDays($days);

        $count = \App\Models\ReviewRequest::onlyTrashed()
            ->where('deleted_at', '<', $date)
            ->forceDelete();

        $this->info("Pruned {$count} review requests deleted before {$date->toDateTimeString()}.");
    }
}
