<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewSubmittedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Review $review;
    public ?string $tourName;
    public ?string $customerName;
    public ?string $adminPanelUrl;

    protected string $mailLocale;

    /**
     * @param Review      $review
     * @param string|null $tourName       Nombre del tour (opcional, se intenta resolver igual)
     * @param string|null $customerName   Nombre del cliente (opcional)
     * @param string|null $adminPanelUrl  URL directa al CRUD (edit) o index
     */
    public function __construct(
        Review $review,
        ?string $tourName = null,
        ?string $customerName = null,
        ?string $adminPanelUrl = null
    ) {
        // Cargar relaciones mínimas para mostrar info en el correo
        $this->review        = $review->loadMissing('product', 'booking', 'user');
        $this->tourName      = $tourName;
        $this->customerName  = $customerName;
        $this->adminPanelUrl = $adminPanelUrl;

        // Usar idioma actual de la app
        $this->mailLocale = app()->getLocale();
    }

    /**
     * Resuelve los correos destino para notificaciones de admin.
     */
    protected function resolveAdminRecipients(string $fallback): array
    {
        // Usar setting de base de datos como pide el usuario
        $raw = setting('email.booking_notifications');

        $items = preg_split('/[,\s;]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $emails = collect($items)
            ->map(fn($e) => trim($e))
            ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();

        if (empty($emails)) {
            // Log por si acaso:
            \Log::warning('[ReviewSubmittedNotification] No valid admin notify emails, fallback to fromAddress', [
                'raw' => $raw,
            ]);

            $emails = [$fallback];
        }

        return $emails;
    }

    public function build()
    {
        $loc = $this->mailLocale;

        // ===== Resolver nombres =====
        $product = $this->review->product;

        $resolvedTourName = $this->tourName
            ?? ($product ? ($product->getTranslation('name', $loc, false) ?? $product->name) : null);

        $resolvedCustomer = $this->customerName
            ?? $this->review->author_name
            ?? optional($this->review->user)->full_name
            ?? optional($this->review->booking)->customer_name
            ?? null;

        // ===== Subject (con traducción si existe) =====
        $subject = __('reviews.emails.submitted.subject', [], $loc);
        if ($resolvedTourName) {
            $subject .= ' — ' . $resolvedTourName;
        }

        // FROM
        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Company Name'));

        // DESTINO: lista de correos de admin (pueden ser varios)
        $adminRecipients = $this->resolveAdminRecipients($fromAddress);

        // URL al panel admin (INDEX siempre, como solicitado)
        $adminUrl = $this->adminPanelUrl;
        if (!$adminUrl) {
           $adminUrl = route('admin.reviews.index');
        }

        $contactEmail = $adminRecipients[0] ?? $fromAddress;
        $appUrl       = rtrim(config('app.url'), '/');
        $companyPhone = env('COMPANY_PHONE');
        $company      = config('app.name', 'Company Name');

        return $this
            ->locale($loc)
            ->from($fromAddress, $fromName)
            ->to($adminRecipients)
            ->subject($subject)
            ->view('emails.reviews.submitted', [
                'review'       => $this->review,
                'tourName'     => $resolvedTourName,
                'customerName' => $resolvedCustomer,
                'adminUrl'     => $adminUrl,

                // Para el layout base:
                'mailLocale'   => $loc,
                'company'      => $company,
                'contactEmail' => $contactEmail,
                'appUrl'       => $appUrl,
                'companyPhone' => $companyPhone,
            ])
            ->text('emails.reviews.submitted_text', [
                'review'       => $this->review,
                'tourName'     => $resolvedTourName,
                'customerName' => $resolvedCustomer,
                'adminUrl'     => $adminUrl,
            ]);
    }
}
