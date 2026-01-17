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
        $locale = $this->booking->tour->lang ?? config('app.locale');
        $subject = __('reviews.emails.booking.payment_reminder_subject', ['ref' => $this->booking->booking_reference], $locale);

        return new Envelope(
            subject: $subject,
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer.payment-reminder',
            text: 'emails.customer.payment-reminder_plain',
            with: [
                'booking' => $this->booking,
                'paymentUrl' => route('booking.payment', $this->booking->booking_reference),
                'daysUntilCharge' => $this->booking->auto_charge_at?->diffInDays(now()) ?? 0,
            ],
        );
    }

    public function headers(): \Illuminate\Mail\Mailables\Headers
    {
        return new \Illuminate\Mail\Mailables\Headers(
            text: [
                'X-Entity-Ref-ID' => $this->booking->booking_reference,
            ],
        );
    }
}
