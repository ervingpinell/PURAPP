<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Concerns\BookingMailHelpers;

/**
 * Admin notification email for new bookings
 * Does NOT include password setup section (security)
 */
class BookingCreatedAdminMail extends Mailable implements ShouldQueue
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
            'user',
            'tour',
            'tourLanguage',
            'hotel',
            'payments',
            'details.tour',
            'details.hotel',
            'details.schedule',
            'details.tourLanguage',
            'details.meetingPoint',
            'details.meetingPoint.translations',
            'redemption.promoCode',
        ]);

        $this->mailLocale    = $this->mailLocaleFromBooking($this->booking);
        $this->reference     = $this->bookingReference($this->booking);
        $this->tourLangLabel = $this->humanTourLanguage($this->mailLocale, $this->booking);
        $this->statusText    = $this->statusLabel($this->mailLocale, $this->booking);

        $subject = __('adminlte::email.booking_created_subject', [
            'reference' => $this->reference,
        ], $this->mailLocale) . ' [ADMIN]';

        $replyTo = config('mail.reply_to.address');

        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name', config('app.name', 'Company Name'));

        return $this
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
                'formatEmailDate' => $this->getDateFormatter($this->mailLocale),
                'paymentUrl'      => $this->booking->getCheckoutUrl(),
                'passwordSetupUrl' => null, // Never show for admin
                'isAdminEmail'     => true,  // Flag to hide password setup section
            ]);
    }

    /**
     * Get date formatter closure for email templates
     * Format: DD/MMM/YYYY (e.g., 21/NOV/2025)
     */
    protected function getDateFormatter(string $locale): \Closure
    {
        return function ($date) use ($locale) {
            if (!$date) return '—';

            $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

            // Get month abbreviation in correct language
            $monthMap = [
                'es' => ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
                'en' => ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
                'de' => ['JAN', 'FEB', 'MÄR', 'APR', 'MAI', 'JUN', 'JUL', 'AOÛ', 'SEP', 'OKT', 'NOV', 'DEZ'],
                'fr' => ['JAN', 'FÉV', 'MAR', 'AVR', 'MAI', 'JUN', 'JUL', 'AOÛ', 'SEP', 'OCT', 'NOV', 'DÉC'],
                'pt' => ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'],
            ];

            $months = $monthMap[$locale] ?? $monthMap['en'];
            $monthAbbr = $months[$carbon->month - 1];

            return $carbon->format('d') . '/' . $monthAbbr . '/' . $carbon->format('Y');
        };
    }
}
