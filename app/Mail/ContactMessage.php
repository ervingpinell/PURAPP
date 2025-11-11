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
        $this->name        = $data['name'];
        $this->email       = $data['email'];
        $this->subjectLine = $data['subject'];
        $this->messageText = $data['message'];
    }

    public function build()
    {
        // Obtener destinatario desde config (para facilitar cambios futuros)
        $contactEmail = config('mail.to.contact', 'info@greenvacationscr.com');

        return $this
            ->from('noreply@greenvacationscr.com', config('mail.from.name', 'Green Vacations CR'))
            ->to($contactEmail)
            ->replyTo($this->email, $this->name)
            ->subject('Contacto: ' . $this->subjectLine)
            ->markdown('emails.contact.message', [
                'name'        => $this->name,
                'email'       => $this->email,
                'subjectLine' => $this->subjectLine,
                'messageText' => $this->messageText,
            ]);
    }
}
