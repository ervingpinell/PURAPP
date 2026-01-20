<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Concerns\BookingMailHelpers;
use Illuminate\Support\Carbon;

class BookingCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, BookingMailHelpers;

    public Booking $booking;

    protected string $mailLocale;
    protected string $reference;
    protected string $tourLangLabel;
    protected string $statusText;

    /**
     * Correos BCC configurados para notificación a administradores.
     */
    protected function adminNotify(): array
    {
        $raw   = config('mail.notifications.address');
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
        // === Cargar relaciones necesarias ===
        $this->booking->loadMissing([
            'user',
            'tour',
            'tourLanguage',
            'hotel',
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

        // === Pickup times desde el primer detalle ===
        $firstDetail = $this->booking->details->first();
        $pickupTime = null;
        $meetingPickupTime = null;

        if ($firstDetail) {
            if (!empty($firstDetail->pickup_time)) {
                $pickupTime = Carbon::parse($firstDetail->pickup_time)->isoFormat('LT');
            }
            if (!empty($firstDetail->meeting_point_pickup_time)) {
                $meetingPickupTime = Carbon::parse($firstDetail->meeting_point_pickup_time)->isoFormat('LT');
            }
        }

        // === Subject traducido ===
        $subject = __('adminlte::email.booking_cancelled_subject', [
            'reference' => $this->reference,
        ], $this->mailLocale);

        // === Reply-To — priorizamos MSFT, luego contacto, luego fallback ===
        $replyTo = config('mail.reply_to.address');

        // === From info ===
        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name', config('app.name', 'Company Name'));

        // === Construcción final del mailable ===
        $mailable = $this
            ->locale($this->mailLocale)
            ->from($fromAddress, $fromName)
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.booking_cancelled')
            ->text('emails.booking_cancelled_plain')
            ->withSymfonyMessage(function ($message) {
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', $this->reference);
            })
            ->with([
                'booking'           => $this->booking,
                'mailLocale'        => $this->mailLocale,
                'reference'         => $this->reference,
                'tourLangLabel'     => $this->tourLangLabel,
                'statusLabel'       => $this->statusText,
                'company'           => $fromName,
                'contactEmail'      => $replyTo,
                'appUrl'            => rtrim(config('app.url'), '/'),
                'companyPhone'      => env('COMPANY_PHONE'),
                'pickupTime'        => $pickupTime,
                'meetingPickupTime' => $meetingPickupTime,
            ]);

        // === BCC a administradores ===
        $bcc = $this->adminNotify();
        if (!empty($bcc)) {
            $mailable->bcc($bcc);
        }

        return $mailable;
    }
}
