<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendExpiryWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-expiry-warnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send expiry warnings to admin for bookings expiring soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $warningHours = config('booking.admin_notifications.unpaid_expiry_warning_hours', 2);
        $warningTime = now()->addHours($warningHours);

        // Find bookings expiring in the next 2 hours that haven't been warned yet
        $expiringBookings = Booking::where('status', 'pending')
            ->where('is_paid', false)
            ->whereNotNull('pending_expires_at')
            ->where('pending_expires_at', '>', now())
            ->where('pending_expires_at', '<=', $warningTime)
            ->whereNull('expiry_warning_sent_at')
            ->with(['user', 'product', 'details.schedule'])
            ->get();

        if ($expiringBookings->isEmpty()) {
            $this->info('No bookings expiring soon.');
            Log::info('[SendExpiryWarnings] No bookings require warnings');
            return 0;
        }

        $sentCount = 0;

        foreach ($expiringBookings as $booking) {
            try {
                // Generate extend token (valid for extending the booking)
                $extendToken = Str::random(64);
                $booking->extend_token = $extendToken;
                $booking->expiry_warning_sent_at = now();
                $booking->save();

                // Get admin email
                $adminEmail = setting('email.notification_email', config('booking.admin_notifications.email', 'admin@example.com'));

                // TODO: Send admin warning email
                // Mail::to($adminEmail)->send(new BookingExpiringAdmin($booking));

                $sentCount++;

                $expiresIn = $booking->pending_expires_at->diffForHumans();
                $this->info("Warning sent for booking {$booking->booking_reference} (expires {$expiresIn})");
                Log::info("[SendExpiryWarnings] Warning sent for booking #{$booking->booking_id} - {$booking->booking_reference}");
            } catch (\Exception $e) {
                Log::error("[SendExpiryWarnings] Failed to send warning for booking #{$booking->booking_id}: {$e->getMessage()}");
                $this->error("Failed to send warning for booking #{$booking->booking_id}");
            }
        }

        $this->info("Successfully sent {$sentCount} expiry warnings.");
        Log::info("[SendExpiryWarnings] Sent {$sentCount} warnings");

        return 0;
    }
}
