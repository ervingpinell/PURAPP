<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixLegacyProductPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-legacy-prices {--force : Force execution without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates legacy prices (null dates) to start from today with "Estandar" label';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = DB::table('tour_prices')
            ->whereNull('valid_from')
            ->whereNull('valid_until');

        $count = $query->count();

        if ($count === 0) {
            $this->info('No legacy prices found to update.');
            return 0;
        }

        $this->info("Found {$count} legacy prices with no dates.");

        if (!$this->option('force') && !$this->confirm('Do you want to update these prices to start from TODAY with label "Estandar"?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $today = Carbon::today()->format('Y-m-d');

        $updated = $query->update([
            'valid_from' => $today,
            'label'      => 'Estandar',
            'updated_at' => now(),
        ]);

        $this->info("Successfully updated {$updated} prices.");

        return 0;
    }
}
