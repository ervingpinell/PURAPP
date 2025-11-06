<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PurgeOldAvailabilityOverrides;

class OverridesPurgeCommand extends Command
{
    protected $signature = 'overrides:purge
        {--daysAgo=0}
        {--onlyInactive=0}
        {--keepBlocked=1}
        {--limit=5000}
        {--chunk=1000}
        {--dryRun=1}
        {--queue= : Si se pasa, se encola en esa cola; si no, corre sync}';

    protected $description = 'Purge past TourAvailability overrides (< today - daysAgo).';

    public function handle()
    {
        $opts = [
            'daysAgo'      => (int) $this->option('daysAgo'),
            'onlyInactive' => (bool) $this->option('onlyInactive'),
            'keepBlocked'  => (bool) $this->option('keepBlocked'),
            'limit'        => (int) $this->option('limit'),
            'chunk'        => (int) $this->option('chunk'),
            'dryRun'       => (bool) $this->option('dryRun'),
        ];

        $queue = $this->option('queue');

        if ($queue) {
            PurgeOldAvailabilityOverrides::dispatch($opts)->onQueue($queue);
            $this->info('Job encolado en "'.$queue.'".');
            return Command::SUCCESS;
        }

        $res = (new PurgeOldAvailabilityOverrides($opts))->handle();
        $this->table(['ok','dryRun','cutoff','scanned','deleted','kept'], [[$res['ok']?'1':'0', $res['dryRun']?'1':'0', $res['cutoff'], $res['scanned'], $res['deleted'], $res['kept']]]);
        return Command::SUCCESS;
    }
}
