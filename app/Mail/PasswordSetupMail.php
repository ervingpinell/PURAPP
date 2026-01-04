<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordSetupMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $setupUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $token,
        public ?string $bookingReference = null
    ) {
        // Generate setup URL
        $this->setupUrl = route('password.setup.show', ['token' => $this->token]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('password_setup.email_subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-setup',
            text: 'emails.password-setup-text',
            with: [
                'user' => $this->user,
                'setupUrl' => $this->setupUrl,
                'bookingReference' => $this->bookingReference,
                'expiresInDays' => 7,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
