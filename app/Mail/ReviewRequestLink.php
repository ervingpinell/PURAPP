<?php

namespace App\Mail;

use App\Models\ReviewRequest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
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
        $rr   = $this->rr->loadMissing(['booking.tour', 'user']);
        $loc  = app()->getLocale() ?: config('app.locale', 'es');
        Carbon::setLocale($loc);

        $ctaUrl = route('reviews.request.show', ['token' => $rr->token]);

        $userName = optional($rr->user)->full_name
            ?? $rr->customer_name
            ?? optional($rr->booking)->customer_name
            ?? null;

        $tourName = optional(optional($rr->booking)->tour)->name ?: __('reviews.generic.our_tour');

        $bk = $rr->booking;
        $activityDateText = $this->fmtDate(
            $bk?->start_date ?? $bk?->activity_date ?? $bk?->tour_date ?? $bk?->created_at ?? $rr->created_at,
            $loc
        );

        $expiresAtText = $this->fmtDate($rr->expires_at, $loc);

        $brandName   = config('adminlte.logo', config('app.name', 'Green Vacations CR'));
        $logoRelPath = config('adminlte.auth_logo.img.path')
            ?? config('adminlte.logo_img')
            ?? 'images/logoCompanyWhite.png';
        $logoRelPath = str_replace('\\', '/', $logoRelPath);

        $preheader = $activityDateText
            ? __('reviews.emails.request.preheader_with_date', ['tour' => $tourName, 'date' => $activityDateText])
            : __('reviews.emails.request.preheader', ['tour' => $tourName]);

        $subject = __('reviews.emails.request.subject', ['tour' => $tourName]);

        $replyTo = config('mail.to.contact', 'info@greenvacationscr.com');

        return $this
            ->from('noreply@greenvacationscr.com', config('mail.from.name', 'Green Vacations CR'))
            ->replyTo($replyTo)
            ->subject($subject)
            ->view('emails.review-link', compact(
                'brandName',
                'logoRelPath',
                'userName',
                'tourName',
                'activityDateText',
                'ctaUrl',
                'expiresAtText',
                'preheader'
            ))
            ->text('emails.review-link_plain', compact(
                'brandName',
                'userName',
                'tourName',
                'activityDateText',
                'ctaUrl',
                'expiresAtText'
            ));
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
