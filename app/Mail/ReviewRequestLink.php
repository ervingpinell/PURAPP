<?php

namespace App\Mail;

use App\Models\ReviewRequest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Concerns\EmbedsBrandAssets;

class ReviewRequestLink extends Mailable implements ShouldQueue
{
use Queueable, SerializesModels, EmbedsBrandAssets;

    public ReviewRequest $rr;

    public function __construct(ReviewRequest $rr)
    {
        $this->rr = $rr->loadMissing(['booking.tour', 'user']);
    }

public function build()
{
    $rr  = $this->rr->loadMissing(['booking.tour', 'user']);
    $loc = app()->getLocale() ?: config('app.locale', 'es');
    \Carbon\Carbon::setLocale($loc);

    // === URL del CTA ===
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
        // Evita “enviar a nadie” accidentalmente; devolvemos el mailable sin destinatarios
        // (Laravel no enviará nada).
        return $this->subject('')->view('emails.reviews.review-link_plain', [
            'userName'        => $userName,
            'tourName'        => '',
            'activityDateText'=> null,
            'ctaUrl'          => $ctaUrl,
            'expiresAtText'   => null,
        ]);
    }

    // === Tour y textos ===
    $tourName = optional(optional($rr->booking)->tour)->name
        ?: __('reviews.generic.our_tour');

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

    $subject   = __('reviews.emails.request.subject', ['tour' => $tourName]);
    $preheader = $activityDateText
        ? __('reviews.emails.request.preheader_with_date', ['tour' => $tourName, 'date' => $activityDateText])
        : __('reviews.emails.request.preheader', ['tour' => $tourName]);

    // === Remitente / Reply-To ===
    $fromAddress = config('mail.from.address', 'noreply@greenvacationscr.com');
    $fromName    = config('mail.from.name', config('app.name', 'Green Vacations CR'));

    $replyTo = collect([
        env('MSFT_REPLY_TO'),
        env('MAIL_TO_CONTACT'),
        data_get(config('mail.reply_to'), 'address'),
        config('mail.from.address'),
    ])->first(fn ($v) => filled($v));

    // === CID + fallback (si usas EmbedsBrandAssets) ===
    $logoCid         = method_exists($this, 'embedLogoCid') ? $this->embedLogoCid() : null;
    $appLogoFallback = method_exists($this, 'logoFallbackUrl') ? $this->logoFallbackUrl() : null;

    // IMPORTANTE: SOLO al cliente, SIN BCC admins / empresa
    $mailable = $this
        ->from($fromAddress, $fromName)
        ->to($to)
        ->subject($subject);

    if ($replyTo) {
        $mailable->replyTo($replyTo);
    }

    return $mailable
        ->view('emails.reviews.review-link', [
            'userName'        => $userName,
            'tourName'        => $tourName,
            'activityDateText'=> $activityDateText,
            'ctaUrl'          => $ctaUrl,
            'expiresAtText'   => $expiresAtText,
            'preheader'       => $preheader,
            'logoCid'         => $logoCid,
            'appLogoFallback' => $appLogoFallback,
        ])
        ->text('emails.reviews.review-link_plain', [
            'userName'        => $userName,
            'tourName'        => $tourName,
            'activityDateText'=> $activityDateText,
            'ctaUrl'          => $ctaUrl,
            'expiresAtText'   => $expiresAtText,
        ]);
}


    private function fmtDate($value, string $locale): ?string
    {
        if (!$value) return null;
        try {
            $dt = Carbon::make($value);
            return $dt ? $dt->locale($locale)->isoFormat('LL') : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
