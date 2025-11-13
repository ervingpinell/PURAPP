{{-- resources/views/admin/bookings/receipt.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <title>{{ __('m_bookings.receipt.title') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root { --green-dark:#1A5229; --green-base:#2E8B57; --gray-light:#f0f2f5; --text-color:#333; --font-heading:'Montserrat',sans-serif; --font-body:'Lora',serif; }
    body { font-family:var(--font-body); font-size:13px; background:var(--gray-light); margin:0; padding:20px; color:var(--text-color); line-height:1.45; }
    .receipt-container { max-width:660px; margin:auto; background:#fff; border:none; border-radius:10px; padding:22px 25px; box-shadow:0 6px 15px rgba(0,0,0,0.07); }
    h2 { margin:0 0 18px; color:var(--green-dark); text-transform:uppercase; letter-spacing:1px; font-size:25px; text-align:center; font-family:var(--font-heading); font-weight:700; }
    h3 { text-align:center; margin-top:-8px; color:var(--green-base); font-family:var(--font-heading); font-size:15px; font-weight:600; letter-spacing:0.8px; }
    .data-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px 25px; }
    .data-item { display:flex; flex-direction:column; padding:6px 0; border-bottom:1px dashed #ddd; }
    .data-item:last-of-type, .data-grid > div:nth-last-child(2):nth-child(odd) { border-bottom:none; }
    .data-item strong { color:var(--green-base); margin-bottom:5px; font-size:12.5px; font-family:var(--font-heading); font-weight:600; }
    .data-item span { font-size:14px; color:var(--text-color); }
    .data-item small { font-size:11.5px; color:#777; }
    .line-separator { border-top:1px solid var(--green-base); margin:22px 0; }
    .category-breakdown { background:#f9fdf9; border-radius:6px; padding:12px; margin:10px 0; }
    .category-item { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px dashed #e0e0e0; }
    .category-item:last-child { border-bottom:none; }
    .category-item .category-name { font-family:var(--font-heading); font-weight:600; color:var(--green-dark); font-size:13px; }
    .category-item .category-qty { color:#666; font-size:12px; margin-left:8px; }
    .category-item .category-price { font-weight:600; color:var(--text-color); }
    .total-section { display:flex; justify-content:flex-end; align-items:center; padding-top:8px; }
    .total { font-size:20px; color:var(--green-dark); border-top:1px dashed #ccc; padding-top:8px; font-family:var(--font-heading); font-weight:700; }
    .qr-container { text-align:center; margin-top:25px; padding:12px; background-color:#f6faf7; border-radius:8px; }
    .qr-label { margin:4px 0; font-size:12.5px; color:#666; font-family:var(--font-body); }
    @media (max-width:600px){ body{padding:15px;} .receipt-container{padding:18px;} .data-grid{grid-template-columns:1fr; gap:10px;} h2{font-size:22px;} .total{font-size:17px;} }
    @media print { body{background:none; padding:0; margin:0;} .receipt-container{box-shadow:none; border:1px solid #ccc; border-radius:0; margin:0.5cm auto; max-width:95%; padding:15px 20px;} }
  </style>
</head>
<body>
  <div class="receipt-container">
    <h2>{{ mb_strtoupper(__('m_bookings.receipt.title'), 'UTF-8') }}</h2>
    <h3>{{ config('app.name', __('m_bookings.receipt.company')) }}</h3>

    @php
      use Carbon\Carbon;
      Carbon::setLocale(app()->getLocale());

      $EM      = '—';
      $tour    = $booking->tour;
      $detail  = $booking->detail;

      // ===== Mapa de nombres por ID (opcional) =====
      $CAT_NAMES = $categoryNamesById ?? [];

      // ===== Categorías dinámicas =====
      $categoriesData = [];
      $subtotal = 0.0;
      $totalPersons = 0;

      if (!empty($detail->categories)) {
        if (is_string($detail->categories)) {
          try { $categoriesData = json_decode($detail->categories, true) ?: []; } catch (\Throwable $e) {
            \Log::error('Error decoding categories JSON in receipt', ['booking_id' => $booking->booking_id, 'error' => $e->getMessage()]);
          }
        } elseif (is_array($detail->categories)) {
          $categoriesData = $detail->categories;
        }
      }

      $resolveCatName = function(array $cat, $id = null) use ($CAT_NAMES) {
        $name = $cat['i18n_name'] ?? $cat['name'] ?? $cat['translation_name'] ?? $cat['category_name'] ?? null;
        if (!$name && $id && isset($CAT_NAMES[$id])) $name = $CAT_NAMES[$id];
        return $name ?: ('Category' . ($id ? " #{$id}" : ''));
      };

      $categories = [];
      if (!empty($categoriesData)) {
        // Array de objetos
        if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
          foreach ($categoriesData as $cat) {
            $qty   = (int)($cat['quantity'] ?? 0);
            $price = (float)($cat['price'] ?? 0);
            $cid   = $cat['category_id'] ?? $cat['id'] ?? null;
            $name  = $resolveCatName($cat, $cid);
            $categories[] = ['name'=>$name,'quantity'=>$qty,'price'=>$price,'total'=>$qty*$price];
            $subtotal     += $qty * $price; $totalPersons += $qty;
          }
        } else {
          // Array asociativo id => {...}
          foreach ($categoriesData as $catId => $cat) {
            $qty   = (int)($cat['quantity'] ?? 0);
            $price = (float)($cat['price'] ?? 0);
            $name  = $resolveCatName($cat, $catId);
            $categories[] = ['name'=>$name,'quantity'=>$qty,'price'=>$price,'total'=>$qty*$price];
            $subtotal     += $qty * $price; $totalPersons += $qty;
          }
        }
      }

      // Fallback legacy
      if (empty($categories)) {
        $adultsQty  = (int)($detail->adults_quantity ?? 0);
        $kidsQty    = (int)($detail->kids_quantity ?? 0);
        $adultPrice = (float)($detail->adult_price ?? $tour->adult_price ?? 0);
        $kidPrice   = (float)($detail->kid_price ?? $tour->kid_price ?? 0);

        if ($adultsQty > 0) {
          $nameA = __('m_bookings.categories.adult', [], false) ?: 'Adults';
          $categories[] = ['name'=>$nameA,'quantity'=>$adultsQty,'price'=>$adultPrice,'total'=>$adultsQty*$adultPrice];
          $subtotal += $adultsQty * $adultPrice; $totalPersons += $adultsQty;
        }
        if ($kidsQty > 0) {
          $nameK = __('m_bookings.categories.kid', [], false) ?: 'Kids';
          $categories[] = ['name'=>$nameK,'quantity'=>$kidsQty,'price'=>$kidPrice,'total'=>$kidsQty*$kidPrice];
          $subtotal += $kidsQty * $kidPrice; $totalPersons += $kidsQty;
        }
      }

      // ===== Hotel o Meeting Point (exclusivo) =====
      $hasHotel        = !empty($detail->hotel_id) || !empty($detail->other_hotel_name);
      $hasMeetingPoint = !empty($detail->meeting_point_id) || !empty($detail->meeting_point_name);

      $hotel = null; $meetingPointName = null;
      if ($hasHotel) {
        $hotel = $detail->is_other_hotel ? ($detail->other_hotel_name ?: $EM) : (optional($detail->hotel)->name ?? $EM);
      } elseif ($hasMeetingPoint) {
        $meetingPointName = $detail->meeting_point_name ?? optional($detail->meetingPoint)->name ?? $EM;
      }

      // ===== Otros datos =====
      $schedule = $detail->schedule
        ? Carbon::parse($detail->schedule->start_time)->isoFormat('LT') . ' – ' . Carbon::parse($detail->schedule->end_time)->isoFormat('LT')
        : __('m_bookings.receipt.no_schedule');

      // Pickup time
      $pickupTime = $detail->pickup_time
        ? Carbon::parse($detail->pickup_time)->isoFormat('LT')
        : $EM;

      $bookingDate = $booking->booking_date ? Carbon::parse($booking->booking_date)->isoFormat('L') : $EM;
      $tourDate    = $detail->tour_date     ? Carbon::parse($detail->tour_date)->isoFormat('L')     : $EM;

      // ===== NOTAS =====
      $notes = $booking->notes ?? $booking->special_requests ?? null;

      // ===== PROMO (preferir snapshots de redención) =====
      $redemption  = $booking->redemption;
      $promo       = $booking->promoCode ?? optional($redemption)->promoCode;

      $couponCode  = $EM;
      $opApplied   = null; // 'add' | 'subtract'
      $appliedAmt  = 0.0;

      if ($redemption) {
        $couponCode = $redemption->code_snapshot ?? optional($redemption->promoCode)->code ?? $EM;
        $opApplied  = $redemption->operation_snapshot ?: null;
        $appliedAmt = (float)($redemption->applied_amount ?? 0);
      } elseif ($promo) {
        $couponCode = $promo->code ?? $EM;
        $opApplied  = $promo->operation === 'add' ? 'add' : 'subtract';
        if (!empty($promo->discount_percent)) {
          $appliedAmt = round($subtotal * ((float)$promo->discount_percent / 100), 2);
        } elseif (!empty($promo->discount_amount)) {
          $appliedAmt = (float)$promo->discount_amount;
        }
      }

      $labelDiscount  = __('m_bookings.receipt.discount');
      $labelSurcharge = __('m_bookings.receipt.surcharge');
      $adjustLabel    = $opApplied === 'add' ? $labelSurcharge : $labelDiscount;

      $statusTranslated = __('m_bookings.bookings.statuses.' . strtolower((string)$booking->status));
    @endphp

    <div class="data-grid">
      <div class="data-item">
        <strong>{{ __('m_bookings.receipt.code') }}:</strong>
        <span>{{ $booking->booking_reference ?: $EM }}</span>
      </div>

      <div class="data-item">
        <strong>{{ __('m_bookings.receipt.client') }}:</strong>
        <span>{{ optional($booking->user)->full_name ?: $EM }}</span>
        <small>({{ optional($booking->user)->email ?: $EM }})</small>
      </div>

      <div class="data-item">
        <strong>{{ __('m_bookings.receipt.tour') }}:</strong>
        <span>{{ $tour->name ?? $EM }}</span>
      </div>

      <div class="data-item">
        <strong>{{ __('m_bookings.receipt.booking_date') }}:</strong>
        <span>{{ $bookingDate }}</span>
      </div>

      <div class="data-item">
        <strong>{{ __('m_bookings.receipt.tour_date') }}:</strong>
        <span>{{ $tourDate }}</span>
      </div>

      <div class="data-item">
        <strong>{{ __('m_bookings.receipt.schedule') }}:</strong>
        <span>{{ $schedule }}</span>
      </div>

      <div class="data-item">
        <strong>{{ __('m_bookings.bookings.fields.pickup_time') }}:</strong>
        <span>{{ $pickupTime }}</span>
      </div>

      @if($hasHotel && $hotel)
        <div class="data-item">
          <strong>{{ __('m_bookings.receipt.hotel') }}:</strong>
          <span>{{ $hotel }}</span>
        </div>
      @endif

      @if(!$hasHotel && $hasMeetingPoint && $meetingPointName)
        <div class="data-item">
          <strong>{{ __('m_bookings.receipt.meeting_point') }}:</strong>
          <span>{{ $meetingPointName }}</span>
        </div>
      @endif

      @if(!empty($notes))
        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.notes') }}:</strong>
          <span>{{ $notes }}</span>
        </div>
      @endif

      <div class="data-item">
        <strong>{{ __('m_bookings.receipt.status') }}:</strong>
        <span>{{ $statusTranslated }}</span>
      </div>
    </div>

    <div class="line-separator"></div>

    {{-- ===== Desglose de categorías ===== --}}
    @if(!empty($categories))
      <div class="category-breakdown">
        @foreach($categories as $cat)
          <div class="category-item">
            <div>
              <span class="category-name">{{ $cat['name'] }}</span>
              <span class="category-qty">({{ $cat['quantity'] }} × ${{ number_format($cat['price'], 2) }})</span>
            </div>
            <div class="category-price">${{ number_format($cat['total'], 2) }}</div>
          </div>
        @endforeach

        <div class="category-item" style="margin-top:8px; padding-top:8px; border-top:2px solid var(--green-base);">
          <div>
            <span class="category-name">{{ __('m_bookings.receipt.people') }}:</span>
            <span class="category-qty">
              @php
                // Pluralización segura
                $personKey = 'm_bookings.receipt.persons'; // usa esta clave si la tienes
                $pluralTxt = trans_choice($personKey, $totalPersons, ['count' => $totalPersons], app()->getLocale());
                if ($pluralTxt === $personKey) { $pluralTxt = $totalPersons === 1 ? 'person' : 'people'; }
              @endphp
              {{ $totalPersons }} {{ $pluralTxt }}
            </span>
          </div>
        </div>
      </div>
    @endif

    <div class="total-section">
      <div style="text-align:right;">
        <div><strong>{{ __('m_bookings.receipt.subtotal') }}:</strong> ${{ number_format($subtotal, 2) }}</div>

        @if($opApplied && $appliedAmt > 0)
          <div style="color: {{ $opApplied === 'add' ? '#b45309' : 'green' }};">
            <strong>{{ $adjustLabel }} ({{ $couponCode }}):</strong>
            {{ $opApplied === 'add' ? '+' : '-' }}${{ number_format($appliedAmt, 2) }}
          </div>
        @endif

        <div class="total mt-2">{{ __('m_bookings.receipt.total') }}: ${{ number_format((float)($booking->total ?? 0), 2) }}</div>
      </div>
    </div>

    {{-- ===== QR opcional y protegido ===== --}}
    <div class="qr-container">
      @php
        $base64 = null;
        if (config('reports.qr_enabled', true)) {
          try {
            $data  = urlencode($booking->booking_reference ?? '');
            $urlQr = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={$data}";
            $png   = @file_get_contents($urlQr);
            if ($png) { $base64 = base64_encode($png); }
          } catch (\Throwable $e) {
            \Log::warning('QR fetch failed in receipt', ['booking_id' => $booking->booking_id, 'error' => $e->getMessage()]);
          }
        }
      @endphp

      @if($base64)
        <img src="data:image/png;base64,{{ $base64 }}" alt="{{ __('m_bookings.receipt.qr_alt') }}" style="width:100px; height:100px;">
      @endif

      <p class="qr-label">{{ __('m_bookings.receipt.qr_scan') }}</p>
      <p class="qr-label">{{ __('m_bookings.receipt.thanks', ['company' => config('app.name', __('m_bookings.receipt.company'))]) }}</p>
    </div>
  </div>
</body>
</html>
