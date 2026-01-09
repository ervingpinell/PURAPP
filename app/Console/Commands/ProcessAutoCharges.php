<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessAutoCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:process-auto-charges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders for unpaid pay-later bookings (NO auto-charge)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->sendPaymentReminders();
        return 0;
    }

    /**
     * Send payment reminders X days before tour date.
     * No auto-charge - user must pay manually via payment link.
     *
     * @return void
     */
    private function sendPaymentReminders()
    {
        $reminderDays = (int) setting('booking.pay_later.reminder_days_before', 3);

        // Calculate the tour date that is X days from now
        $targetTourDate = now()->addDays($reminderDays)->format('Y-m-d');

        // Find unpaid pay-later bookings with tour on target date
        $bookingsNeedingReminder = Booking::where('status', 'pending')
            ->where('is_paid', false)
            ->where('is_pay_later', true)
            ->whereHas('details', function ($q) use ($targetTourDate) {
                $q->whereDate('tour_date', $targetTourDate);
            })
            ->whereNull('payment_reminder_sent_at')
            ->with(['user', 'tour', 'details'])
            ->get();

        if ($bookingsNeedingReminder->isEmpty()) {
            $this->info('No payment reminders to send.');
            if (config('app.debug')) {
                Log::info('[PaymentReminders] No bookings need reminders today');
            }
            return;
        }

        $sentCount = 0;

        foreach ($bookingsNeedingReminder as $booking) {
            try {
                // Mark reminder as sent
                $booking->payment_reminder_sent_at = now();
                $booking->save();

                // Send payment reminder email
                try {
                    Mail::to($booking->user->email)
                        ->send(new \App\Mail\PaymentReminderMail($booking));
                } catch (\Exception $e) {
                    Log::warning("[PaymentReminders] Email failed for booking #{$booking->booking_id}: {$e->getMessage()}");
                }

                $sentCount++;
                $this->info("Reminder sent for booking {$booking->booking_reference}");

                if (config('app.debug')) {
                    Log::info("[PaymentReminders] Sent reminder", [
                        'booking_id' => $booking->booking_id,
                        'reference' => $booking->booking_reference,
                        'tour_date' => $booking->details->first()->tour_date ?? 'N/A',
                        'days_until_tour' => $reminderDays
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("[PaymentReminders] Failed for booking #{$booking->booking_id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$sentCount} payment reminders.");
        if (config('app.debug')) {
            Log::info("[PaymentReminders] Completed: {$sentCount} reminders sent");
        }
    }
}
