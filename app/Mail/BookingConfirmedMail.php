<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Concerns\BookingMailHelpers;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels, BookingMailHelpers;

    public Booking $booking;

    protected string $mailLocale;
    protected string $reference;
    protected string $tourLangLabel;
    protected string $statusText;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $this->booking->loadMissing([
            'detail.hotel', 'tour', 'user',
            'tourLanguage', 'detail.tourLanguage',
        ]);

        $this->mailLocale   = $this->mailLocaleFromBooking($this->booking);
        $this->reference    = $this->bookingReference($this->booking);
        $this->tourLangLabel= $this->humanTourLanguage($this->mailLocale, $this->booking);
        $this->statusText   = $this->statusLabel($this->mailLocale, $this->booking);

        $subject = __('adminlte::email.booking_confirmed_subject', [
            'reference' => $this->reference,
        ], $this->mailLocale);

        return $this->locale($this->mailLocale)
            ->subject($subject)
            ->view('emails.booking_confirmed')
            ->with([
                'booking'        => $this->booking,
                'mailLocale'     => $this->mailLocale,
                'reference'      => $this->reference,
                'tourLangLabel'  => $this->tourLangLabel,
                'statusLabel'    => $this->statusText,
                'company'        => config('mail.from.name', config('app.name', 'Green Vacations CR')),
                'contactEmail'   => 'info@greenvacations.com',
            ]);
    }
}
