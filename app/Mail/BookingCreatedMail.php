<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use App\Mail\Concerns\EmbedsBrandAssets;

class BookingCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, EmbedsBrandAssets;

    public Booking $booking;
    /** @var \Illuminate\Support\Collection<int, BookingDetail> */
    public $details;
    public string $lang;
    public string $company;

    public function __construct(Booking $booking, $details = null)
    {
        // Precarga de relaciones
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

    /** Lista de correos BCC para admins (BOOKING_NOTIFY / mail.booking_notify). */
    protected function adminNotify(): array
    {
        $raw   = config('mail.booking_notify') ?? env('BOOKING_NOTIFY', '');
        $items = preg_split('/[,\s;]+/', (string)$raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($items)
            ->map(fn($e) => trim($e))
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    public function build()
    {
        // Asegurar relaciones
        $this->booking = $this->booking->loadMissing([
            'user', 'tour', 'tourLanguage', 'hotel',
            'details.tour', 'details.hotel', 'details.schedule', 'details.tourLanguage',
            'details.meetingPoint', 'details.meetingPoint.translations',
            'redemption.promoCode',
        ]);

        // Locale del correo (es/en)
        $firstDetail = optional($this->details)->first();
        $langName = optional($this->booking->tourLanguage)->language_name
            ?? optional($this->booking->tourLanguage)->name
            ?? optional($firstDetail?->tourLanguage)->language_name
            ?? optional($firstDetail?->tourLanguage)->name;

        $val = Str::lower((string)$langName);
        $mailLocale = Str::startsWith($val, 'es') ? 'es'
            : (Str::startsWith($val, 'en') ? 'en'
            : (str_starts_with(app()->getLocale() ?? 'en', 'es') ? 'es' : 'en'));

        // Asunto
        $subject = __('adminlte::email.booking_created_subject', [
            'reference' => $this->booking->booking_reference
        ], $mailLocale);

        // From / Reply-To
        $replyTo = env('MSFT_REPLY_TO')
            ?: (env('MAIL_TO_CONTACT') ?: (data_get(config('mail.reply_to'), 'address') ?: config('mail.from.address')));

        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

        // Logo (CID + fallback)
        $logoCid         = $this->embedLogoCid();
        $appLogoFallback = $this->logoFallbackUrl();

        $mailable = $this
            ->locale($mailLocale)
            ->from($fromAddress, $fromName)
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.booking_created')
            ->with([
                'booking'         => $this->booking,
                'details'         => $this->details ?? BookingDetail::with([
                    'tour','hotel','schedule','tourLanguage','booking',
                    'meetingPoint','meetingPoint.translations',
                ])->where('booking_id', $this->booking->booking_id)->get(),
                'lang'            => $mailLocale,
                'company'         => $fromName,
                'contactEmail'    => $replyTo,
                'appUrl'          => rtrim(config('app.url'), '/'),
                'companyPhone'    => env('COMPANY_PHONE'),
                'appLogo'         => env('APP_LOGO', env('COMPANY_LOGO', 'images/logo.png')),
                'logoCid'         => $logoCid,
                'appLogoFallback' => $appLogoFallback,
            ]);

        // BCC admins
        $bcc = $this->adminNotify();
        if (!empty($bcc)) {
            $mailable->bcc($bcc);
        }

        // opcional: cola dedicada
        $this->onQueue('mail')->afterCommit();

        return $mailable;
    }
}
