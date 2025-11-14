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
    public ?string $locale;

    /**
     * @param array $data [
     *   'name'    => string,
     *   'email'   => string,
     *   'subject' => string|null,
     *   'message' => string,
     *   'locale'  => string|null ('es', 'en', etc.)
     * ]
     */
    public function __construct(array $data)
    {
        $this->name        = (string) ($data['name']    ?? '');
        $this->email       = (string) ($data['email']   ?? '');
        $this->subjectLine = (string) ($data['subject'] ?? '');
        $this->messageText = (string) ($data['message'] ?? '');
        $this->locale      = $data['locale'] ?? null;
    }

    public function build()
    {
        // Forzamos locale a 'es' o 'en' de forma simple (puedes ajustar si tienes tu trait RestrictsEmailLocale)
        $current = strtolower($this->locale ?? app()->getLocale());
        $mailLocale = str_starts_with($current, 'es') ? 'es' : 'en';

        // Subject: si viene desde el formulario se respeta, si no, usamos traducción
        $subject = $this->subjectLine !== ''
            ? $this->subjectLine
            : __('emails.contact.subject', [], $mailLocale);

        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

        // Reply-To: al correo del cliente que llenó el formulario
        $replyToAddress = $this->email ?: env('MAIL_TO_CONTACT', $fromAddress);
        $replyToName    = $this->name  ?: $replyToAddress;

        $contactEmail = env('MAIL_TO_CONTACT', $fromAddress);

        return $this
            ->locale($mailLocale)
            ->from($fromAddress, $fromName)
            ->replyTo($replyToAddress, $replyToName)
            ->subject($subject)
            ->view('emails.contact_message')
            ->with([
                'name'         => $this->name,
                'email'        => $this->email,
                'subjectLine'  => $this->subjectLine,
                'messageText'  => $this->messageText,
                'mailLocale'   => $mailLocale,
                'company'      => $fromName,
                'contactEmail' => $contactEmail,
                'appUrl'       => rtrim(config('app.url'), '/'),
                'companyPhone' => env('COMPANY_PHONE'),
                // El layout usará env('COMPANY_LOGO_URL') para el logo
            ]);
    }
}
