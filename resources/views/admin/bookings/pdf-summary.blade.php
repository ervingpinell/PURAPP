{{-- resources/views/admin/bookings/pdf-summary.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="UTF-8">
  <title>{{ __('m_bookings.reports.pdf_title') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <style>
    @page {
      margin: 18mm 15mm;
    }

    :root {
      --green-dark: #1A5229;
      --green-base: #2E8B57;
      --gray-light: #f0f2f5;
      --text-color: #333;
      --font-heading: 'Montserrat', sans-serif;
      --font-body: 'Lora', serif;
    }

    body {
      font-family: var(--font-body);
      font-size: 14px;
      background: var(--gray-light);
      color: var(--text-color);
      margin: 0;
      padding: 40px 0;
      line-height: 1.6;
    }

    h2 {
      text-align: center;
      color: var(--green-dark);
      margin: 0 0 20px;
      font-family: var(--font-heading);
      font-size: 28px;
      letter-spacing: 1.2px;
      text-transform: uppercase;
    }

    .report-container {
      max-width: 850px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .booking-page {
      background: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      padding: 22px 20px;
      page-break-inside: avoid;
    }

    .booking-section {
      border: 1px solid #e7e7e7;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 16px;
    }

    .section-title {
      font-weight: 700;
      color: var(--green-base);
      margin-bottom: 12px;
      font-family: var(--font-heading);
      font-size: 17px;
    }

    .data-item {
      margin-bottom: 7px;
      display: flex;
      align-items: baseline;
    }

    .data-item strong {
      color: var(--green-dark);
      min-width: 140px;
      display: inline-block;
      font-family: var(--font-heading);
      font-weight: 600;
      font-size: 13px;
    }

    .data-item strong::after {
      content: ':';
      margin-left: 2px;
    }

    .data-item span,
    .data-item small {
      font-family: var(--font-body);
      font-size: 14px;
      color: var(--text-color);
      margin-left: 8px;
    }

    .data-item small {
      font-size: 12px;
      color: #777;
    }

    .line-separator {
      border-top: 1px dashed #c0c0c0;
      margin: 12px 0;
    }

    .total {
      font-weight: 700;
      color: var(--green-dark);
      text-align: right;
      font-size: 18px;
      font-family: var(--font-heading);
      margin-top: 10px;
    }

    .category-breakdown {
      background: #f9fdf9;
      border-radius: 6px;
      padding: 10px;
      margin: 8px 0;
    }

    .category-item {
      display: flex;
      justify-content: space-between;
      padding: 4px 0;
      font-size: 13px;
    }

    .summary-global {
      background: #eaf5ed;
      border: 1px solid var(--green-base);
      border-radius: 10px;
      padding: 18px;
      margin-top: 18px;
      page-break-inside: avoid;
    }

    .summary-global .data-item {
      font-size: 15px;
      font-family: var(--font-heading);
      font-weight: 600;
      color: var(--green-dark);
    }

    .summary-global .data-item strong {
      min-width: 150px;
    }

    .page-break {
      page-break-after: always;
    }

    @media print {
      body {
        background: none;
        padding: 0;
        margin: 0;
      }

      .report-container {
        max-width: initial;
        padding: 0;
      }

      .booking-page {
        border: 1px solid #cfcfcf;
        border-radius: 0;
        padding: 14mm 12mm;
        margin: 0;
      }

      .booking-section {
        border: 1px solid #cfcfcf;
      }

      h2 {
        font-size: 22px;
        margin-bottom: 14px;
      }

      .section-title {
        font-size: 15px;
      }

      .total {
        font-size: 16px;
      }

      .summary-global {
        border: 1px solid #cfcfcf;
        background: #f6fbf7;
      }
    }
  </style>
</head>

<body>
  <div class="report-container">
    <h2>{{ __('m_bookings.reports.general_report_title') }}</h2>

    @php
    use App\Models\CustomerCategory;

    // Mapa opcional enviado por el controlador: [category_id => translated_name]
    $CAT_NAMES = $categoryNamesById ?? [];
    $EM = '—';
    $grandTotalPersons = 0;

    // Cache local para resolver nombres sin repetir queries
    $CAT_CACHE = [];

    /**
    * Resuelve el nombre de categoría:
    * 1) Usa el nombre que venga en el snapshot (i18n_name/name/translation_name/category_name)
    * 2) Usa el mapa $CAT_NAMES pasado desde el controlador
    * 3) Fallback: consulta CustomerCategory + translations (cache local)
    * 4) Último recurso: "Category #ID"
    */
    $resolveName = function(array $catArr = [], $catId = null) use (&$CAT_NAMES, &$CAT_CACHE) {
    $name = $catArr['i18n_name']
    ?? $catArr['name']
    ?? $catArr['translation_name']
    ?? $catArr['category_name']
    ?? null;

    if (!$name && $catId && isset($CAT_NAMES[$catId])) {
    $name = $CAT_NAMES[$catId];
    }

    if (!$name && $catId) {
    if (!array_key_exists($catId, $CAT_CACHE)) {
    $cat = CustomerCategory::with('translations')->find($catId);
    $CAT_CACHE[$catId] = $cat
    ? (method_exists($cat, 'getTranslatedName')
    ? $cat->getTranslatedName(app()->getLocale())
    : ($cat->translations->firstWhere('locale', app()->getLocale())->name
    ?? ($cat->translations->first()->name ?? \Illuminate\Support\Str::of((string)$cat->slug)->replace(['_','-'],' ')->title())))
    : null;
    }
    $name = $CAT_CACHE[$catId] ?: $name;
    }

    return $name ?: ('Category' . ($catId ? " #{$catId}" : ''));
    };
    @endphp

    @foreach($bookings as $booking)
    @php
    $product = $booking->product;
    $detail = $booking->detail;

    // ====== CATEGORÍAS (con nombre traducido) ======
    $categoriesData = [];
    if (!empty($detail->categories)) {
    if (is_string($detail->categories)) {
    try { $categoriesData = json_decode($detail->categories, true) ?: []; } catch (\Throwable $e) {}
    } elseif (is_array($detail->categories)) {
    $categoriesData = $detail->categories;
    }
    }

    $categories = [];
    $subtotal = 0.0;
    $totalPersons = 0;

    if (!empty($categoriesData)) {
    // Caso: array de objetos
    if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
    foreach ($categoriesData as $cat) {
    $qty = (int)($cat['quantity'] ?? 0);
    $price = (float)($cat['price'] ?? 0);
    $cid = $cat['category_id'] ?? $cat['id'] ?? null;
    $name = $resolveName($cat, $cid);

    $categories[] = [
    'name' => $name,
    'quantity' => $qty,
    'price' => $price,
    'total' => $qty * $price,
    ];
    $subtotal += $qty * $price;
    $totalPersons += $qty;
    }
    } else {
    // Caso: array asociativo id => {...}
    foreach ($categoriesData as $catId => $cat) {
    $qty = (int)($cat['quantity'] ?? 0);
    $price = (float)($cat['price'] ?? 0);
    $name = $resolveName($cat, $catId);

    $categories[] = [
    'name' => $name,
    'quantity' => $qty,
    'price' => $price,
    'total' => $qty * $price,
    ];
    $subtotal += $qty * $price;
    $totalPersons += $qty;
    }
    }
    }

    // Fallback legacy (usar traducciones de adults/kids)
    if (empty($categories)) {
    $adultsQty = (int)($detail->adults_quantity ?? 0);
    $kidsQty = (int)($detail->kids_quantity ?? 0);
    $adultPrice = (float)($product->adult_price ?? 0);
    $kidPrice = (float)($product->kid_price ?? 0);

    if ($adultsQty > 0) {
    $nameA = __('m_bookings.categories.adult', [], false) ?: 'Adults';
    $categories[] = ['name' => $nameA, 'quantity' => $adultsQty, 'price' => $adultPrice, 'total' => $adultsQty * $adultPrice];
    $subtotal += $adultsQty * $adultPrice;
    $totalPersons += $adultsQty;
    }
    if ($kidsQty > 0) {
    $nameK = __('m_bookings.categories.kid', [], false) ?: 'Kids';
    $categories[] = ['name' => $nameK, 'quantity' => $kidsQty, 'price' => $kidPrice, 'total' => $kidsQty * $kidPrice];
    $subtotal += $kidsQty * $kidPrice;
    $totalPersons += $kidsQty;
    }
    }

    $grandTotalPersons += $totalPersons;

    // ====== Hotel / Meeting Point ======
    $hasHotel = !empty($detail->hotel_id) || !empty($detail->other_hotel_name);
    $hotel = $hasHotel
    ? ($detail->is_other_hotel ? ($detail->other_hotel_name ?: $EM) : (optional($detail->hotel)->name ?? $EM))
    : null;

    $meetingPoint = (!$hasHotel && !empty($detail->meeting_point_id))
    ? ($detail->meeting_point_name ?? optional($detail->meetingPoint)->name_localized ?? $EM)
    : null;

    $schedule = $detail->schedule
    ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') . ' — ' . \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A')
    : __('m_bookings.bookings.messages.no_schedules');

    // ====== Pickup time ======
    $pickupTime = $detail->pickup_time
    ? \Carbon\Carbon::parse($detail->pickup_time)->format('g:i A')
    : $EM;

    // ====== PROMO (preferir snapshots de redención) ======
    $redemption = $booking->redemption;
    $promo = $booking->promoCode ?? optional($redemption)->promoCode;
    $couponCode = $EM;
    $opApplied = null; // 'add' | 'subtract'
    $appliedAmt = 0.0;

    if ($redemption) {
    $couponCode = $redemption->code_snapshot ?? optional($redemption->promoCode)->code ?? $EM;
    $opApplied = $redemption->operation_snapshot ?: null;
    $appliedAmt = (float)($redemption->applied_amount ?? 0);
    } elseif ($promo) {
    $couponCode = $promo->code ?? $EM;
    $opApplied = $promo->operation === 'add' ? 'add' : 'subtract';
    // Estimación (solo si no hay redención)
    if (!empty($promo->discount_percent)) {
    $appliedAmt = round($subtotal * ((float)$promo->discount_percent / 100), 2);
    } elseif (!empty($promo->discount_amount)) {
    $appliedAmt = (float)$promo->discount_amount;
    }
    }

    $labelDiscount = __('m_bookings.reports.discount', [], false) ?: 'Discount';
    $labelSurcharge = __('m_bookings.reports.surcharge', [], false) ?: 'Surcharge';
    $adjustLabel = $opApplied === 'add' ? $labelSurcharge : $labelDiscount;

    // ====== Otros valores seguros ======
    $ref = $booking->booking_reference ?: $EM;
    $customer = optional($booking->user)->full_name ?: $EM;
    $customerEm = optional($booking->user)->email ?: $EM;
    $productName = $product ? (string)$product->name : $EM;
    $bkDate = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('m/d/Y') : $EM;
    $trDate = $detail->tour_date ? \Carbon\Carbon::parse($detail->tour_date)->format('m/d/Y') : $EM;

    $statusKey = 'm_bookings.bookings.statuses.' . ($booking->status ?? 'pending');
    $statusTr = __($statusKey);
    if ($statusTr === $statusKey) { $statusTr = ucfirst((string)($booking->status ?? 'pending')); }

    // ====== NOTAS ======
    $notes = $booking->notes ?? $booking->special_requests ?? null;
    @endphp

    <div class="booking-page">
      <div class="booking-section">
        <div class="section-title">
          {{ __('m_bookings.bookings.fields.reference') }}: {{ $ref }}
        </div>

        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.customer') }}</strong>
          <span>{{ $customer }}</span>
          <small>({{ $customerEm }})</small>
        </div>

        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.tour') }}</strong>
          <span>{{ $productName }}</span>
        </div>

        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.booking_date') }}</strong>
          <span>{{ $bkDate }}</span>
        </div>

        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.tour_date') }}</strong>
          <span>{{ $trDate }}</span>
        </div>

        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.schedule') }}</strong>
          <span>{{ $schedule }}</span>
        </div>

        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.pickup_time') }}</strong>
          <span>{{ $pickupTime }}</span>
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

        @if(!empty($notes))
        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.notes') }}</strong>
          <span>{{ $notes }}</span>
        </div>
        @endif

        <div class="data-item">
          <strong>{{ __('m_bookings.bookings.fields.status') }}</strong>
          <span>{{ $statusTr }}</span>
        </div>

        <div class="line-separator"></div>

        {{-- Desglose por categoría (con nombre traducido) --}}
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

        @if ($opApplied && $appliedAmt > 0)
        <div class="data-item">
          <strong>{{ $adjustLabel }}</strong>
          <span style="color: {{ $opApplied === 'add' ? '#b45309' : 'green' }};">
            {{ $opApplied === 'add' ? '+' : '−' }}${{ number_format($appliedAmt, 2) }}
            ({{ $couponCode }})
          </span>
        </div>
        <div class="data-item">
          <strong>{{ __('m_bookings.reports.original_price') }}</strong>
          <span style="text-decoration: line-through; color: #999;">
            ${{ number_format($subtotal, 2) }}
          </span>
        </div>
        @endif

        <div class="total">{{ __('m_bookings.bookings.fields.total') }}: ${{ number_format((float)($booking->total ?? 0), 2) }}</div>
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