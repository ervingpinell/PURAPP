@extends('emails.layouts.base')

@section('content')
@php
    $mailLocale = str_starts_with(($mailLocale ?? app()->getLocale()), 'es') ? 'es' : 'en';

    $company = $brandName
        ?? ($company ?? config('mail.from.name', config('app.name', 'Green Vacations CR')));

    $contact = [
        'site'  => rtrim(env('COMPANY_SITE', config('app.url')), '/'),
        'email' => env('MAIL_TO_CONTACT', config('mail.from.address')),
        'phone' => env('COMPANY_PHONE', '+506 2479 1471'),
    ];

    // Intro con “sobre <strong>Tour</strong>” (HTML)
    $introHtml = __('reviews.emails.reply.intro', [
        'extra' => $tourName
            ? __('reviews.emails.reply.about_html', ['tour' => e($tourName)])
            : '',
    ]);
@endphp

{{-- Preheader opcional (oculto visualmente) --}}
<span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;">
  {{ strip_tags($introHtml) }}
</span>

<div class="section-card">
  <div class="section-title" style="margin-bottom:8px;">{{ $company }}</div>

  <div style="font-size:16px;line-height:1.6;color:#111827;">
    <p style="margin:0 0 12px;">
      {{ __('reviews.emails.reply.greeting', ['name' => $customerName ?: __('reviews.emails.traveler')]) }}
    </p>

    <p style="margin:0 0 12px;">{!! $introHtml !!}</p>

    <blockquote style="margin:0 0 16px;padding:12px 16px;border-left:4px solid #6b7280;background:#f9fafb;">
      {{ $body }}
    </blockquote>

    <p style="margin:0 0 12px;">{{ __('reviews.emails.reply.closing') }}</p>

    <p style="margin:16px 0 0;">
      {!! __('reviews.emails.reply.sign', ['admin' => e($adminName)]) !!}
    </p>
  </div>
</div>

<div class="section-card" style="margin-top:8px;">
  <div style="font-size:13px;color:#6b7280;">
    {!! __('reviews.emails.contact_line', [
        'email' => '<a href="mailto:'.e($contact['email']).'" style="color:#6b7280">'.e($contact['email']).'</a>',
        'phone' => '<a href="tel:'.preg_replace("/\s+/", "", e($contact['phone'])).'" style="color:#6b7280">'.e($contact['phone']).'</a>',
        'url'   => '<a href="'.e($contact['site']).'" style="color:#6b7280">'.e($contact['site']).'</a>',
    ]) !!}
  </div>
</div>
@endsection
