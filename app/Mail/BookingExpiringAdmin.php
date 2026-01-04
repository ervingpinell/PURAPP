<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingExpiringAdmin extends Mailable
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
        $expiresIn = $this->booking->pending_expires_at?->diffForHumans() ?? 'soon';

        return new Envelope(
            subject: "⚠️ Booking Expiring {$expiresIn} - {$this->booking->booking_reference}",
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.booking-expiring',
            with: [
                'booking' => $this->booking,
                'extendUrl' => route('admin.bookings.extend', [
                    'booking' => $this->booking->booking_id,
                    'token' => $this->booking->extend_token
                ]),
            ],
        );
    }
}
