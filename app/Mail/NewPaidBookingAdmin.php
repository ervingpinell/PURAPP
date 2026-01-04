<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPaidBookingAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ New Paid Booking - {$this->booking->booking_reference}",
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.new-paid-booking',
            with: ['booking' => $this->booking],
        );
    }
}
