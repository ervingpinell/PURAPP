<?php

namespace App\Console\Commands;

use App\Models\Itinerary;
use Illuminate\Console\Command;

class CleanupOldItineraries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'itineraries:cleanup {--days=30 : Number of days after which to permanently delete itineraries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete itineraries that have been in trash for more than X days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Looking for itineraries deleted more than {$days} days ago...");

        $oldItineraries = Itinerary::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days))
            ->get();

        if ($oldItineraries->isEmpty()) {
            $this->info('No itineraries found for cleanup.');
            return 0;
        }

        $this->info("Found {$oldItineraries->count()} itinerary(s) to permanently delete.");

        foreach ($oldItineraries as $itinerary) {
            $name = $itinerary->name ?? $itinerary->itinerary_id;

            // Detach items first (if cascade is not set or to be safe)
            $itinerary->items()->detach();
            $itinerary->translations()->delete();

            $itinerary->forceDelete();
            $this->line("✓ Permanently deleted: {$name}");
        }

        $this->info("✅ Cleanup completed. {$oldItineraries->count()} itinerary(s) permanently deleted.");

        return 0;
    }
}
