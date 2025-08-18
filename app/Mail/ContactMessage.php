<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
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
        // IMPORTANTE: usar replyTo para poder responder al cliente desde tu bandeja
        return $this->subject('Contacto: ' . $this->subjectLine)
            ->replyTo($this->email, $this->name)
            ->markdown('emails.contact.message', [
                'name'        => $this->name,
                'email'       => $this->email,
                'subjectLine' => $this->subjectLine,
                'messageText' => $this->messageText,
            ]);
    }
}
