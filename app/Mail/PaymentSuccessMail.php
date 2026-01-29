<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $locale = $this->booking->product->lang ?? config('app.locale');
        $subject = __('reviews.emails.booking.payment_success_subject', ['ref' => $this->booking->booking_reference], $locale);

        return new Envelope(
            subject: $subject,
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    public function content(): Content
    {
        $passwordSetupUrl = null;
        try {
            if ($this->booking->user && !$this->booking->user->password) {
                $svc = app(\App\Services\Auth\PasswordSetupService::class);
                $tokenData = $svc->generateSetupToken($this->booking->user); // Returns array
                $passwordSetupUrl = route('password.setup.show', ['token' => $tokenData['plain_token']]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to generate password token for email', ['u' => $this->booking->user_id, 'err' => $e->getMessage()]);
        }

        return new Content(
            view: 'emails.customer.payment-success',
            text: 'emails.customer.payment-success_plain',
            with: [
                'booking' => $this->booking,
                'passwordSetupUrl' => $passwordSetupUrl,
            ],
        );
    }

    public function headers(): \Illuminate\Mail\Mailables\Headers
    {
        return new \Illuminate\Mail\Mailables\Headers(
            text: [
                'X-Entity-Ref-ID' => $this->booking->booking_reference,
            ],
        );
    }
}
