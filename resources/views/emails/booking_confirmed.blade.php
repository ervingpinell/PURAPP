@extends('emails.layouts.base')

@section('content')
@php
// Use the mailLocale passed from the Mailable (already determined by product language)
$mailLocale = $mailLocale ?? 'en';
$money = fn($n) => '$' . number_format((float) $n, 2);
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

$subtotal = $booking->subtotal ?? ($booking->amount_before_discounts ?? null);
if ($subtotal === null) {
$subtotal = collect($booking->details ?? [])->flatMap(fn($d) => collect($d->categories ?? []))
->reduce(fn($c, $x) => $c + ((float) ($x['quantity'] ?? 0) * (float) ($x['price'] ?? 0)), 0.0);
}

$promo = $booking->redemption?->promoCode ?? $booking->promoCode ?? $booking->promoCodeLegacy;
$discountName = null;
$adjustmentAmount = null;
$adjustmentType = null;

if ($promo) {
$discountName = $promo->code ?? $promo->name ?? null;

// Check for both discount_percent and discount_amount
$percentValue = $promo->discount_percent ?? null;
$amountValue = $promo->discount_amount ?? $promo->discount ?? null;

// Calculate actual discount amount
if ($percentValue !== null && $percentValue != 0.0) {
// Percentage discount - calculate from subtotal
$adjustmentAmount = abs(($subtotal * (float)$percentValue) / 100);
$op = strtolower($promo->operation ?? 'subtract');
$adjustmentType = ($op === 'add') ? 'surcharge' : 'discount';
} elseif ($amountValue !== null && $amountValue != 0.0) {
// Fixed amount discount
$adjustmentAmount = abs((float)$amountValue);
$op = strtolower($promo->operation ?? 'subtract');
$adjustmentType = ($op === 'add') ? 'surcharge' : 'discount';
}
}

$hasAdjustment = $adjustmentAmount !== null && $adjustmentAmount > 0;

$taxes = $booking->taxes ?? ($booking->tax ?? null);
$total = $booking->total ?? ($booking->amount ?? null);

$effectiveAdj = 0.0;
if ($hasAdjustment) {
$signForCalc = $adjustmentType === 'discount' ? 1 : -1;
$effectiveAdj = $signForCalc * (float) $adjustmentAmount;
}

$tConfirmed = $mailLocale === 'es' ? 'Reserva confirmada' : 'Booking confirmed';
$tRef = $mailLocale === 'es' ? 'Referencia' : 'Reference';
$tSummary = $mailLocale === 'es' ? 'Resumen' : 'Summary';
$tSubtotal = $mailLocale === 'es' ? 'Subtotal' : 'Subtotal';
$tTaxes = $mailLocale === 'es' ? 'Impuestos' : 'Taxes';
$tTotal = $mailLocale === 'es' ? 'Total' : 'Total';

$d = collect($booking->details ?? [])->first();

// Use mailLocale for product name translation (already set from product language)
$preferredLoc = $mailLocale;

$productName = $d?->product_name;
if (!$productName && $d?->relationLoaded('product') && $d?->product) {
$product = $d->product;
if (isset($product->translated_name) && filled($product->translated_name)) {
$productName = $product->translated_name;
}
if (!$productName && method_exists($product, 'getTranslated')) {
$productName = $product->getTranslated('name', $preferredLoc) ?? $product->name ?? null;
}
if (!$productName) {
$tr = $product->relationLoaded('translations')
? $product->translations->firstWhere('locale', $preferredLoc)
: $product->translations()->where('locale', $preferredLoc)->first();
$productName = $tr->name ?? $product->name ?? null;
}
}
$productName = $productName ?: ($mailLocale === 'es' ? 'Product' : 'Product');

$productDate = $d?->product_date ? \Illuminate\Support\Carbon::parse($d->product_date)->format('d-M-Y') : null;
$scheduleTxt = $d?->schedule
? \Illuminate\Support\Carbon::parse($d->schedule->start_time)->isoFormat('LT') . ' – ' . \Illuminate\Support\Carbon::parse($d->schedule->end_time)->isoFormat('LT')
: null;

$productLang = optional($d?->productLanguage)->language_name
?? optional($d?->productLanguage)->name
?? optional($booking->productLanguage)->language_name
?? optional($booking->productLanguage)->language
?? null;

$meetingName = $d?->meeting_point_name;
$meetingUrl = $d?->meeting_point_map_url;
if (!$meetingName) {
$mp = $d?->meetingPoint;
if ($mp) {
if (method_exists($mp, 'getTranslated')) {
$meetingName = $mp->getTranslated('name', $mailLocale) ?? $mp->name;
} else {
$tr = $mp->relationLoaded('translations')
? $mp->translations->firstWhere('locale', $mailLocale)
: $mp->translations()->where('locale', $mailLocale)->first();
$meetingName = $tr->name ?? $mp->name;
}
$meetingUrl = $meetingUrl ?: ($mp->map_url ?? null);
}
}

