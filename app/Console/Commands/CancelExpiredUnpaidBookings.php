<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredUnpaidBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel expired unpaid pending bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // Find expired unpaid bookings
        $expiredBookings = Booking::where('status', 'pending')
            ->where('is_paid', false)
            ->whereNotNull('pending_expires_at')
            ->where('pending_expires_at', '<', $now)
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired unpaid bookings found.');
            Log::info('[CancelExpiredUnpaidBookings] No expired bookings to cancel');
            return 0;
        }

        $cancelledCount = 0;

        foreach ($expiredBookings as $booking) {
            try {
                // Update status to cancelled
                $booking->status = 'cancelled';

                // Append note about auto-cancellation
                $note = "\n\n[AUTO-CANCELLED] Booking expired on {$booking->pending_expires_at->format('Y-m-d H:i:s')} without payment.";
                $booking->notes = ($booking->notes ?? '') . $note;

                $booking->save();

                $cancelledCount++;

                Log::info("[CancelExpiredUnpaidBookings] Cancelled booking #{$booking->booking_id} - {$booking->booking_reference}");

                // TODO: Send customer notification email
                // Mail::to($booking->user->email)->send(new BookingCancelledExpiry($booking));

            } catch (\Exception $e) {
                Log::error("[CancelExpiredUnpaidBookings] Failed to cancel booking #{$booking->booking_id}: {$e->getMessage()}");
                $this->error("Failed to cancel booking #{$booking->booking_id}");
            }
        }

        $this->info("Successfully cancelled {$cancelledCount} expired unpaid bookings.");
        Log::info("[CancelExpiredUnpaidBookings] Cancelled {$cancelledCount} bookings");

        return 0;
    }
}
