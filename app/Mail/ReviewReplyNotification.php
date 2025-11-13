<?php

namespace App\Mail;

use App\Models\ReviewReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Concerns\EmbedsBrandAssets;

class ReviewReplyNotification extends Mailable implements ShouldQueue
{
use Queueable, SerializesModels, /* BookingMailHelpers, */ EmbedsBrandAssets;

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
    // SUBJECT
    $subject = __('reviews.emails.reply.subject');
    if ($this->tourName) {
        $subject .= ' — ' . $this->tourName;
    }

    // FROM (sin reply-to)
    $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
    $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

    // CID + fallback (logo)
    $logoCid         = $this->embedLogoCid();
    $appLogoFallback = $this->logoFallbackUrl();

    // construir mailable sin BCC / sin replyTo
    $mailable = $this
        ->from($fromAddress, $fromName)
        ->subject($subject);

    return $mailable
        ->view('emails.reviews.reply', [
            'adminName'       => $this->adminName,
            'tourName'        => $this->tourName,
            'customerName'    => $this->customerName,
            'body'            => $this->reply->body,
            'logoCid'         => $logoCid,
            'appLogoFallback' => $appLogoFallback,
        ])
        ->text('emails.reviews.reply_text', [
            'adminName'    => $this->adminName,
            'tourName'     => $this->tourName,
            'customerName' => $this->customerName,
            'body'         => $this->reply->body,
        ]);
}



}
