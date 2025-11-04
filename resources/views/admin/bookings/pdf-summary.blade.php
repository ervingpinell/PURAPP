{{-- resources/views/admin/bookings/pdf-summary.blade.php --}}

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <title>{{ __('m_bookings.reports.pdf_title') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <style>
    @page { margin: 18mm 15mm; }

    :root{
      --green-dark:#1A5229;
      --green-base:#2E8B57;
      --gray-light:#f0f2f5;
      --text-color:#333;
      --font-heading:'Montserrat',sans-serif;
      --font-body:'Lora',serif;
    }

    body{
      font-family:var(--font-body);
      font-size:14px;
      background:var(--gray-light);
      color:var(--text-color);
      margin:0;
      padding:40px 0;
      line-height:1.6;
    }

    h2{
      text-align:center;
      color:var(--green-dark);
      margin:0 0 20px;
      font-family:var(--font-heading);
      font-size:28px;
      letter-spacing:1.2px;
      text-transform:uppercase;
    }

    .report-container{
      max-width:850px;
      margin:0 auto;
      padding:0 20px;
    }

    .booking-page{
      background:#fff;
      border:1px solid #e0e0e0;
      border-radius:10px;
      padding:22px 20px;
      page-break-inside: avoid;
    }

    .booking-section{
      border:1px solid #e7e7e7;
      border-radius:8px;
      padding:16px;
      margin-bottom:16px;
    }

    .section-title{
      font-weight:700;
      color:var(--green-base);
      margin-bottom:12px;
      font-family:var(--font-heading);
      font-size:17px;
    }

    .data-item{
      margin-bottom:7px;
      display:flex;
      align-items:baseline;
    }

    .data-item strong{
      color:var(--green-dark);
      min-width:140px;
      display:inline-block;
      font-family:var(--font-heading);
      font-weight:600;
      font-size:13px;
    }

    .data-item strong::after {
      content: ':';
      margin-left: 2px;
    }

    .data-item span,
    .data-item small{
      font-family:var(--font-body);
      font-size:14px;
      color:var(--text-color);
      margin-left: 8px;
    }

    .data-item small{
      font-size:12px;
      color:#777;
    }

    .line-separator{
      border-top:1px dashed #c0c0c0;
      margin:12px 0;
    }

    .total{
      font-weight:700;
      color:var(--green-dark);
      text-align:right;
      font-size:18px;
      font-family:var(--font-heading);
      margin-top:10px;
    }

    .category-breakdown {
      background:#f9fdf9;
      border-radius:6px;
      padding:10px;
      margin:8px 0;
    }

    .category-item {
      display:flex;
      justify-content:space-between;
      padding:4px 0;
      font-size:13px;
    }

    .summary-global{
      background:#eaf5ed;
      border:1px solid var(--green-base);
      border-radius:10px;
      padding:18px;
      margin-top:18px;
      page-break-inside: avoid;
    }

    .summary-global .data-item{
      font-size:15px;
      font-family:var(--font-heading);
      font-weight:600;
      color:var(--green-dark);
    }

    .summary-global .data-item strong{
      min-width:150px;
    }

    .page-break {
      page-break-after: always;
    }

    @media print{
      body{ background:none; padding:0; margin:0; }
      .report-container{ max-width:initial; padding:0; }
      .booking-page{
        border:1px solid #cfcfcf;
        border-radius:0;
        padding:14mm 12mm;
        margin:0;
      }
      .booking-section{ border:1px solid #cfcfcf; }
      h2{ font-size:22px; margin-bottom:14px; }
      .section-title{ font-size:15px; }
      .total{ font-size:16px; }
      .summary-global{ border:1px solid #cfcfcf; background:#f6fbf7; }
    }
  </style>
</head>
<body>
  <div class="report-container">
    <h2>{{ __('m_bookings.reports.general_report_title') }}</h2>

    @php
      $grandTotalPersons = 0;
    @endphp

    @foreach($bookings as $booking)
      @php
        $tour   = $booking->tour;
        $detail = $booking->detail;

        // ========== CATEGORÍAS DINÁMICAS ==========
        $categoriesData = [];
        $subtotal = 0;
        $totalPersons = 0;

        if ($detail->categories && is_string($detail->categories)) {
          try {
            $categoriesData = json_decode($detail->categories, true);
          } catch (\Exception $e) {}
        } elseif (is_array($detail->categories)) {
          $categoriesData = $detail->categories;
        }

        $categories = [];
        if (!empty($categoriesData)) {
          // Array de objetos
          if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
            foreach ($categoriesData as $cat) {
              $qty = (int)($cat['quantity'] ?? 0);
              $price = (float)($cat['price'] ?? 0);
              $name = $cat['name'] ?? $cat['category_name'] ?? 'Category';

              $categories[] = [
                'name' => $name,
                'quantity' => $qty,
                'price' => $price,
                'total' => $qty * $price
              ];

              $subtotal += $qty * $price;
              $totalPersons += $qty;
            }
          }
          // Array asociativo
          else {
            foreach ($categoriesData as $catId => $cat) {
              $qty = (int)($cat['quantity'] ?? 0);
              $price = (float)($cat['price'] ?? 0);
              $name = $cat['name'] ?? $cat['category_name'] ?? "Category #{$catId}";

              $categories[] = [
                'name' => $name,
                'quantity' => $qty,
                'price' => $price,
                'total' => $qty * $price
              ];

              $subtotal += $qty * $price;
              $totalPersons += $qty;
            }
          }
        }

        // Fallback legacy
        if (empty($categories)) {
          $adultsQty = (int)($detail->adults_quantity ?? 0);
          $kidsQty = (int)($detail->kids_quantity ?? 0);
          $adultPrice = $tour->adult_price ?? 0;
          $kidPrice = $tour->kid_price ?? 0;

          if ($adultsQty > 0) {
            $categories[] = ['name' => 'Adults', 'quantity' => $adultsQty, 'price' => $adultPrice, 'total' => $adultsQty * $adultPrice];
            $subtotal += $adultsQty * $adultPrice;
            $totalPersons += $adultsQty;
          }
          if ($kidsQty > 0) {
            $categories[] = ['name' => 'Kids', 'quantity' => $kidsQty, 'price' => $kidPrice, 'total' => $kidsQty * $kidPrice];
            $subtotal += $kidsQty * $kidPrice;
            $totalPersons += $kidsQty;
          }
        }

        $grandTotalPersons += $totalPersons;

        // Hotel o Meeting Point
        $hasHotel = !empty($detail->hotel_id) || !empty($detail->other_hotel_name);
        $hotel = $hasHotel
          ? ($detail->is_other_hotel ? $detail->other_hotel_name : (optional($detail->hotel)->name ?? '—'))
          : null;

        $meetingPoint = !$hasHotel && !empty($detail->meeting_point_id)
          ? ($detail->meeting_point_name ?? optional($detail->meetingPoint)->name ?? '—')
          : null;

        $schedule = $detail->schedule
            ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') . ' — ' . \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A')
            : __('m_bookings.bookings.messages.no_schedules');

        // Promo
        $promo = $booking->promoCode ?? optional($booking->redemption)->promoCode;
        $operation = $promo ? ($promo->operation === 'add' ? 'add' : 'subtract') : null;

        $delta = 0.0;
        if ($promo) {
          if ($promo->discount_percent) {
            $delta = round($subtotal * ((float)$promo->discount_percent / 100), 2);
          } elseif ($promo->discount_amount) {
            $delta = (float) $promo->discount_amount;
          }
        }

        $labelDiscount = __('m_bookings.reports.discount', [], false) ?: 'Discount';
        $labelSurcharge = __('m_bookings.reports.surcharge', [], false) ?: 'Surcharge';
        $adjustLabel = $operation === 'add' ? $labelSurcharge : $labelDiscount;
      @endphp

      <div class="booking-page">
        <div class="booking-section">
          <div class="section-title">
            {{ __('m_bookings.bookings.fields.reference') }}: {{ $booking->booking_reference }}
          </div>

          <div class="data-item">
            <strong>{{ __('m_bookings.bookings.fields.customer') }}</strong>
            <span>{{ optional($booking->user)->full_name }}</span>
            <small>({{ optional($booking->user)->email }})</small>
          </div>

          <div class="data-item">
            <strong>{{ __('m_bookings.bookings.fields.tour') }}</strong>
            <span>{{ $tour->name }}</span>
          </div>

          <div class="data-item">
            <strong>{{ __('m_bookings.bookings.fields.booking_date') }}</strong>
            <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('m/d/Y') }}</span>
          </div>

          <div class="data-item">
            <strong>{{ __('m_bookings.bookings.fields.tour_date') }}</strong>
            <span>{{ \Carbon\Carbon::parse($detail->tour_date)->format('m/d/Y') }}</span>
          </div>

          <div class="data-item">
            <strong>{{ __('m_bookings.bookings.fields.schedule') }}</strong>
            <span>{{ $schedule }}</span>
          </div>

          @if($hotel)
            <div class="data-item">
              <strong>{{ __('m_bookings.bookings.fields.hotel') }}</strong>
              <span>{{ $hotel }}</span>
            </div>
          @endif

          @if($meetingPoint)
            <div class="data-item">
              <strong>{{ __('m_bookings.bookings.fields.meeting_point') }}</strong>
              <span>{{ $meetingPoint }}</span>
            </div>
          @endif

          <div class="data-item">
            <strong>{{ __('m_bookings.bookings.fields.status') }}</strong>
            <span>{{ __('m_bookings.bookings.statuses.' . $booking->status) }}</span>
          </div>

          <div class="line-separator"></div>

          <div class="category-breakdown">
            @foreach($categories as $cat)
              <div class="category-item">
                <span><strong>{{ $cat['name'] }}</strong> ({{ $cat['quantity'] }} × ${{ number_format($cat['price'], 2) }})</span>
                <span>${{ number_format($cat['total'], 2) }}</span>
              </div>
            @endforeach
          </div>

          <div class="data-item">
            <strong>{{ __('m_bookings.reports.people') }}</strong>
            <span>{{ $totalPersons }}</span>
          </div>

          <div class="line-separator"></div>

          <div class="data-item">
            <strong>{{ __('m_bookings.reports.subtotal') }}</strong>
            <span>${{ number_format($subtotal, 2) }}</span>
          </div>

          @if ($promo && $delta > 0)
            <div class="data-item">
              <strong>{{ $adjustLabel }}</strong>
              <span style="color: {{ $operation === 'add' ? '#b45309' : 'green' }};">
                {{ $operation === 'add' ? '+' : '−' }}${{ number_format($delta, 2) }}
                ({{ $promo->code }})
              </span>
            </div>
            <div class="data-item">
              <strong>{{ __('m_bookings.reports.original_price') }}</strong>
              <span style="text-decoration: line-through; color: #999;">
                ${{ number_format($subtotal, 2) }}
              </span>
            </div>
          @endif

          <div class="total">{{ __('m_bookings.bookings.fields.total') }}: ${{ number_format($booking->total, 2) }}</div>
        </div>
      </div>

      @if(!$loop->last)
        <div class="page-break"></div>
      @endif
    @endforeach

    {{-- Resumen global --}}
    <div class="summary-global">
      <div class="data-item">
        <strong>{{ __('m_bookings.reports.total_people') }}</strong>
        <span>{{ $grandTotalPersons }}</span>
      </div>
    </div>
  </div>
</body>
</html>
