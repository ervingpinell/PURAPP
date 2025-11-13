@extends('emails.layouts.base')

@section('content')
@php
    $mailLocale = (isset($lang) && is_string($lang))
        ? (str_starts_with($lang, 'es') ? 'es' : 'en')
        : (str_starts_with(app()->getLocale(), 'es') ? 'es' : 'en');

    $money = fn($n) => '$' . number_format((float)$n, 2);
    $reference = $booking->booking_reference ?? $booking->reference ?? $booking->booking_id;

    $subtotal = $booking->subtotal ?? ($booking->amount_before_discounts ?? null);
    if ($subtotal === null) {
        $subtotal = collect($booking->details ?? [])->flatMap(fn($d) => collect($d->categories ?? []))
            ->reduce(fn($c,$x)=> $c + ((float)($x['quantity']??0) * (float)($x['price']??0)), 0.0);
    }

    $promo           = $booking->redemption?->promoCode ?? $booking->promoCode ?? $booking->promoCodeLegacy;
    $discountAmount  = $promo->discount_amount ?? $promo?->discount ?? null;
    $discountName    = $promo->code ?? $promo?->name ?? null;
    $taxes           = $booking->taxes ?? ($booking->tax ?? null);
    $total           = $booking->total ?? ($booking->amount ?? null);

    $tTitle     = $mailLocale === 'es' ? 'Reserva creada'       : 'Booking created';
    $tRef       = $mailLocale === 'es' ? 'Referencia'            : 'Reference';
    $tSummary   = $mailLocale === 'es' ? 'Resumen'               : 'Summary';
    $tBreakdown = $mailLocale === 'es' ? 'Desglose por cliente'  : 'Customer breakdown';
    $tSubtotal  = $mailLocale === 'es' ? 'Subtotal'              : 'Subtotal';
    $tDiscount  = $mailLocale === 'es' ? 'Descuento'             : 'Discount';
    $tTaxes     = $mailLocale === 'es' ? 'Impuestos'             : 'Taxes';
    $tTotal     = $mailLocale === 'es' ? 'Total'                 : 'Total';

    $d = collect($details ?? $booking->details ?? [])->first();

    /* ===== Locale preferido para el nombre del tour ===== */
    $preferredLoc = strtolower(
        $booking->locale ?? $booking->language_code ?? $mailLocale ?? app()->getLocale()
    );
    $preferredLoc = \Illuminate\Support\Str::of($preferredLoc)->before('-')->lower()->value();

    // 1) Snapshot
    $tourName = $d?->tour_name;

    // 2) Relación Tour traducida
    if (!$tourName && $d?->relationLoaded('tour') && $d?->tour) {
        $tour = $d->tour;

        if (isset($tour->translated_name) && filled($tour->translated_name)) {
            $tourName = $tour->translated_name;
        }
        if (!$tourName && method_exists($tour, 'getTranslated')) {
            $tourName = $tour->getTranslated('name', $preferredLoc) ?? $tour->name ?? null;
        }
        if (!$tourName) {
            $tr = $tour->relationLoaded('translations')
                ? $tour->translations->firstWhere('locale', $preferredLoc)
                : $tour->translations()->where('locale', $preferredLoc)->first();
            $tourName = $tr->name ?? $tour->name ?? null;
        }
    }
    $tourName = $tourName ?: ($mailLocale === 'es' ? 'Tour' : 'Tour');

    $tourDate = $d?->tour_date ? \Illuminate\Support\Carbon::parse($d->tour_date)->format('Y-m-d') : null;
    $scheduleTxt = $d?->schedule
        ? \Illuminate\Support\Carbon::parse($d->schedule->start_time)->isoFormat('LT') . ' – ' . \Illuminate\Support\Carbon::parse($d->schedule->end_time)->isoFormat('LT')
        : null;

    $tourLang = optional($d?->tourLanguage)->language_name
        ?? optional($d?->tourLanguage)->name
        ?? optional($booking->tourLanguage)->language_name
        ?? optional($booking->tourLanguage)->language
        ?? null;

    $meetingName = $d?->meeting_point_name;
    $meetingUrl  = $d?->meeting_point_map_url;
    if (!$meetingName) {
        $mp = $d?->meetingPoint;
        if ($mp) {
            if (method_exists($mp, 'getTranslated')) {
                $meetingName = $mp->getTranslated('name', app()->getLocale()) ?? $mp->name;
            } else {
                $loc = \Illuminate\Support\Str::of(app()->getLocale())->before('-')->lower()->value();
                $tr  = $mp->relationLoaded('translations')
                    ? $mp->translations->firstWhere('locale', $loc)
                    : $mp->translations()->where('locale', $loc)->first();
                $meetingName = $tr->name ?? $mp->name;
            }
            $meetingUrl = $meetingUrl ?: ($mp->map_url ?? null);
        }
    }

    $hotelName = (($d?->is_other_hotel ?? false) && filled($d?->other_hotel_name))
        ? $d->other_hotel_name
        : (optional($d?->hotel)->name ?? optional($booking->hotel)->name ?? null);
@endphp

{{-- Encabezado --}}
<div class="section-card" style="margin-bottom:14px;">
  <div class="section-title" style="margin-bottom:4px;">{{ $tTitle }}</div>
  <div style="font-size:13px;color:#6b7280;">{{ $tRef }}: {{ $reference }}</div>
</div>

{{-- RESUMEN --}}
<div class="section-card" style="margin-bottom:12px;">
  <div class="section-title" style="margin-bottom:6px;font-weight:700;">{{ $tSummary }}</div>
  <div style="font-size:14px;color:#374151;">
    @if($tourName)<div><strong>Tour:</strong> {{ $tourName }}</div>@endif
    @if($tourDate)<div><strong>{{ $mailLocale==='es'?'Fecha del tour':'Tour date' }}:</strong> {{ $tourDate }}</div>@endif
    @if($scheduleTxt)<div><strong>{{ $mailLocale==='es'?'Horario':'Schedule' }}:</strong> {{ $scheduleTxt }}</div>@endif
    @if($tourLang)<div><strong>{{ $mailLocale==='es'?'Idioma':'Language' }}:</strong> {{ $tourLang }}</div>@endif

    @if($meetingName)
      <div><strong>{{ $mailLocale==='es'?'Punto de encuentro':'Meeting point' }}:</strong>
        @if($meetingUrl)
          <a href="{{ $meetingUrl }}" target="_blank" rel="noopener" style="color:#0ea5e9;text-decoration:none;">{{ $meetingName }}</a>
        @else
          {{ $meetingName }}
        @endif
      </div>
    @elseif($hotelName)
      <div><strong>{{ $mailLocale==='es'?'Hotel pickup':'Hotel pickup' }}:</strong> {{ $hotelName }}</div>
    @endif

    @php $notes = trim((string)($booking->notes ?? '')); @endphp
    @if($notes !== '')<div><strong>{{ $mailLocale==='es'?'Notas':'Notes' }}:</strong> {{ $notes }}</div>@endif
  </div>
</div>

{{-- DESGLOSE POR CLIENTE --}}
@include('emails.partials.booking-line-items', [
  'booking'        => $booking,
  'details'        => $details ?? null,
  'mailLocale'     => $mailLocale,
  'suppressHeader' => true,
])

{{-- TOTALES (inline) --}}
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
