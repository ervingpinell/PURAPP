@extends('emails.layouts.base')

@section('content')
@php
// Use the mailLocale passed from the Mailable (already determined by tour language)
// DO NOT recalculate it here - it's already set correctly in BookingCreatedMail
$mailLocale = $mailLocale ?? 'en'; // Fallback only if not provided

$money = fn($n) => '$' . number_format((float)$n, 2);
$reference = $booking->booking_reference ?? $booking->reference ?? $booking->booking_id;

$subtotal = $booking->subtotal ?? ($booking->amount_before_discounts ?? null);
if ($subtotal === null) {
$subtotal = collect($booking->details ?? [])->flatMap(fn($d) => collect($d->categories ?? []))
->reduce(fn($c,$x)=> $c + ((float)($x['quantity']??0) * (float)($x['price']??0)), 0.0);
}


// Promo: puede ser descuento o recargo
$promo = $booking->redemption?->promoCode ?? $booking->promoCode ?? $booking->promoCodeLegacy;
$discountName = null;
$adjustmentAmount = null; // valor positivo
$adjustmentType = null; // 'discount' | 'surcharge'

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

// Para fallback del total
$effectiveAdj = 0.0;
if ($hasAdjustment) {
$signForCalc = $adjustmentType === 'discount' ? 1 : -1;
$effectiveAdj = $signForCalc * (float)$adjustmentAmount;
}

$tTitle = $mailLocale === 'es' ? 'Reserva creada' : 'Booking created';
$tRef = $mailLocale === 'es' ? 'Referencia' : 'Reference';
$tSummary = $mailLocale === 'es' ? 'Resumen' : 'Summary';
$tSubtotal = $mailLocale === 'es' ? 'Subtotal' : 'Subtotal';
$tTaxes = $mailLocale === 'es' ? 'Impuestos' : 'Taxes';
$tTotal = $mailLocale === 'es' ? 'Total' : 'Total';

$d = collect($details ?? $booking->details ?? [])->first();

// Use mailLocale for tour name translation (already set from tour language)
$preferredLoc = $mailLocale;

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


$tourDate = $d?->tour_date ? $formatEmailDate($d->tour_date) : null;
$scheduleTxt = $d?->schedule
? \Illuminate\Support\Carbon::parse($d->schedule->start_time)->isoFormat('LT') . ' – ' . \Illuminate\Support\Carbon::parse($d->schedule->end_time)->isoFormat('LT')
: null;

$tourLang = optional($d?->tourLanguage)->language_name
?? optional($d?->tourLanguage)->name
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

$notes = trim((string)($booking->notes ?? ''));
@endphp

{{-- 1. BOOKING STATUS --}}
<div class="section-card" style="margin-bottom:14px; background-color:#f9fafb; border:1px solid #e5e7eb; padding:15px;">
  <div class="section-title" style="margin:0 0 10px 0; font-weight:700; color:#111827; font-size:24px;">{{ $tTitle }}</div>
  <div style="font-size:13px;color:#6b7280;">{{ $tRef }}: <strong>{{ $reference }}</strong></div>

  {{-- Customer Details --}}
  <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #e5e7eb; font-size:14px; color:#374151;">
    <div style="margin-bottom:2px;"><strong>{{ $mailLocale === 'es' ? 'Cliente' : 'Customer' }}:</strong> {{ $booking->user->full_name ?? '—' }}</div>
    <div style="margin-bottom:2px;"><strong>Email:</strong> {{ $booking->user->email ?? '—' }}</div>
    <div><strong>{{ $mailLocale === 'es' ? 'Teléfono' : 'Phone' }}:</strong> {{ $booking->user->phone ?? '—' }}</div>
  </div>

  @php
  // Determinar estado de pago
  $paymentStatus = 'pending'; // default
  $paymentStatusText = $mailLocale === 'es' ? 'Pendiente' : 'Pending';

  // Verificar si hay pagos asociados
  if ($booking->relationLoaded('payments') && $booking->payments->isNotEmpty()) {
  $latestPayment = $booking->payments->sortByDesc('created_at')->first();
  if ($latestPayment && $latestPayment->status === 'completed') {
  $paymentStatus = 'paid';
  $paymentStatusText = $mailLocale === 'es' ? 'Pagado' : 'Paid';
  }
  } elseif (isset($booking->payment_status)) {
  // Fallback a campo directo si existe
  if (in_array($booking->payment_status, ['completed', 'paid'])) {
  $paymentStatus = 'paid';
  $paymentStatusText = $mailLocale === 'es' ? 'Pagado' : 'Paid';
  }
  }
  @endphp

  <div style="margin-top:8px;">
    <span style="font-size:12px; color:#6b7280;">{{ $mailLocale === 'es' ? 'Estado de pago' : 'Payment status' }}:</span>
    <span class="payment-status {{ $paymentStatus }}" style="display:inline-block; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; text-transform:uppercase; margin-left:8px; {{ $paymentStatus === 'paid' ? 'background-color:#d1fae5; color:#065f46; border:1px solid #10b981;' : 'background-color:#fef3c7; color:#92400e; border:1px solid #fbbf24;' }}">
      {{ $paymentStatusText }}
    </span>
  </div>

  {{-- Payment validation message --}}
  @if($paymentStatus === 'paid')
  <div style="margin-top:12px; padding:12px; background-color:#f0fdf4; border-left:4px solid #60a862; border-radius:4px;">
    <p style="margin:0; font-size:13px; color:#14532d; line-height:1.5;">
      @if($mailLocale === 'es')
      <strong>✓ Pago recibido.</strong> Nuestro equipo validará el pago y se contactará con usted lo antes posible para confirmar los detalles de su reserva.
      @else
      <strong>✓ Payment received.</strong> Our team will validate the payment and contact you as soon as possible to confirm your booking details.
      @endif
    </p>
  </div>
  @endif

  {{-- Payment Link Button --}}
  @if($paymentStatus === 'pending' && !empty($paymentUrl))
  <div style="margin-top:16px; text-align:center;">
    <a href="{{ $paymentUrl }}" target="_blank" style="display:inline-block; background-color:#60a862; color:#ffffff; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:600; font-size:14px;">
      {{ $mailLocale === 'es' ? 'Pagar Ahora' : 'Pay Now' }}
    </a>
    <div style="margin-top:8px; font-size:12px; color:#6b7280;">
      {{ $mailLocale === 'es' ? 'Haga clic para completar su pago de forma segura.' : 'Click to complete your payment securely.' }}
    </div>
  </div>
  @endif
