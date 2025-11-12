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

    public function __construct(ReviewRequest $rr)
    {
        $this->rr = $rr->loadMissing(['booking.tour', 'user']);
    }

    public function build()
    {
        $rr  = $this->rr->loadMissing(['booking.tour', 'user']);
        $loc = app()->getLocale() ?: config('app.locale', 'es');
        Carbon::setLocale($loc);

        $ctaUrl = route('reviews.request.show', ['token' => $rr->token]);

        $userName = optional($rr->user)->full_name
            ?? $rr->customer_name
            ?? optional($rr->booking)->customer_name
            ?? null;

        // nombre del tour (si no, texto genérico)
        $tourName = optional(optional($rr->booking)->tour)->name ?: __('reviews.generic.our_tour');

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

        // SUBJECT (localizable)
        $subject   = __('reviews.emails.request.subject', ['tour' => $tourName]);
        $preheader = $activityDateText
            ? __('reviews.emails.request.preheader_with_date', ['tour' => $tourName, 'date' => $activityDateText])
            : __('reviews.emails.request.preheader', ['tour' => $tourName]);

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
            env('MAIL_NOTIFICATIONS'), // p.ej. "info@greenvacationscr.com"
            env('MAIL_TO_CONTACT'),
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
        // - HTML: resources/views/emails/reviews/review-link.blade.php
        // - TEXT: resources/views/emails/reviews/review-link_plain.blade.php
        return $mailable
            ->view('emails.reviews.review-link', [
                'userName'        => $userName,
                'tourName'        => $tourName,
                'activityDateText'=> $activityDateText,
                'ctaUrl'          => $ctaUrl,
                'expiresAtText'   => $expiresAtText,
                'preheader'       => $preheader,
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
