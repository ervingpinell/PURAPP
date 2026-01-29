<?php

// app/Console/Commands/CleanOldTourLogs.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuditLog;
use Illuminate\Support\Carbon;

class CleanOldProductLogs extends Command
{
    protected $signature = 'products:audit:cleanup {--days=365}';
    protected $description = 'Elimina logs de tour_audit_logs más antiguos que N días.';

    public function handle()
    {
        $days   = (int) $this->option('days');
        $cutoff = Carbon::now()->subDays($days);

        $deleted = ProductAuditLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Eliminados {$deleted} registros de tour_audit_logs anteriores a {$cutoff}.");
    }
}
