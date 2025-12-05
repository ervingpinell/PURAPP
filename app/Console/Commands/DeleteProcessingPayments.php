<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteProcessingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:delete-processing {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all payments with "processing" status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $processingCount = Payment::where('status', 'processing')->count();

        if ($processingCount === 0) {
            $this->info('No processing payments found.');
            return 0;
        }

        $this->warn("Found {$processingCount} payment(s) with 'processing' status.");

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete these payments? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 1;
            }
        }

        // Delete processing payments
        $deleted = Payment::where('status', 'processing')->delete();

        $this->info("Successfully deleted {$deleted} processing payment(s).");

        Log::warning('Deleted processing payments via command', [
            'deleted_count' => $deleted,
            'executed_by' => 'console',
        ]);

        return 0;
    }
}
