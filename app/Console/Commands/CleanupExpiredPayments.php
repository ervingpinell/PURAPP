<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired pending/processing payments as failed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up expired payment intents...');

        $expiredCount = Payment::whereIn('status', ['pending', 'processing'])
            ->where('expires_at', '<', now())
            ->count();

        if ($expiredCount === 0) {
            $this->info('No expired payments found.');
            return 0;
        }

        // Update expired payments to failed status
        Payment::whereIn('status', ['pending', 'processing'])
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'failed',
                'metadata' => \DB::raw("jsonb_set(COALESCE(metadata, '{}'::jsonb), '{expired}', 'true'::jsonb)")
            ]);

        $this->info("Marked {$expiredCount} expired payment(s) as failed.");

        Log::info('Cleanup expired payments completed', [
            'expired_count' => $expiredCount,
        ]);

        return 0;
    }
}
