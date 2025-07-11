<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Green Vacations – Test Email';

    public function build()
    {
        return $this->subject('Correo de prueba Green Vacations')
                    ->view('emails.test');
    }
}
