<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject = 'Green Vacations – Test Email';

    public function build()
    {
        // Log para confirmar ejecución dentro del Worker
        Log::info('TestEmail::build ejecutado en cola', [
            'to' => $this->to ?? [],
            'queue' => config('queue.default'),
            'env' => config('app.env'),
        ]);

        return $this->subject('Correo de prueba Green Vacations')
                    ->view('emails.test');
    }
}
