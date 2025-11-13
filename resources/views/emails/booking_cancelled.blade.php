@extends('emails.layouts.base')

@section('content')
@php
    $mailLocale = str_starts_with(($mailLocale ?? app()->getLocale()), 'es') ? 'es' : 'en';
    $money      = fn($n) => '$' . number_format((float) $n, 2);
    $reference  = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

    $subtotal = $booking->subtotal ?? ($booking->amount_before_discounts ?? null);
    if ($subtotal === null) {
        $subtotal = collect($booking->details ?? [])->flatMap(fn($d) => collect($d->categories ?? []))
            ->reduce(fn($c,$x)=> $c + ((float)($x['quantity']??0) * (float)($x['price']??0)), 0.0);
    }

    $promo           = $booking->promoCode ?? $booking->promoCodeLegacy ?? $booking->redemption?->promoCode;
    $discountAmount  = $promo->discount_amount ?? $promo->discount ?? null;
    $discountName    = $promo->code ?? $promo?->name ?? null;
    $taxes  = $booking->taxes ?? ($booking->tax ?? null);
    $total  = $booking->total ?? ($booking->amount ?? null);

    $tTitle     = $mailLocale === 'es' ? 'Reserva cancelada' : 'Booking cancelled';
    $tRef       = $mailLocale === 'es' ? 'Referencia' : 'Reference';
    $tStatus    = $mailLocale === 'es' ? 'Estado' : 'Status';
    $tMsg       = $mailLocale === 'es'
        ? 'La reserva fue cancelada. A continuación se muestra el detalle originalmente registrado:'
        : 'The booking was cancelled. Below is the originally recorded breakdown:';
    $tSubtotal  = $mailLocale === 'es' ? 'Subtotal'  : 'Subtotal';
    $tDiscount  = $mailLocale === 'es' ? 'Descuento' : 'Discount';
    $tTaxes     = $mailLocale === 'es' ? 'Impuestos' : 'Taxes';
    $tTotal     = $mailLocale === 'es' ? 'Total original' : 'Original total';
@endphp

<div class="section-card" style="margin-bottom:18px;">
  <div class="section-title" style="margin-bottom:4px;">{{ $tTitle }}</div>
  <div style="font-size:13px;color:#6b7280;">{{ $tRef }}: {{ $reference }}</div>
</div>

<div class="section-card" style="margin-bottom:8px;">
  <div class="section-title" style="margin-bottom:4px;">
    {{ $tStatus }}: {{ $statusLabel ?? ($mailLocale === 'es' ? 'Cancelada' : 'Cancelled') }}
  </div>
  <p style="margin:0;color:#6b7280">{{ $tMsg }}</p>
</div>

@include('emails.partials.booking-line-items', [
  'booking'    => $booking,
  'mailLocale' => $mailLocale,
])

<div class="totals-inline">
  <div class="row"><span class="label">{{ $tSubtotal }}:</span> <span class="amount">{{ $money($subtotal) }}</span></div>

  @if($discountAmount && $discountAmount > 0)
    <div class="row">
      <span class="label">{{ $tDiscount }}:</span>
      <span class="amount">-{{ $money($discountAmount) }}</span>
      @if($discountName)
        <span class="muted">({{ $discountName }})</span>
      @endif
    </div>
  @endif

  @if($taxes && $taxes > 0)
    <div class="row"><span class="label">{{ $tTaxes }}:</span> <span class="amount">{{ $money($taxes) }}</span></div>
  @endif

  <div class="row total">
    <span class="label">{{ $tTotal }}:</span>
    <span class="amount">
      {{ $total !== null ? $money($total) : $money(max(0, (float)$subtotal - (float)($discountAmount ?? 0) + (float)($taxes ?? 0))) }}
    </span>
  </div>
</div>
@endsection