</div>

{{-- PASSWORD SETUP CTA (for guest users without password) --}}
{{-- ONLY show for CUSTOMER emails, NEVER for admin --}}
@if(!isset($isAdminEmail) || !$isAdminEmail)
@if(!empty($passwordSetupUrl))
<div class="section-card" style="margin-bottom:14px; background-color:#f0fdf4; border:1px solid #60a862; padding:20px; border-radius:4px;">
  {{-- Centered header using table --}}
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="padding-bottom:16px;">
        <div style="font-size:20px; margin-bottom:8px;">🔑</div>
        <div style="font-weight:700; color:#111827; font-size:18px; margin-bottom:8px;">
          {{ $mailLocale === 'es' ? '¡Crea tu cuenta!' : 'Create Your Account!' }}
        </div>
        <div style="font-size:14px; color:#374151; line-height:1.5;">
          {{ $mailLocale === 'es' 
              ? 'Configura una contraseña para acceder a todos los beneficios' 
              : 'Set up a password to access all benefits' }}
        </div>
      </td>
    </tr>
  </table>

  {{-- Benefits List --}}
  <div style="background-color:#ffffff; border-radius:4px; padding:14px; margin-bottom:16px; border:1px solid #e5e7eb;">
    <div style="margin-bottom:8px;">
      <span style="color:#10b981; font-weight:700; margin-right:8px;">✓</span>
      <span style="color:#374151; font-size:14px;">
        {{ $mailLocale === 'es' ? 'Ver todas tus reservas' : 'View all your bookings' }}
      </span>
    </div>
    <div style="margin-bottom:8px;">
      <span style="color:#10b981; font-weight:700; margin-right:8px;">✓</span>
      <span style="color:#374151; font-size:14px;">
        {{ $mailLocale === 'es' ? 'Gestionar tu perfil' : 'Manage your profile' }}
      </span>
    </div>
    <div>
      <span style="color:#10b981; font-weight:700; margin-right:8px;">✓</span>
      <span style="color:#374151; font-size:14px;">
        {{ $mailLocale === 'es' ? 'Recibir ofertas exclusivas' : 'Receive exclusive offers' }}
      </span>
    </div>
  </div>

  {{-- Setup Button - Centered using table --}}
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="padding-bottom:12px;">
        <a href="{{ $passwordSetupUrl }}" target="_blank" style="display:inline-block; background-color:#60a862; color:#ffffff; padding:12px 28px; border-radius:6px; text-decoration:none; font-weight:600; font-size:15px;">
          {{ $mailLocale === 'es' ? 'Crear Mi Cuenta' : 'Create My Account' }}
        </a>
      </td>
    </tr>
  </table>

  {{-- Expiration Notice - Centered using table --}}
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="font-size:12px; color:#6b7280; padding-bottom:8px;">
        {{ $mailLocale === 'es' 
            ? '⏰ Este enlace expira en 7 días' 
            : '⏰ This link expires in 7 days' }}
      </td>
    </tr>
  </table>

  {{-- Skip Option - Centered using table --}}
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="font-size:12px; color:#9ca3af;">
        {{ $mailLocale === 'es' 
      ? 'Puedes crear tu cuenta más tarde si lo prefieres' 
      : 'You can create your account later if you prefer' }}
      </td>
    </tr>
  </table>
</div>
@endif
@endif


{{-- 2. BOOKING SUMMARY --}}
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
      <a href="{{ $meetingUrl }}" target="_blank" rel="noopener" style="color:#3869d4;text-decoration:none;">{{ $meetingName }}</a>
      @else
      {{ $meetingName }}
      @endif
    </div>
    @elseif($hotelName)
    <div><strong>{{ $mailLocale==='es'?'Hotel pickup':'Hotel pickup' }}:</strong> {{ $hotelName }}</div>
    @endif

    @if($notes !== '')<div><strong>{{ $mailLocale==='es'?'Notas':'Notes' }}:</strong> {{ $notes }}</div>@endif
  </div>
</div>

{{-- 3. DESGLOSE CLIENTES --}}
@include('emails.partials.booking-line-items', [
'booking' => $booking,
'details' => $details ?? null,
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
            : $money(max(0, (float)$subtotal - (float)$effectiveAdj + (float)($taxes ?? 0))) }}
    </span>
  </div>
</div>
@endsection