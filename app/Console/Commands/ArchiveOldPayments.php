<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ArchiveOldPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:archive-old 
                            {--days=200 : Number of days to keep payments before archiving}
                            {--status=failed : Payment status to archive (failed, pending, processing)}
                            {--force : Skip confirmation}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive old payments by deleting them after logging for audit trail';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $status = $this->option('status');
        $isDryRun = $this->option('dry-run');

        $cutoffDate = now()->subDays($days);

        $this->info("Searching for '{$status}' payments older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");

        // Find old payments
        $query = Payment::where('status', $status)
            ->where('created_at', '<', $cutoffDate);

        $count = $query->count();

        if ($count === 0) {
            $this->info('No old payments found to archive.');
            return 0;
        }

        $this->warn("Found {$count} payment(s) to archive.");

        // Show sample of what will be deleted
        $sample = $query->limit(5)->get(['payment_id', 'booking_id', 'amount', 'gateway', 'created_at']);

        $this->table(
            ['Payment ID', 'Booking ID', 'Amount', 'Gateway', 'Created At'],
            $sample->map(fn($p) => [
                $p->payment_id,
                $p->booking_id ?? 'N/A',
                $p->amount . ' ' . $p->currency,
                $p->gateway,
                $p->created_at->format('Y-m-d H:i:s'),
            ])
        );

        if ($count > 5) {
            $this->line("... and " . ($count - 5) . " more.");
        }

        if ($isDryRun) {
            $this->info('DRY RUN: No payments were deleted.');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to archive (delete) these payments? This will be logged for audit.')) {
                $this->info('Operation cancelled.');
                return 1;
            }
        }

        // Get full details for audit log before deletion
        $paymentsToArchive = $query->get();

        // Create audit log entry
        $auditData = [
            'action' => 'archive_old_payments',
            'executed_at' => now()->toIso8601String(),
            'executed_by' => 'console_command',
            'criteria' => [
                'status' => $status,
                'days_old' => $days,
                'cutoff_date' => $cutoffDate->toIso8601String(),
            ],
            'count' => $count,
            'payments' => $paymentsToArchive->map(function ($payment) {
                return [
                    'payment_id' => $payment->payment_id,
                    'booking_id' => $payment->booking_id,
                    'user_id' => $payment->user_id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'gateway' => $payment->gateway,
                    'status' => $payment->status,
                    'created_at' => $payment->created_at->toIso8601String(),
                    'gateway_payment_intent_id' => $payment->gateway_payment_intent_id,
                ];
            })->toArray(),
        ];

        // Log to Laravel log for permanent audit trail
        Log::warning('Archiving old payments', $auditData);

        // Also save to a dedicated audit log file
        $auditLogPath = storage_path('logs/payment_archive_audit.log');
        file_put_contents(
            $auditLogPath,
            json_encode($auditData, JSON_PRETTY_PRINT) . "\n\n",
            FILE_APPEND
        );

        // Delete the payments
        $deleted = Payment::where('status', $status)
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Successfully archived {$deleted} payment(s).");
        $this->line("Audit log saved to: {$auditLogPath}");
        $this->line("Also logged to: storage/logs/laravel.log");

        return 0;
    }
}
