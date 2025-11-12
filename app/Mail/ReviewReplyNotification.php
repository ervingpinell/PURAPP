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

    /**
     * @param ReviewReply $reply
     * @param string      $adminName
     * @param string|null $tourName
     * @param string|null $customerName
     */
    public function __construct(ReviewReply $reply, string $adminName, ?string $tourName = null, ?string $customerName = null)
    {
        // por si acaso, suma relación del tour
        $this->reply        = $reply->loadMissing('review.tour');
        $this->adminName    = $adminName;
        $this->tourName     = $tourName;
        $this->customerName = $customerName;
    }

    public function build()
    {
        // SUBJECT (localizable)
        $subject = __('reviews.emails.reply.subject');
        if ($this->tourName) {
            $subject .= ' — ' . $this->tourName;
        }

        // FROM / REPLY-TO desde config/env
        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

        $replyTo = collect([
            env('MSFT_REPLY_TO'),
            env('MAIL_TO_CONTACT'),
            data_get(config('mail.reply_to'), 'address'),
            config('mail.from.address'),
        ])->first(fn ($v) => filled($v));

        // BCC de notificaciones (admins)
        $notifications = array_filter([
            env('MAIL_NOTIFICATIONS'),              // p.ej. "info@greenvacationscr.com"
            env('MAIL_TO_CONTACT'),                 // fallback razonable
        ]);
        $notifications = array_values(array_unique($notifications));

        $mailable = $this
            ->from($fromAddress, $fromName)
            ->subject($subject);

        if ($replyTo) {
            $mailable->replyTo($replyTo);
        }
        if (!empty($notifications)) {
            $mailable->bcc($notifications);
        }

        // Vistas NUEVAS con header/footer unificados:
        // - HTML:  resources/views/emails/reviews/reply.blade.php
        // - TEXT:  resources/views/emails/reviews/reply_text.blade.php
        return $mailable
            ->view('emails.reviews.reply', [
                // Los blades ya leen ENV/config para brand/contact,
                // aquí solo pasamos lo específico del mensaje:
                'adminName'    => $this->adminName,
                'tourName'     => $this->tourName,
                'customerName' => $this->customerName,
                'body'         => $this->reply->body,
            ])
            ->text('emails.reviews.reply_text', [
                'adminName'    => $this->adminName,
                'tourName'     => $this->tourName,
                'customerName' => $this->customerName,
                'body'         => $this->reply->body,
            ]);
    }
}
