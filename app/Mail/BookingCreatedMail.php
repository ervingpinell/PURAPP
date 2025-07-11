<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking; // Pasas la reserva completa
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Confirmación de Reserva - Green Vacations')
                    ->view('emails.booking_created');
    }
}
