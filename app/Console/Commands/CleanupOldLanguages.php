<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TourLanguage;
use Carbon\Carbon;

class CleanupOldLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:old-languages {days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete languages trashed more than X days ago';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->argument('days');

        $this->info("Cleaning up languages deleted more than {$days} days ago...");

        // Usar el scopeOlderThan definido en el modelo
        $count = TourLanguage::olderThan($days)->count();

        if ($count > 0) {
            TourLanguage::olderThan($days)->forceDelete();
            $this->info("Permanently deleted {$count} language(s).");
        } else {
            $this->info("No old trashed languages found.");
        }

        return 0;
    }
}
