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

    // Intro con “sobre <strong>Product</strong>” (HTML)
    $introHtml = __('reviews.emails.reply.intro', [
        'extra' => $productName
            ? __('reviews.reply.about_html', ['product' => e($productName)])
            : '',
    ]);

    // Force locale for this render
    $oldLocale = app()->getLocale();
    app()->setLocale($mailLocale);
@endphp

{{-- Preheader opcional (oculto visualmente) --}}
<span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;">
  {{ strip_tags($introHtml) }}
</span>

<div class="section-card">

  <div style="font-size:16px;line-height:1.6;color:#111827;">
    <p style="margin:0 0 12px;">
      {{ __('reviews.emails.reply.greeting', ['name' => $customerName ?: __('reviews.traveler')]) }}
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


@php
  if(isset($oldLocale)) {
      app()->setLocale($oldLocale);
  }
@endphp
@endsection