$hotelName = (($d?->is_other_hotel ?? false) && filled($d?->other_hotel_name))
? $d->other_hotel_name
: (optional($d?->hotel)->name ?? optional($booking->hotel)->name ?? null);

// Pickup times (desde el detail, con fallback si no vienen del mailable)
$pickupTime = $pickupTime ?? null;
$meetingPickupTime = $meetingPickupTime ?? null;

if ($d) {
if ($pickupTime === null && !empty($d->pickup_time)) {
$pickupTime = \Illuminate\Support\Carbon::parse($d->pickup_time)->isoFormat('LT');
}
if ($meetingPickupTime === null && !empty($d->meeting_point_pickup_time)) {
$meetingPickupTime = \Illuminate\Support\Carbon::parse($d->meeting_point_pickup_time)->isoFormat('LT');
}
}

$notes = trim((string) ($booking->notes ?? ''));
@endphp

{{-- 1. BOOKING STATUS --}}
<div class="section-card" style="margin-bottom:14px;">
  <div class="section-title" style="margin-bottom:4px;color:#256d1b;font-size:24px;">{{ $tConfirmed }}</div>
  <div style="font-size:13px;color:#6b7280;">{{ $tRef }}: {{ $reference }}</div>
</div>

{{-- 2. BOOKING SUMMARY --}}
<div class="section-card" style="margin-bottom:12px;">
  <div class="section-title" style="margin-bottom:6px;font-weight:700;">{{ $tSummary }}</div>
  <div style="font-size:14px;color:#374151;">
    @if($productName)
    <div><strong>{{ __('adminlte::email.service') }}:</strong> {{ $productName }}</div>
    @endif

    @if($productDate)
    <div><strong>{{ $mailLocale==='es'?'Fecha del producto':'Product date' }}:</strong> {{ $productDate }}</div>
    @endif

    @if($scheduleTxt)
    <div><strong>{{ $mailLocale==='es'?'Horario':'Schedule' }}:</strong> {{ $scheduleTxt }}</div>
    @endif

    @if($productLang)
    <div><strong>{{ $mailLocale==='es'?'Idioma':'Language' }}:</strong> {{ $productLang }}</div>
    @endif

    @if($meetingName)
    <div>
      <strong>{{ $mailLocale==='es'?'Punto de encuentro':'Meeting point' }}:</strong>
      @if($meetingUrl)
      <a href="{{ $meetingUrl }}" target="_blank" rel="noopener" style="color:#3869d4;text-decoration:none;">
        {{ $meetingName }}
      </a>
      @else
      {{ $meetingName }}
      @endif
    </div>

    @if(!empty($meetingPickupTime))
    <div>
      <strong>{{ $mailLocale==='es' ? 'Hora de recogida' : 'Pickup time' }}:</strong>
      {{ $meetingPickupTime }}
    </div>
    @endif

    @elseif($hotelName)
    <div>
      <strong>{{ $mailLocale==='es'?'Hotel pickup':'Hotel pickup' }}:</strong>
      {{ $hotelName }}
    </div>

    @if(!empty($pickupTime))
    <div>
      <strong>{{ $mailLocale==='es' ? 'Hora de recogida' : 'Pickup time' }}:</strong>
      {{ $pickupTime }}
    </div>
    @endif
    @endif

    @if($notes !== '')
    <div><strong>{{ $mailLocale==='es'?'Notas':'Notes' }}:</strong> {{ $notes }}</div>
    @endif
  </div>
</div>

{{-- 3. DESGLOSE CLIENTES --}}
@include('emails.partials.booking-line-items', [
'booking' => $booking,
'mailLocale' => $mailLocale,
'showLineTotals' => true,
])

{{-- 4. TOTALES --}}
<div class="totals-inline">
  <div class="row">
    <span class="label">{{ $tSubtotal }}:</span>
    <span class="amount">{{ $money($subtotal) }}</span>
  </div>

  @if($hasAdjustment)
  @php
  $isDiscount = $adjustmentType === 'discount';
  $adjLabel = $mailLocale === 'es'
  ? ($isDiscount ? 'Descuento' : 'Recargo')
  : ($isDiscount ? 'Discount' : 'Surcharge');
  $adjPrefix = $isDiscount ? '-' : '+';
  $adjAmount = $money($adjustmentAmount);
  @endphp
  <div class="row">
    <span class="label">{{ $adjLabel }}:</span>
    <span class="amount">{{ $adjPrefix }}{{ $adjAmount }}</span>
    @if($discountName)
    <span class="muted">({{ $discountName }})</span>
    @endif
  </div>
  @endif

  @if($taxes && $taxes != 0)
  <div class="row">
    <span class="label">{{ $tTaxes }}:</span>
    <span class="amount">{{ $money($taxes) }}</span>
  </div>
  @endif

  <div class="row total">
    <span class="label">{{ $tTotal }}:</span>
    <span class="amount">
      {{ $total !== null
            ? $money($total)
            : $money(max(0, (float) $subtotal - (float) $effectiveAdj + (float) ($taxes ?? 0))) }}
    </span>
  </div>
</div>
@endsection