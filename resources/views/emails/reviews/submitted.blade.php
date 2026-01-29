@extends('emails.layouts.base')

@section('content')
@php
    $mailLocale = str_starts_with(app()->getLocale(), 'es') ? 'es' : 'en';

    $rating   = (int) ($review->rating ?? 0);
    $title    = trim((string) ($review->title ?? ''));
    $body     = trim((string) ($review->body ?? ''));
    $created  = $review->created_at ? $review->created_at->format('d-M-Y H:i') : null;
    $provider = $review->provider ?? 'local';

    $productLabel = $productName ?: ($mailLocale === 'es' ? 'Product' : 'Product');
    $customerLabel = $customerName ?: ($mailLocale === 'es' ? 'Cliente' : 'Customer');

    $tHeader   = $mailLocale === 'es' ? 'Nueva reseña recibida'        : 'New review received';
    $tSummary  = $mailLocale === 'es' ? 'Resumen de la reseña'         : 'Review summary';
    $tProduct     = $mailLocale === 'es' ? 'Product'                          : 'Product';
    $tCustomer = $mailLocale === 'es' ? 'Cliente'                       : 'Customer';
    $tRating   = $mailLocale === 'es' ? 'Calificación'                  : 'Rating';
    $tTitle    = $mailLocale === 'es' ? 'Título'                        : 'Title';
    $tBody     = $mailLocale === 'es' ? 'Contenido'                     : 'Content';
    $tDate     = $mailLocale === 'es' ? 'Fecha de envío'               : 'Submitted at';
    $tProvider = $mailLocale === 'es' ? 'Origen'                        : 'Source';
    $tGoAdmin  = $mailLocale === 'es' ? 'Ver en panel de administración' : 'Open in admin panel';
@endphp

<div class="section-card" style="margin-bottom:14px;">
  <div class="section-title" style="margin-bottom:4px;">
    {{ $tHeader }}
  </div>
  @if($productName)
    <div style="font-size:13px;color:#6b7280;">
      {{ $tProduct }}: {{ $productName }}
    </div>
  @endif
</div>

<div class="section-card" style="margin-bottom:12px;">
  <div class="section-title" style="margin-bottom:6px;font-weight:700;">{{ $tSummary }}</div>
  <div style="font-size:14px;color:#374151;">
    @if($productName)
      <div><strong>{{ $tProduct }}:</strong> {{ $productName }}</div>
    @endif

    @if($customerName)
      <div><strong>{{ $tCustomer }}:</strong> {{ $customerName }}</div>
    @endif

    @if($rating > 0)
      <div>
        <strong>{{ $tRating }}:</strong>
        {{ $rating }}/5
        @php
          $stars = str_repeat('★', $rating) . str_repeat('☆', max(0, 5 - $rating));
        @endphp
        <span style="margin-left:6px;">{{ $stars }}</span>
      </div>
    @endif

    @if($created)
      <div><strong>{{ $tDate }}:</strong> {{ $created }}</div>
    @endif

    @if($provider)
      <div><strong>{{ $tProvider }}:</strong> {{ ucfirst($provider) }}</div>
    @endif

    @if($title !== '')
      <div style="margin-top:8px;">
        <strong>{{ $tTitle }}:</strong> {{ $title }}
      </div>
    @endif

    @if($body !== '')
      <div style="margin-top:8px;">
        <strong>{{ $tBody }}:</strong>
        <div style="margin-top:4px;white-space:pre-line;">{{ $body }}</div>
      </div>
    @endif
  </div>
</div>

@if($adminUrl)
  <div class="section-card" style="text-align:center;">
    <a href="{{ $adminUrl }}"
       style="display:inline-block;padding:10px 18px;border-radius:999px;background:#256d1b;color:#fff;
              font-weight:600;font-size:14px;text-decoration:none;">
      {{ $tGoAdmin }}
    </a>
  </div>
@endif
@endsection
