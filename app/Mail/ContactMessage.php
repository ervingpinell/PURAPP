<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $subjectLine;
    public string $messageText;

    public function __construct(array $data)
    {
        $this->name        = (string) $data['name'];
        $this->email       = (string) $data['email'];
        $this->subjectLine = (string) $data['subject'];
        $this->messageText = (string) $data['message'];
    }

    public function build()
    {
        // FROM / REPLY-TO desde config/env
        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

        // Destinatario principal desde ENV (BOOKING_NOTIFY) con fallbacks razonables
        $to = collect([
            env('BOOKING_NOTIFY'),
            env('MAIL_TO_CONTACT'),
            data_get(config('mail.reply_to'), 'address'),
            config('mail.from.address'),
        ])->first(fn ($v) => filled($v));

        // BCC para admins (opcional, coma-separado permitido)
        $bccRaw = env('MAIL_NOTIFICATIONS');
        $bcc    = $bccRaw
            ? array_values(array_filter(array_map('trim', explode(',', $bccRaw))))
            : [];

        $subject = 'Contacto: ' . $this->subjectLine;

        $mailable = $this
            ->from($fromAddress, $fromName)
            ->subject($subject)
            ->replyTo($this->email, $this->name);

        if ($to)   $mailable->to($to);
        if ($bcc)  $mailable->bcc($bcc);

        // Usamos tu layout unificado y pasamos solo los datos del mensaje
        return $mailable
            ->view('emails.contact.message', [
                'name'        => $this->name,
                'email'       => $this->email,
                'subjectLine' => $this->subjectLine,
                'messageText' => $this->messageText,
            ])
            ->text('emails.contact.message_text', [
                'name'        => $this->name,
                'email'       => $this->email,
                'subjectLine' => $this->subjectLine,
                'messageText' => $this->messageText,
            ]);
    }
}
