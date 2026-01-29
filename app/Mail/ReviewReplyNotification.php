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
    public ?string $productName;
    public ?string $customerName;
    public ?string $localeOverride;

    protected string $mailLocale;

    /**
     * @param ReviewReply $reply
     * @param string      $adminName      Nombre del admin que responde
     * @param string|null $productName       Nombre del producto (opcional)
     * @param string|null $customerName   Nombre del cliente (opcional)
     * @param string|null $locale         Locale forzado (opcional, ej. 'es', 'en')
     */
    public function __construct(
        ReviewReply $reply,
        string $adminName,
        ?string $productName = null,
        ?string $customerName = null,
        ?string $locale = null
    ) {
        $this->reply          = $reply->loadMissing('review');
        $this->adminName      = $adminName;
        $this->productName       = $productName;
        $this->customerName   = $customerName;
        $this->localeOverride = $locale;

        // Forzar a es/en de forma simple (igual que en otros mailables)
        $current = strtolower($this->localeOverride ?? app()->getLocale());
        $this->mailLocale = str_starts_with($current, 'es') ? 'es' : 'en';
    }

    public function build()
    {
        $loc = $this->mailLocale;

        // ===== SUBJECT =====
        // Ajusta la key según tu lang:
        // ej: "Hemos respondido a tu reseña" / "We've replied to your review"
        $subject = __('reviews.emails.reply.subject', [], $loc);
        if ($this->productName) {
            $subject .= ' — ' . $this->productName;
        }

        // ===== FROM / REPLY-TO =====
        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Company Name'));

        // Email del cliente para reply-to si lo tienes en la reseña
        $customerEmail =
            $this->reply->review->email
            ?? optional($this->reply->review->user)->email
            ?? env('MAIL_TO_CONTACT', $fromAddress);

        $replyTo      = $customerEmail;
        $replyToName  = $this->customerName
            ?: ($this->reply->review->author_name ?? $customerEmail);

        $company      = $fromName;
        $contactEmail = env('MAIL_TO_CONTACT', $fromAddress);
        $appUrl       = rtrim(config('app.url'), '/');
        $companyPhone = env('COMPANY_PHONE');

        return $this
            ->locale($loc)
            ->from($fromAddress, $fromName)
            ->replyTo($replyTo, $replyToName)
            ->subject($subject)
            ->view('emails.reviews.reply')
            ->with([
                'adminName'    => $this->adminName,
                'productName'     => $this->productName,
                'customerName' => $this->customerName,
                'body'         => $this->reply->body,
                'mailLocale'   => $loc,
                'company'      => $company,
                'contactEmail' => $contactEmail,
                'appUrl'       => $appUrl,
                'companyPhone' => $companyPhone,
                // El layout usa env('COMPANY_LOGO_URL') para el logo
            ])
            ->text('emails.reviews.reply_text', [
                'adminName'    => $this->adminName,
                'productName'     => $this->productName,
                'customerName' => $this->customerName,
                'body'         => $this->reply->body,
            ]);
    }
}
