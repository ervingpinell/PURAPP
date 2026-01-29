<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cleanup-expired
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired pending bookings without payment to release capacity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if cleanup is enabled
        if (!config('booking.cleanup_enabled', true)) {
            $this->info('Booking cleanup is disabled in configuration.');
            return 0;
        }

        $timeoutMinutes = config('booking.cleanup_after_minutes', 30);
        $cutoffTime = Carbon::now()->subMinutes($timeoutMinutes);

        $this->info("Looking for pending bookings created before: {$cutoffTime->format('Y-m-d H:i:s')}");

        // Find expired bookings
        $expiredBookings = Booking::where('status', 'pending')
            ->where('created_at', '<', $cutoffTime)
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })
            ->with(['user', 'product', 'detail'])
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');
            return 0;
        }

        $this->info("Found {$expiredBookings->count()} expired booking(s).");

        // Show details
        $this->table(
            ['ID', 'Reference', 'User', 'Product', 'Created', 'Age (min)'],
            $expiredBookings->map(function ($booking) {
                return [
                    $booking->booking_id,
                    $booking->booking_reference ?? 'N/A',
                    optional($booking->user)->email ?? 'N/A',
                    optional($booking->product)->name ?? 'N/A',
                    $booking->created_at->format('Y-m-d H:i'),
                    $booking->created_at->diffInMinutes(Carbon::now()),
                ];
            })
        );

        // Dry run check
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN: No bookings were actually deleted.');
            return 0;
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to cancel these bookings?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Cancel bookings
        $cancelled = 0;
        $errors = 0;

        foreach ($expiredBookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    // Update booking status
                    $booking->update([
                        'status' => 'cancelled',
                        'notes' => ($booking->notes ?? '') . "\n[Auto-cancelled: Payment timeout]",
                    ]);

                    if (config('app.debug')) {
                        Log::info('Booking auto-cancelled due to payment timeout', [
                            'booking_id' => $booking->booking_id,
                            'reference' => $booking->booking_reference,
                            'user_id' => $booking->user_id,
                            'created_at' => $booking->created_at,
                            'age_minutes' => $booking->created_at->diffInMinutes(Carbon::now()),
                        ]);
                    }
                });

                $cancelled++;
                $this->line("Cancelled booking #{$booking->booking_id}");
            } catch (\Exception $e) {
                $errors++;
                $this->error("Failed to cancel booking #{$booking->booking_id}: {$e->getMessage()}");
                Log::error('Failed to cancel expired booking', [
                    'booking_id' => $booking->booking_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Summary
        $this->newLine();
        $this->info("Cleanup complete:");
        $this->line("  Cancelled: {$cancelled}");
        if ($errors > 0) {
            $this->error("  Errors: {$errors}");
        }

        return $errors > 0 ? 1 : 0;
    }
}
