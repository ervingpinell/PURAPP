<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load(['detail.hotel', 'tour', 'user']);
    }

    public function build()
    {
        return $this->subject('¡Tu reserva ha sido confirmada!')
            ->view('emails.booking_confirmed');
    }
}
