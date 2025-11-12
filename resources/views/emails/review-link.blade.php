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

    $greeting = __('reviews.emails.request.greeting', [
        'name' => $userName ?: __('reviews.emails.traveler')
    ]);

    $tourLabel = trim(($tourName ?? '') . (!empty($activityDateText) ? " ({$activityDateText})" : ''));
    $intro = __('reviews.emails.request.intro', ['tour' => $tourLabel]);
    $ask   = __('reviews.emails.request.ask');
    $cta   = __('reviews.emails.request.cta');

    $pre   = $preheader ?? $tourLabel; // preheader
@endphp

{{-- Preheader oculto --}}
<span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;">
  {{ $pre }}
</span>

<div class="section-card">
  <div class="section-title" style="margin-bottom:8px;">{{ $company }}</div>

  <div style="font-size:16px;line-height:1.6;color:#111827;">
    <p style="margin:0 0 12px;">{{ $greeting }}</p>
    <p style="margin:0 0 12px;">{{ $intro }}</p>
    <p style="margin:0 0 12px;">{{ $ask }}</p>
  </div>

  <div style="text-align:center;margin:6px 0 10px;">
    <a href="{{ $ctaUrl }}"
       style="display:inline-block;background:#10b981;color:#fff;text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:700;">
      {{ $cta }}
    </a>
  </div>

  <div style="font-size:12px;color:#6b7280;line-height:1.5;">
    <p style="margin:0;">
      {{ __('reviews.emails.request.fallback') }}<br>
      <a href="{{ $ctaUrl }}" style="color:#6b7280;word-break:break-all;">{{ $ctaUrl }}</a>
    </p>
  </div>
</div>

<div class="section-card" style="margin-top:8px;">
  @if(!empty($expiresAtText))
    <p style="margin:0 0 8px 0;font-size:13px;color:#374151;">
      {{ __('reviews.emails.request.expires', ['date' => $expiresAtText]) }}
    </p>
  @endif

  <p style="margin:0 0 8px 0;font-size:13px;color:#374151;">
    {{ __('reviews.emails.request.footer') }}
  </p>

  <div style="font-size:13px;color:#6b7280;">
    {!! __('reviews.emails.contact_line', [
        'email' => '<a href="mailto:'.e($contact['email']).'" style="color:#6b7280">'.e($contact['email']).'</a>',
        'phone' => '<a href="tel:'.preg_replace("/\s+/", "", e($contact['phone'])).'" style="color:#6b7280">'.e($contact['phone']).'</a>',
        'url'   => '<a href="'.e($contact['site']).'" style="color:#6b7280">'.e($contact['site']).'</a>',
    ]) !!}
  </div>
</div>
@endsection
