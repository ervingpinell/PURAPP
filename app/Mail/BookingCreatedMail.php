<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class BookingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    /** @var \Illuminate\Support\Collection<int, BookingDetail> */
    public $details;
    public string $lang;
    public string $company;

    /**
     * @param \App\Models\Booking $booking
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection|null $details
     */
    public function __construct(Booking $booking, $details = null)
    {
        $this->booking = $booking->loadMissing(['user']);

        $this->details = collect($details ?? BookingDetail::with(['tour','hotel','schedule','tourLanguage','booking'])
            ->where('booking_id', $booking->booking_id)->get());

        $this->company = config('app.company_name', 'Green Vacations CR');

        // Determinar idioma por el primer detalle (si el controlador manda 1 por item
        // quedará perfecto; si manda varios, ya se validó que son del mismo idioma)
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

        return $this->subject($subject)
            ->view('emails.booking_created')
            ->with([
                'booking' => $this->booking,
                'details' => $this->details,
                'lang'    => $this->lang,
                'company' => $this->company,
            ]);
    }
}
