<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Concerns\BookingMailHelpers;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingUpdatedMail extends Mailable implements ShouldQueue
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

        $subject = __('adminlte::email.booking_updated_subject', [
            'reference' => $this->reference,
        ], $this->mailLocale);

        $replyTo = config('mail.to.contact', 'info@greenvacationscr.com');

        return $this
            ->locale($this->mailLocale)
            ->from('noreply@greenvacationscr.com', config('mail.from.name', 'Green Vacations CR'))
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.booking_updated')
            ->with([
                'booking'        => $this->booking,
                'mailLocale'     => $this->mailLocale,
                'reference'      => $this->reference,
                'tourLangLabel'  => $this->tourLangLabel,
                'statusLabel'    => $this->statusText,
                'company'        => config('mail.from.name', config('app.name', 'Green Vacations CR')),
                'contactEmail'   => $replyTo,
            ]);
    }
}
