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
        $this->reply        = $reply->loadMissing('review.tour');
        $this->adminName    = $adminName;
        $this->tourName     = $tourName;
        $this->customerName = $customerName;
    }

    public function build()
    {
        $brandName   = config('adminlte.logo', config('app.name', 'Green Vacations CR'));
        $logoRelPath = config('adminlte.auth_logo.img.path')
            ?? config('adminlte.logo_img')
            ?? 'images/logoCompanyWhite.png';

        $contact = [
            'site'  => 'https://greenvacationscr.com',
            'email' => 'info@greenvacationscr.com',
            'phone' => '+506 2479 1471',
        ];

        $subject = __('reviews.emails.reply.subject');
        if ($this->tourName) {
            $subject .= ' — ' . $this->tourName;
        }

        $replyTo = config('mail.to.contact', 'info@greenvacationscr.com');

        return $this
            ->from('noreply@greenvacationscr.com', config('mail.from.name', 'Green Vacations CR'))
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.reply_html', [
                'brandName'    => $brandName,
                'logoRelPath'  => $logoRelPath,
                'adminName'    => $this->adminName,
                'tourName'     => $this->tourName,
                'customerName' => $this->customerName,
                'body'         => $this->reply->body,
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
