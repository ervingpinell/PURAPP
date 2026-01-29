<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledExpiry extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        // Calculate locale based on booking->product connection if possible, fallback to app locale
        $locale = $this->booking->product->lang ?? config('app.locale');

        // Set the application locale for this email sending process if needed, 
        // though typically it's better to just translate the subject here.
        // We will assume 'reviews.php' is loaded or available.
        $subject = __('reviews.emails.booking.cancelled_subject', ['ref' => $this->booking->booking_reference], $locale);

        return new Envelope(
            subject: $subject,
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer.booking-cancelled-expiry',
            text: 'emails.customer.booking-cancelled-expiry_plain',
            with: ['booking' => $this->booking],
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
