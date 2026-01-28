<?php

namespace App\Mail;

use App\Models\ReviewRequest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewRequestLink extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ReviewRequest $rr;
    protected string $mailLocale;

    public function __construct(ReviewRequest $rr, ?string $locale = null)
    {
        $this->rr = $rr->loadMissing(['booking.product', 'user']);

        // Detect locale: Spanish or English (controller determines based on tour language)
        $current = strtolower($locale ?? app()->getLocale() ?: config('app.locale', 'en'));
        $this->mailLocale = str_starts_with($current, 'es') ? 'es' : 'en';
    }

    public function build()
    {
        $rr  = $this->rr->loadMissing(['booking.product', 'user']);
        $loc = $this->mailLocale;
        Carbon::setLocale($loc);

        // === URL del CTA ===
        // Appending lang parameter to ensure correct locale on landing
        $ctaUrl = route('reviews.request.show', ['token' => $rr->token]);

        // === Nombre del cliente ===
        $userName = optional($rr->user)->full_name
            ?? $rr->customer_name
            ?? optional($rr->booking)->customer_name
            ?? null;

        // === Email del cliente (solo a él) ===
        $candidateEmails = [
            optional($rr->user)->email,
            data_get($rr, 'customer_email'),
            optional(optional($rr->booking)->user)->email,
        ];
        $to = collect($candidateEmails)
            ->filter(fn($e) => is_string($e) && filter_var($e, FILTER_VALIDATE_EMAIL))
            ->first();

        if (!$to) {
            \Log::warning('[ReviewRequestLink] No valid customer email. Skipping send.', [
                'review_request_id' => $rr->id ?? null,
            ]);

            return $this
                ->locale($loc)
                ->subject('')
                ->view('emails.reviews.review-link_plain', [
                    'userName'         => $userName,
                    'tourName'         => '',
                    'activityDateText' => null,
                    'ctaUrl'           => $ctaUrl,
                    'expiresAtText'    => null,
                ]);
        }

        // === Product y textos ===
        $product = optional($rr->booking)->product;
        
        // Get product name in the email's language
        if ($product) {
            $tourName = $product->getTranslation('name', $loc, false) ?? $product->name;
        } else {
            $tourName = __('reviews.generic.our_tour', [], $loc);
        }

        $bk = $rr->booking;
        $activityDateText = $this->fmtDate(
            $bk?->start_date
            ?? $bk?->activity_date
            ?? $bk?->tour_date
            ?? $bk?->created_at
            ?? $rr->created_at,
            $loc
        );

        $expiresAtText = $this->fmtDate($rr->expires_at, $loc);

        // SUBJECT: que suene más transaccional, no promo.
        // Asegúrate de que la traducción no tenga emojis ni “¡oferta!” y cosas así.
        $subject = __('reviews.emails.request.subject', [
            'tour' => $tourName,
        ], $loc);

        $preheader = $activityDateText
            ? __('reviews.emails.request.preheader_with_date', [
                'tour' => $tourName,
                'date' => $activityDateText,
            ], $loc)
            : __('reviews.emails.request.preheader', [
                'tour' => $tourName,
            ], $loc);

        // === Remitente / Reply-To ===
        // Usa el mismo remitente que tus correos de confirmación de reserva (transaccionales)
        $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
        $fromName    = config('mail.from.name', config('app.name', 'Company Name'));

        $replyTo = collect([
            env('MSFT_REPLY_TO'),
            env('MAIL_TO_CONTACT'),
            data_get(config('mail.reply_to'), 'address'),
            config('mail.from.address'),
        ])->first(fn($v) => filled($v));

        $contactEmail = $replyTo ?: env('MAIL_TO_CONTACT', $fromAddress);
        $appUrl       = rtrim(config('app.url'), '/');
        $companyPhone = env('COMPANY_PHONE');

        // IMPORTANTE: SOLO al cliente, SIN BCC, SIN cabeceras de bulk.
        $mailable = $this
            ->locale($loc)
            ->from($fromAddress, $fromName)
            ->to($to, $userName ?? $to)
            ->subject($subject)
            ->priority(1) // alta prioridad (no garantiza nada, pero ayuda un poco)
            ->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) {
                // Marcar como importante (otra señal suave; no siempre respeta el tab)
                $message->priority(1);
                
                // Headers para mejorar deliverability y evitar carpeta de promociones
                $headers = $message->getHeaders();
                
                // Marcar como transaccional
                $headers->addTextHeader('X-Entity-Ref-ID', 'review-request');
                
                // Evitar keywords de marketing que Google pueda penalizar
                // pero sí indicar prioridad alta si es crítico

                
                // Categoría de Gmail (intenta que vaya a Primary)
                $headers->addTextHeader('X-Google-Appengine-App-Id', 'transactional');
                
                // No añadimos cosas tipo Precedence: bulk ni List-Unsubscribe
            });

        if ($replyTo) {
            $mailable->replyTo($replyTo);
        }

        return $mailable
            ->view('emails.reviews.review-link', [
                'userName'         => $userName,
                'tourName'         => $tourName,
                'activityDateText' => $activityDateText,
                'ctaUrl'           => $ctaUrl,
                'expiresAtText'    => $expiresAtText,
                'preheader'        => $preheader,
                'mailLocale'       => $loc,
                'company'          => $fromName,
                'contactEmail'     => $contactEmail,
                'appUrl'           => $appUrl,
                'companyPhone'     => $companyPhone,
            ])
            ->text('emails.reviews.review-link_plain', [
                'userName'         => $userName,
                'tourName'         => $tourName,
                'activityDateText' => $activityDateText,
                'ctaUrl'           => $ctaUrl,
                'expiresAtText'    => $expiresAtText,
            ]);
    }

    private function fmtDate($value, string $locale): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            $dt = Carbon::make($value);
            return $dt ? $dt->locale($locale)->isoFormat('LL') : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
