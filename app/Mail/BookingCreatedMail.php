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
    public $details;
    public string $lang;
    public string $company;

    public function __construct(Booking $booking, $details = null)
    {
        $this->booking = $booking->loadMissing(['user']);

        $this->details = collect($details ?? BookingDetail::with(['tour','hotel','schedule','tourLanguage','booking'])
            ->where('booking_id', $booking->booking_id)->get());

        $this->company = config('app.company_name', 'Green Vacations CR');
        $this->lang = $this->resolveLangFromDetail($this->details->first());
    }

    protected function resolveLangFromDetail(?BookingDetail $detail): string
    {
        if (!$detail) return 'en';
        $name = optional($detail->tourLanguage)->name;
        $val  = Str::lower($name ?? '');
        return Str::contains($val, 'es') ? 'es' : 'en';
    }

    public function build()
    {
        $subject = __('adminlte::email.booking_created_subject', [
            'reference' => $this->booking->booking_reference
        ], $this->lang);

        $replyTo = config('mail.to.contact', 'info@greenvacationscr.com');

        return $this
            ->from('noreply@greenvacationscr.com', config('mail.from.name', 'Green Vacations CR'))
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.booking_created')
            ->with([
                'booking' => $this->booking,
                'details' => $this->details,
                'lang'    => $this->lang,
                'company' => $this->company,
            ]);
    }
}
