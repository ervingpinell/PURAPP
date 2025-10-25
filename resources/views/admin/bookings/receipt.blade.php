{{-- resources/views/admin/bookings/receipts.blade.php --}}

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <title>{{ __('m_bookings.receipt.title') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root { --green-dark:#1A5229; --green-base:#2E8B57; --gray-light:#f0f2f5; --text-color:#333; --font-heading:'Montserrat',sans-serif; --font-body:'Lora',serif; }
    body { font-family:var(--font-body); font-size:14px; background:var(--gray-light); margin:0; padding:30px; color:var(--text-color); line-height:1.5; }
    .receipt-container { max-width:680px; margin:auto; background:#fff; border:none; border-radius:10px; padding:30px; box-shadow:0 8px 20px rgba(0,0,0,0.08); }
    h2 { margin:0 0 25px; color:var(--green-dark); text-transform:uppercase; letter-spacing:1.5px; font-size:28px; text-align:center; font-family:var(--font-heading); font-weight:700; }
    h3 { text-align:center; margin-top:-10px; color:var(--green-base); font-family:var(--font-heading); font-size:16px; font-weight:600; letter-spacing:1px; }
    .data-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px 30px; }
    .data-item { display:flex; flex-direction:column; padding:8px 0; border-bottom:1px dashed #ddd; }
    .data-item:last-of-type, .data-grid > div:nth-last-child(2):nth-child(odd) { border-bottom:none; }
    .data-item strong { color:var(--green-base); margin-bottom:6px; font-size:13px; font-family:var(--font-heading); font-weight:600; }
    .data-item span { font-size:15px; color:var(--text-color); }
    .data-item small { font-size:12px; color:#777; }
    .line-separator { border-top:1.5px solid var(--green-base); margin:30px 0; }
    .total-section { display:flex; justify-content:flex-end; align-items:center; padding-top:10px; }
    .total { font-size:22px; color:var(--green-dark); border-top:1px dashed #ccc; padding-top:10px; font-family:var(--font-heading); font-weight:700; }
    .qr-container { text-align:center; margin-top:35px; padding:15px; background-color:#f6faf7; border-radius:8px; }
    .qr-label { margin:5px 0; font-size:13px; color:#666; font-family:var(--font-body); }
    @media (max-width:600px){ body{padding:15px;} .receipt-container{padding:20px;} .data-grid{grid-template-columns:1fr; gap:10px;} h2{font-size:24px;} .total{font-size:18px;} }
    @media print { body{background:none; padding:0; margin:0;} .receipt-container{box-shadow:none; border:1px solid #ccc; border-radius:0; margin:1cm auto; max-width:initial;} }
  </style>
</head>
<body>
  <div class="receipt-container">
    <h2>{{ mb_strtoupper(__('m_bookings.receipt.title'), 'UTF-8') }}</h2>
    <h3>{{ config('app.name', __('m_bookings.receipt.company')) }}</h3>

    @php
      use Carbon\Carbon;
      Carbon::setLocale(app()->getLocale());

      $tour   = $booking->tour;
      $detail = $booking->detail;
      $adultsQty   = (int) $detail->adults_quantity;
      $kidsQty   = (int) $detail->kids_quantity;
      $adultPrice = $tour->adult_price ?? 0;
      $kidPrice = $tour->kid_price ?? 0;

      $hotel  = $detail->is_other_hotel ? $detail->other_hotel_name : (optional($detail->hotel)->name ?? '—');

      // Meeting Point (name only)
      $meetingPointName  = $detail->meeting_point_name ?? optional($detail->meetingPoint)->name ?? '—';

      $schedule = $detail->schedule
        ? Carbon::parse($detail->schedule->start_time)->isoFormat('LT') . ' — ' . Carbon::parse($detail->schedule->end_time)->isoFormat('LT')
        : __('m_bookings.receipt.no_schedule');

      $bookingDate = Carbon::parse($booking->booking_date)->isoFormat('L');
      $tourDate    = Carbon::parse($detail->tour_date)->isoFormat('L');

      // Base subtotal and promo
      $subtotal = ($adultPrice * $adultsQty) + ($kidPrice * $kidsQty);

      // Get promo from direct relation or redemption
      $promo = $booking->promoCode ?? optional($booking->redemption)->promoCode;

      $operation    = $promo ? ($promo->operation === 'add' ? 'add' : 'subtract') : null;
      $delta = 0.0;
      if ($promo) {
          if ($promo->discount_percent) {
              $delta = round($subtotal * ((float)$promo->discount_percent / 100), 2);
          } elseif ($promo->discount_amount) {
              $delta = (float) $promo->discount_amount;
          }
      }

      // Dynamic label
      $labelDiscount = __('m_bookings.receipt.discount');
      $labelSurcharge = __('m_bookings.receipt.surcharge');
      $adjustLabel = $operation === 'add' ? $labelSurcharge : $labelDiscount;

      // Translated status
      $statusTranslated = __('m_bookings.bookings.statuses.' . strtolower((string)$booking->status));
    @endphp

    <div class="data-grid">
      <div class="data-item"><strong>{{ __('m_bookings.receipt.code') }}</strong><span>{{ $booking->booking_reference }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.client') }}</strong><span>{{ optional($booking->user)->full_name }}</span><small>({{ optional($booking->user)->email }})</small></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.tour') }}</strong><span>{{ $tour->name }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.booking_date') }}</strong><span>{{ $bookingDate }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.tour_date') }}</strong><span>{{ $tourDate }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.schedule') }}</strong><span>{{ $schedule }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.hotel') }}</strong><span>{{ $hotel }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.meeting_point') }}</strong><span>{{ $meetingPointName }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.status') }}</strong><span>{{ $statusTranslated }}</span></div>
    </div>

    <div class="line-separator"></div>

    <div class="data-grid">
      <div class="data-item"><strong>{{ __('m_bookings.receipt.adults_x', ['count' => $adultsQty]) }}</strong><span>${{ number_format($adultPrice * $adultsQty, 2) }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.kids_x', ['count' => $kidsQty]) }}</strong><span>${{ number_format($kidPrice * $kidsQty, 2) }}</span></div>
      <div class="data-item"><strong>{{ __('m_bookings.receipt.people') }}</strong><span>{{ $adultsQty + $kidsQty }}</span></div>
    </div>

    <div class="total-section">
      <div style="text-align:right;">
        <div><strong>{{ __('m_bookings.receipt.subtotal') }}:</strong> ${{ number_format($subtotal, 2) }}</div>

        @if($promo && $delta > 0)
          <div style="color: {{ $operation === 'add' ? '#b45309' : 'green' }};">
            <strong>{{ $adjustLabel }} ({{ $promo->code }}):</strong>
            {{ $operation === 'add' ? '+' : '-' }}${{ number_format($delta, 2) }}
          </div>
        @endif

        <div class="total mt-2">{{ __('m_bookings.receipt.total') }}: ${{ number_format($booking->total, 2) }}</div>
      </div>
    </div>

    <div class="qr-container">
      @php
        $data = urlencode($booking->booking_reference);
        $urlQr = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={$data}";
        $png = @file_get_contents($urlQr);
        $base64 = $png ? base64_encode($png) : null;
      @endphp
      @if($base64)
        <img src="data:image/png;base64,{{ $base64 }}" alt="{{ __('m_bookings.receipt.qr_alt') }}" style="width:120px; height:120px;">
      @endif
      <p class="qr-label">{{ __('m_bookings.receipt.qr_scan') }}</p>
      <p class="qr-label">{{ __('m_bookings.receipt.thanks', ['company' => config('app.name', __('m_bookings.receipt.company'))]) }}</p>
    </div>
  </div>
</body>
</html>
