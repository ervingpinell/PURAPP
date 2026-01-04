<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CancelUnpaidBeforeTour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-unpaid-before-tour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel unpaid pay-later bookings X hours before tour date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoursBeforeTour = (int) setting('booking.pay_later.cancel_hours_before_tour', 24);

        // Calculate the cutoff time (tour date - X hours)
        $cutoffTime = now()->addHours($hoursBeforeTour);

        // Find unpaid pay-later bookings with tour date approaching
        $bookingsToCancel = Booking::where('status', 'pending')
            ->where('is_paid', false)
            ->where('is_pay_later', true)
            ->whereHas('details', function ($q) use ($cutoffTime) {
                // Tour date/time is within the cancellation window
                $q->where('tour_date', '<=', $cutoffTime->format('Y-m-d'));
            })
            ->with(['user', 'tour', 'details'])
            ->get();

        if ($bookingsToCancel->isEmpty()) {
            $this->info('No unpaid bookings to cancel.');
            Log::info('[CancelBeforeTour] No bookings to cancel');
            return 0;
        }

        $cancelledCount = 0;

        foreach ($bookingsToCancel as $booking) {
            try {
                $tourDate = $booking->details->first()?->tour_date;

                // Double-check timing
                if (!$tourDate || now()->diffInHours($tourDate, false) > $hoursBeforeTour) {
                    continue; // Not yet time to cancel
                }

                // Cancel the booking
                $booking->status = 'cancelled';
                $note = "\n\n[AUTO-CANCELLED] Unpaid pay-later booking cancelled {$hoursBeforeTour}h before tour on " . now()->format('Y-m-d H:i:s');
                $booking->notes = ($booking->notes ?? '') . $note;
                $booking->save();

                // Send cancellation email
                try {
                    Mail::to($booking->user->email)
                        ->send(new \App\Mail\BookingCancelledExpiry($booking));
                } catch (\Exception $e) {
                    Log::warning("[CancelBeforeTour] Email failed for booking #{$booking->booking_id}: {$e->getMessage()}");
                }

                $cancelledCount++;
                $this->info("✓ Cancelled booking {$booking->booking_reference} (tour: {$tourDate})");

                Log::info("[CancelBeforeTour] Booking cancelled", [
                    'booking_id' => $booking->booking_id,
                    'reference' => $booking->booking_reference,
                    'tour_date' => $tourDate,
                    'hours_before' => $hoursBeforeTour
                ]);
            } catch (\Exception $e) {
                Log::error("[CancelBeforeTour] Failed to cancel booking #{$booking->booking_id}: {$e->getMessage()}");
            }
        }

        $this->info("✓ Cancelled {$cancelledCount} unpaid bookings.");
        Log::info("[CancelBeforeTour] Completed: {$cancelledCount} bookings cancelled");

        return 0;
    }
}
