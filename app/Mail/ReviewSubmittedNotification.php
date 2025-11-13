<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Concerns\EmbedsBrandAssets;

class ReviewSubmittedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, EmbedsBrandAssets;

    public Review $review;
    public ?string $tourName;
    public ?string $customerName;
    public ?string $adminPanelUrl;

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
        $this->review        = $review->loadMissing('tour', 'booking', 'user');
        $this->tourName      = $tourName;
        $this->customerName  = $customerName;
        $this->adminPanelUrl = $adminPanelUrl;
    }

    public function build()
    {
        // ===== Resolver nombres =====
        $tour = $this->review->tour;

        $resolvedTourName = $this->tourName
            ?? ($tour->translated_name ?? $tour->name ?? null);

        $resolvedCustomer = $this->customerName
            ?? $this->review->author_name
            ?? optional($this->review->user)->full_name
            ?? optional($this->review->booking)->customer_name
            ?? null;

        // ===== Subject (con traducción si existe) =====
        // Clave sugerida: reviews.emails.submitted.subject
        $subject = __('reviews.emails.submitted.subject');
        if ($resolvedTourName) {
            $subject .= ' — ' . $resolvedTourName;
        }

        // FROM
        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

        // DESTINO: correo de notificaciones
        $notifyTo = env('BOOKING_NOTIFY')
            ?: (config('reviews.admin_notify_to')
                ?? config('mail.to.admin')
                ?? $fromAddress);

        // Logo CID + fallback
        $logoCid         = $this->embedLogoCid();
        $appLogoFallback = $this->logoFallbackUrl();

        // URL al panel admin (edit como preferencia, index como fallback)
        $adminUrl = $this->adminPanelUrl;
        if (! $adminUrl) {
            try {
                if (\function_exists('route')) {
                    // Intenta ir directo al edit de esa review
                    $adminUrl = route('admin.reviews.edit', $this->review->id ?? $this->review);
                }
            } catch (\Throwable $e) {
                try {
                    $adminUrl = route('admin.reviews.index');
                } catch (\Throwable $e2) {
                    $adminUrl = null;
                }
            }
        }

        return $this
            ->from($fromAddress, $fromName)
            ->to($notifyTo)
            ->subject($subject)
            ->view('emails.reviews.submitted', [
                'review'          => $this->review,
                'tourName'        => $resolvedTourName,
                'customerName'    => $resolvedCustomer,
                'adminUrl'        => $adminUrl,
                'logoCid'         => $logoCid,
                'appLogoFallback' => $appLogoFallback,
            ])
            ->text('emails.reviews.submitted_text', [
                'review'       => $this->review,
                'tourName'     => $resolvedTourName,
                'customerName' => $resolvedCustomer,
                'adminUrl'     => $adminUrl,
            ]);
    }
}
