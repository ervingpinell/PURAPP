<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public $details; // Collection<BookingDetail>
    public string $lang;
    public string $company;

    public function __construct(Booking $booking, $details = null)
    {
        // Incluir meetingPoint.translations
        $this->booking = $booking->loadMissing([
            'user', 'tour', 'tourLanguage', 'hotel',
            'details.tour', 'details.hotel', 'details.schedule', 'details.tourLanguage',
            'details.meetingPoint', 'details.meetingPoint.translations',
            'redemption.promoCode',
        ]);

        $this->details = collect($details ?? BookingDetail::with([
            'tour','hotel','schedule','tourLanguage','booking',
            'meetingPoint','meetingPoint.translations',
        ])->where('booking_id', $booking->booking_id)->get());

        $this->company = config('mail.from.name', config('app.name', 'Green Vacations CR'));
        $this->lang    = $this->resolveLocale($this->booking, $this->details->first());
    }

    protected function resolveLocale(Booking $booking, ?BookingDetail $detail): string
    {
        $langName = optional($booking->tourLanguage)->language_name
            ?? optional($booking->tourLanguage)->name
            ?? optional($detail?->tourLanguage)->language_name
            ?? optional($detail?->tourLanguage)->name;

        $val = Str::lower((string)$langName);
        if (Str::startsWith($val, 'es')) return 'es';
        if (Str::startsWith($val, 'en')) return 'en';
        return str_starts_with(app()->getLocale() ?? 'en', 'es') ? 'es' : 'en';
    }

    /** Devuelve lista de correos para BCC (desde env/config), validada y sin duplicados. */
    protected function adminNotify(): array
    {
        $raw = config('mail.booking_notify') ?? env('BOOKING_NOTIFY', '');
        $items = preg_split('/[,\s;]+/', (string)$raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $list = collect($items)
            ->map(fn($e) => trim($e))
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        return $list;
    }

    public function build()
    {
        $subject = __('adminlte::email.booking_created_subject', [
            'reference' => $this->booking->booking_reference
        ], $this->lang);

        $replyTo = env('MSFT_REPLY_TO')
            ?: (env('MAIL_TO_CONTACT') ?: (config('mail.reply_to.address') ?: config('mail.from.address')));

        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

        $mailable = $this
            ->locale($this->lang)
            ->from($fromAddress, $fromName)
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.booking_created')
            ->with([
                'booking'      => $this->booking,
                'details'      => $this->details,
                'lang'         => $this->lang,
                'company'      => $this->company,
                'contactEmail' => $replyTo,
                'appUrl'       => rtrim(config('app.url'), '/'),
                'companyPhone' => env('COMPANY_PHONE'),
                'appLogo'      => env('APP_LOGO', env('COMPANY_LOGO', 'images/logo.png')),
            ]);

        $bcc = $this->adminNotify();
        if (!empty($bcc)) {
            $mailable->bcc($bcc);
        }

        return $mailable;
    }
}
