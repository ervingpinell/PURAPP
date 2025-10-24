<?php

namespace App\Mail;

use App\Models\ReviewReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewReplyNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ReviewReply $reply;
    public string $adminName;
    public ?string $tourName;
    public ?string $customerName;

    public function __construct(ReviewReply $reply, string $adminName, ?string $tourName = null, ?string $customerName = null)
    {
        // Cargamos tour por si el blade lo necesita
        $this->reply        = $reply->loadMissing('review.tour');
        $this->adminName    = $adminName;
        $this->tourName     = $tourName;
        $this->customerName = $customerName;
    }

    public function build()
    {
        // Marca/branding (AdminLTE > app)
        $brandName   = config('adminlte.logo', config('app.name', 'Green Vacations CR'));
        $logoRelPath = config('adminlte.auth_logo.img.path')
            ?? config('adminlte.logo_img')
            ?? 'images/logoCompanyWhite.png';

        // Datos de contacto
        $contact = [
            'site'  => 'https://greenvacationscr.com',
            'email' => 'info@greenvacationscr.com',
            'phone' => '+506 2479 1471',
        ];

        // Asunto traducido (si hay tour, lo agregamos al final)
        $subject = __('reviews.emails.reply.subject');
        if ($this->tourName) {
            $subject .= ' — ' . $this->tourName;
        }

        return $this->subject($subject)
            ->view('emails.reply_html', [
                'brandName'    => $brandName,
                'logoRelPath'  => $logoRelPath,
                'adminName'    => $this->adminName,
                'tourName'     => $this->tourName,
                'customerName' => $this->customerName,
                'body'         => $this->reply->body, // el blade usa {{ $body }}
                'contact'      => $contact,
            ])
            ->text('emails.reply_text', [
                'brandName'    => $brandName,
                'adminName'    => $this->adminName,
                'tourName'     => $this->tourName,
                'customerName' => $this->customerName,
                'body'         => $this->reply->body,
                'contact'      => $contact,
            ]);
    }
}
