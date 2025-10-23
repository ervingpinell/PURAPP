<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestEmail extends Mailable implements ShouldQueue // 👈 acá también
{
    use Queueable, SerializesModels;

    public $subject = 'Green Vacations – Test Email';

    public function build()
    {
        return $this->subject('Correo de prueba Green Vacations')
                    ->view('emails.test');
    }
}
