<?php

namespace App\Console\Commands;

use App\Models\MeetingPoint;
use Illuminate\Console\Command;

class CleanupOldMeetingPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetingpoints:cleanup {--days=30 : Number of days before permanent deletion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete meeting points that have been in trash for more than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Looking for meeting points deleted more than {$days} days ago...");

        $oldPoints = MeetingPoint::olderThan($days)->get();

        if ($oldPoints->isEmpty()) {
            $this->info('No meeting points found for cleanup.');
            return Command::SUCCESS;
        }

        $count = $oldPoints->count();
        $this->warn("Found {$count} meeting point(s) to permanently delete:");

        foreach ($oldPoints as $point) {
            $this->line("  - ID: {$point->id}, Deleted: {$point->deleted_at->diffForHumans()}");
        }

        if ($this->confirm('Do you want to proceed with permanent deletion?', true)) {
            foreach ($oldPoints as $point) {
                $point->forceDelete();
            }

            $this->info("Successfully deleted {$count} meeting point(s) permanently.");
            return Command::SUCCESS;
        }

        $this->info('Cleanup cancelled.');
        return Command::SUCCESS;
    }
}
