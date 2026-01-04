<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Reminder - Your Tour is Coming Soon! #{$this->booking->booking_reference}",
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customer.payment-reminder',
            with: [
                'booking' => $this->booking,
                'paymentUrl' => route('booking.payment', $this->booking->booking_reference),
                'daysUntilCharge' => $this->booking->auto_charge_at?->diffInDays(now()) ?? 0,
            ],
        );
    }
}
