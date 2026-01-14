<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Amenity;

class CleanupOldAmenities extends Command
{
    protected $signature = 'amenities:cleanup {--days=30 : Number of days to keep deleted items}';
    protected $description = 'Permanently delete amenities soft-deleted more than X days ago.';

    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Looking for amenities deleted more than {$days} days ago...");

        $oldItems = Amenity::onlyTrashed()
            ->olderThan($days)
            ->get();

        if ($oldItems->isEmpty()) {
            $this->info('No amenities found for cleanup.');
            return 0;
        }

        $this->info("Found {$oldItems->count()} item(s) to permanently delete.");

        foreach ($oldItems as $item) {
            $name = $item->name ?? $item->amenity_id;
            try {
                // Verificar si tiene tours asociados antes de forzar borrado
                if ($item->tours()->exists()) {
                    $this->warn("! Skipped {$name}: Has associated tours.");
                    continue;
                }

                $item->forceDelete();
                $this->line("✓ Permanently deleted: {$name}");
            } catch (\Exception $e) {
                $this->error("x Failed to delete {$name}: {$e->getMessage()}");
            }
        }

        $this->info("✅ Cleanup completed.");
        return 0;
    }
}
