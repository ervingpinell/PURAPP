<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $locale = $this->booking->tour->lang ?? config('app.locale');
        $subject = __('reviews.emails.booking.payment_failed_subject', ['ref' => $this->booking->booking_reference], $locale);

        return new Envelope(
            subject: $subject,
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customer.payment-failed',
            text: 'emails.customer.payment-failed_plain',
            with: [
                'booking' => $this->booking,
                'paymentUrl' => route('booking.payment', $this->booking->booking_reference),
                'graceHours' => 24,
            ],
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Entity-Ref-ID' => $this->booking->booking_reference,
            ],
        );
    }
}
