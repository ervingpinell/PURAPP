<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Concerns\BookingMailHelpers;

class BookingCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, BookingMailHelpers;

    public Booking $booking;

    protected string $mailLocale;
    protected string $reference;
    protected string $tourLangLabel;
    protected string $statusText;

    /**
     * Lista de correos BCC para notificar administradores.
     */
    protected function adminNotify(): array
    {
        $raw   = config('mail.booking_notify') ?? env('BOOKING_NOTIFY', '');
        $items = preg_split('/[,\s;]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($items)
            ->map(fn($e) => trim($e))
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $this->booking->loadMissing([
            'user', 'tour', 'tourLanguage', 'hotel',
            'details.tour', 'details.hotel', 'details.schedule', 'details.tourLanguage',
            'details.meetingPoint', 'details.meetingPoint.translations',
            'redemption.promoCode',
        ]);

        $this->mailLocale    = $this->mailLocaleFromBooking($this->booking);
        $this->reference     = $this->bookingReference($this->booking);
        $this->tourLangLabel = $this->humanTourLanguage($this->mailLocale, $this->booking);
        $this->statusText    = $this->statusLabel($this->mailLocale, $this->booking);

        $subject = __('adminlte::email.booking_created_subject', [
            'reference' => $this->reference,
        ], $this->mailLocale);

        $replyTo = collect([
            env('MSFT_REPLY_TO'),
            env('MAIL_TO_CONTACT'),
            data_get(config('mail.reply_to'), 'address'),
            config('mail.from.address'),
        ])->first(fn($v) => filled($v));

        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

        $mailable = $this
            ->locale($this->mailLocale)
            ->from($fromAddress, $fromName)
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.booking_created')
            ->with([
                'booking'       => $this->booking,
                'mailLocale'    => $this->mailLocale,
                'reference'     => $this->reference,
                'tourLangLabel' => $this->tourLangLabel,
                'statusLabel'   => $this->statusText,
                'company'       => $fromName,
                'contactEmail'  => $replyTo,
                'appUrl'        => rtrim(config('app.url'), '/'),
                'companyPhone'  => env('COMPANY_PHONE'),
                // El layout tomará el logo desde env('COMPANY_LOGO_URL')
            ]);

        $bcc = $this->adminNotify();
        if (!empty($bcc)) {
            $mailable->bcc($bcc);
        }

        return $mailable;
    }
}
