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
        // Cargamos relaciones que el blade podría usar
        $this->rr = $rr->loadMissing(['booking.tour', 'user']);
    }

    public function build()
    {
        $rr   = $this->rr->loadMissing(['booking.tour', 'user']);
        $loc  = app()->getLocale() ?: config('app.locale', 'es');
        Carbon::setLocale($loc);

        // URL del formulario público
        $ctaUrl = route('reviews.request.show', ['token' => $rr->token]);

        // Nombre del cliente (si está disponible)
        $userName = optional($rr->user)->full_name
            ?? $rr->customer_name
            ?? optional($rr->booking)->customer_name
            ?? null;

        // Nombre del tour (si está disponible)
        $tourName = optional(optional($rr->booking)->tour)->name ?: __('reviews.generic.our_tour');

        // Fecha de actividad (buscando varias columnas comunes; null-safe)
        $bk = $rr->booking;
        $activityDateText = $this->fmtDate(
            $bk?->start_date
            ?? $bk?->activity_date
            ?? $bk?->tour_date
            ?? $bk?->service_date
            ?? $bk?->travel_date
            ?? $bk?->date
            ?? $bk?->scheduled_for
            ?? $bk?->created_at
            ?? $rr->created_at,
            $loc
        );

        // Expiración si existe
        $expiresAtText = $this->fmtDate($rr->expires_at, $loc);

        // Branding / logo (desde AdminLTE con fallback)
        $brandName   = config('adminlte.logo', config('app.name', 'Green Vacations CR'));
        $logoRelPath = config('adminlte.auth_logo.img.path')
            ?? config('adminlte.logo_img')
            ?? 'images/logoCompanyWhite.png';
        $logoRelPath = str_replace('\\', '/', $logoRelPath); // normaliza slashes

        // Preheader traducido (si no hay fecha, lo deja sin la parte de fecha)
        $preheader = $activityDateText
            ? __('reviews.emails.request.preheader_with_date', ['tour' => $tourName, 'date' => $activityDateText])
            : __('reviews.emails.request.preheader', ['tour' => $tourName]);

        // Asunto traducido
        $subject = __('reviews.emails.request.subject', ['tour' => $tourName]);

        return $this->subject($subject)
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

    // (Opcional) por si en algún momento quisieras resolver una URL absoluta del logo
    private function brandLogoUrl(): ?string
    {
        $path = Config::get('adminlte.logo_img') ?? Config::get('brand.logo');
        if (!$path) return null;
        $path = str_replace('\\', '/', $path);
        return asset($path);
    }
}
