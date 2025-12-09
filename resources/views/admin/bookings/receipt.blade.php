{{-- resources/views/admin/bookings/receipt.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('m_bookings.receipt.title') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 13px;
            background: #fff;
            color: #000;
            line-height: 1.5;
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 160px;
            height: auto;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 25px;
            color: #1A5229;
        }

        .info-block {
            margin-bottom: 20px;
        }

        .info-line {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
            border-bottom: 1px dotted #e0e0e0;
        }

        .info-line.highlight {
            font-size: 15px;
            font-weight: 700;
            margin: 10px 0;
            border-bottom: 2px solid #1A5229;
        }

        .label {
            font-weight: 600;
            color: #555;
        }

        .value {
            font-weight: 400;
            color: #000;
            text-align: right;
        }

        .divider {
            border-top: 2px solid #1A5229;
            margin: 15px 0;
        }

        .category-list {
            margin: 12px 0;
        }

        .category-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 12px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 17px;
            font-weight: 700;
            border-top: 3px solid #1A5229;
            margin-top: 12px;
        }

        .qr-section {
            text-align: center;
            margin-top: 25px;
        }

        .qr-section img {
            width: 110px;
            height: 110px;
            margin: 0 auto 10px;
        }

        .barcode-text {
            font-size: 11px;
            letter-spacing: 2px;
            font-weight: 600;
            margin-top: 6px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 18px;
            line-height: 1.6;
        }

        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
            font-size: 10px;
            display: none;
        }

        @page {
            margin: 1cm;
        }

        @media print {
            body {
                padding: 20px;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    @php
    use Carbon\Carbon;

    // ===== DEBUG & LANGUAGE DETECTION =====
    $receiptLocale = 'es'; // Default fallback
    $tourLanguageName = 'Español';
    $debugInfo = [];

    // Debug: Check what we have
    $debugInfo[] = "Detail exists: " . ($booking->detail ? 'YES' : 'NO');
    $debugInfo[] = "tour_language_id: " . ($booking->detail->tour_language_id ?? 'NULL');
    $debugInfo[] = "tourLanguage loaded: " . ($booking->detail && $booking->relationLoaded('detail') && $booking->detail->relationLoaded('tourLanguage') ? 'YES' : 'NO');

    // Try to get language - MAP BY NAME since table doesn't have 'code' field
    if ($booking->detail && $booking->detail->tourLanguage) {
    $tourLang = $booking->detail->tourLanguage;
    $langName = mb_strtolower(trim($tourLang->name ?? ''));
    $debugInfo[] = "TourLanguage found: {$tourLang->name}";

    // Map language name to locale code
    $nameToLocaleMap = [
    // English variations
    'english' => 'en',
    'inglés' => 'en',
    'ingles' => 'en',
    'anglais' => 'en',
    // Spanish variations
    'español' => 'es',
    'espanol' => 'es',
    'spanish' => 'es',
    // French variations
    'français' => 'fr',
    'francais' => 'fr',
    'french' => 'fr',
    // German variations
    'deutsch' => 'de',
    'german' => 'de',
    'alemán' => 'de',
    'aleman' => 'de',
    // Portuguese variations
    'português' => 'pt',
    'portugues' => 'pt',
    'portuguese' => 'pt',
    ];

    if (isset($nameToLocaleMap[$langName])) {
    $receiptLocale = $nameToLocaleMap[$langName];
    $tourLanguageName = $tourLang->name;
    $debugInfo[] = "Locale mapped to: {$receiptLocale}";
    } else {
    $debugInfo[] = "Language name '{$langName}' not mapped - using default es";
    }
    } else {
    $debugInfo[] = "TourLanguage NOT found - using default Spanish";
    }

    // Set locales
    Carbon::setLocale($receiptLocale);
    $originalLocale = app()->getLocale();
    app()->setLocale($receiptLocale);

    $debugInfo[] = "App locale: " . app()->getLocale();
    $debugInfo[] = "Carbon locale: " . Carbon::getLocale();

    $tour = $booking->tour;
    $detail = $booking->detail;

    // Tour name
    $tourName = $tour->name ?? '—';
    if ($tour && method_exists($tour, 'getTranslation')) {
    $tourName = $tour->getTranslation('name', $receiptLocale) ?? $tour->name ?? '—';
    }

    // Categories
    $CAT_NAMES = $categoryNamesById ?? [];
    $categoriesData = [];
    $totalPersons = 0;
    $subtotal = 0.0;

    if (!empty($detail->categories)) {
    if (is_string($detail->categories)) {
    try { $categoriesData = json_decode($detail->categories, true) ?: []; }
    catch (\Throwable $e) {}
    } elseif (is_array($detail->categories)) {
    $categoriesData = $detail->categories;
    }
    }

    $resolveCatName = function(array $cat, $id = null) use ($CAT_NAMES, $receiptLocale) {
    $name = $cat['i18n_name'] ?? $cat['name'] ?? $cat['translation_name'] ?? $cat['category_name'] ?? null;
    if (!$name && $id && isset($CAT_NAMES[$id])) $name = $CAT_NAMES[$id];
    if (!$name && !empty($cat['slug'])) {
    $slug = $cat['slug'];
    $tryKeys = ["customer_categories.labels.{$slug}", "m_tours.customer_categories.labels.{$slug}"];
    foreach ($tryKeys as $key) {
    $tr = __($key, [], $receiptLocale);
    if ($tr !== $key) { $name = $tr; break; }
    }
    }
    return $name ?: 'Category';
    };

    $categories = [];
    if (!empty($categoriesData)) {
    if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
    foreach ($categoriesData as $cat) {
    $qty = (int)($cat['quantity'] ?? 0);
    $price = (float)($cat['price'] ?? 0);
    $cid = $cat['category_id'] ?? $cat['id'] ?? null;
    $slug = $cat['slug'] ?? null;
    $catArray = array_merge($cat, ['slug' => $slug]);
    $name = $resolveCatName($catArray, $cid);
    $categories[] = ['name' => $name, 'quantity' => $qty, 'price' => $price, 'total' => $qty * $price];
    $totalPersons += $qty;
    $subtotal += $qty * $price;
    }
    } else {
    foreach ($categoriesData as $catId => $cat) {
    $qty = (int)($cat['quantity'] ?? 0);
    $price = (float)($cat['price'] ?? 0);
    $slug = $cat['slug'] ?? null;
    $catArray = is_array($cat) ? array_merge($cat, ['slug' => $slug]) : ['slug' => $slug];
    $name = $resolveCatName($catArray, $catId);
    $categories[] = ['name' => $name, 'quantity' => $qty, 'price' => $price, 'total' => $qty * $price];
    $totalPersons += $qty;
    $subtotal += $qty * $price;
    }
    }
    }

    // Fallback
    if (empty($categories)) {
    $adultsQty = (int)($detail->adults_quantity ?? 0);
    $kidsQty = (int)($detail->kids_quantity ?? 0);
    $adultPrice = (float)($detail->adult_price ?? $tour->adult_price ?? 0);
    $kidPrice = (float)($detail->kid_price ?? $tour->kid_price ?? 0);

    if ($adultsQty > 0) {
    $categories[] = ['name' => __('m_bookings.categories.adult', [], $receiptLocale), 'quantity' => $adultsQty, 'price' => $adultPrice, 'total' => $adultsQty * $adultPrice];
    $totalPersons += $adultsQty;
    $subtotal += $adultsQty * $adultPrice;
    }
    if ($kidsQty > 0) {
    $categories[] = ['name' => __('m_bookings.categories.kid', [], $receiptLocale), 'quantity' => $kidsQty, 'price' => $kidPrice, 'total' => $kidsQty * $kidPrice];
    $totalPersons += $kidsQty;
    $subtotal += $kidsQty * $kidPrice;
    }
    }

    // Dates
    $tourDate = $detail->tour_date ? Carbon::parse($detail->tour_date)->isoFormat('DD/MMM/YYYY') : '—';

    // Promo
    $redemption = $booking->redemption;
    $promo = $booking->promoCode ?? optional($redemption)->promoCode;
    $opApplied = null;
    $appliedAmt = 0.0;

    if ($redemption) {
    $opApplied = $redemption->operation_snapshot;
    $appliedAmt = (float)($redemption->applied_amount ?? 0);
    } elseif ($promo) {
    $opApplied = $promo->operation === 'add' ? 'add' : 'subtract';
    if (!empty($promo->discount_percent)) {
    $appliedAmt = round($subtotal * ((float)$promo->discount_percent / 100), 2);
    } elseif (!empty($promo->discount_amount)) {
    $appliedAmt = (float)$promo->discount_amount;
    }
    }
    @endphp

    {{-- Debug info (hidden by default) --}}
    <div class="debug-info">
        <strong>DEBUG INFO:</strong><br>
        @foreach($debugInfo as $info)
        {{ $info }}<br>
        @endforeach
    </div>

    {{-- Logo --}}
    <div class="logo">
        <img src="{{ public_path('cdn/logos/brand-logo-white.png') }}" alt="{{ config('app.name') }}">
    </div>

    {{-- Title --}}
    <div class="title">{{ __('m_bookings.receipt.title', [], $receiptLocale) }}</div>

    {{-- Main Info --}}
    <div class="info-block">
        <div class="info-line highlight">
            <span class="label">{{ __('m_bookings.receipt.tour_date', [], $receiptLocale) }}</span>
            <span class="value">{{ $tourDate }}</span>
        </div>

        <div class="info-line">
            <span class="label">{{ __('m_bookings.receipt.client', [], $receiptLocale) }}</span>
            <span class="value">{{ optional($booking->user)->full_name ?? '—' }}</span>
        </div>

        <div class="info-line">
            <span class="label">{{ __('m_bookings.receipt.tour', [], $receiptLocale) }}</span>
            <span class="value">{{ $tourName }}</span>
        </div>

        @if($detail->schedule)
        <div class="info-line">
            <span class="label">{{ __('m_bookings.receipt.schedule', [], $receiptLocale) }}</span>
            <span class="value">{{ Carbon::parse($detail->schedule->start_time)->isoFormat('LT') }}</span>
        </div>
        @endif

        <div class="info-line">
            <span class="label">{{ __('m_bookings.bookings.fields.language', [], $receiptLocale) }}</span>
            <span class="value">{{ $tourLanguageName }}</span>
        </div>
    </div>

    <div class="divider"></div>

    {{-- Categories --}}
    <div class="category-list">
        @foreach($categories as $cat)
        <div class="category-item">
            <span>{{ $cat['name'] }} × {{ $cat['quantity'] }}</span>
            <span>${{ number_format($cat['total'], 2) }}</span>
        </div>
        @endforeach
    </div>

    @if($opApplied && $appliedAmt > 0)
    <div class="category-item" style="color: {{ $opApplied === 'add' ? '#b45309' : '#1A5229' }};">
        <span>{{ $opApplied === 'add' ? __('m_bookings.receipt.surcharge', [], $receiptLocale) : __('m_bookings.receipt.discount', [], $receiptLocale) }}</span>
        <span>{{ $opApplied === 'add' ? '+' : '-' }}${{ number_format($appliedAmt, 2) }}</span>
    </div>
    @endif

    {{-- Total --}}
    <div class="total-line">
        <span>{{ __('m_bookings.receipt.total', [], $receiptLocale) }}</span>
        <span>${{ number_format((float)$booking->total, 2) }}</span>
    </div>

    {{-- QR Code --}}
    <div class="qr-section">
        @php
        $base64 = null;
        try {
        $data = urlencode($booking->booking_reference ?? '');
        $urlQr = "https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={$data}";
        $png = @file_get_contents($urlQr);
        if ($png) { $base64 = base64_encode($png); }
        } catch (\Throwable $e) {}
        @endphp

        @if($base64)
        <img src="data:image/png;base64,{{ $base64 }}" alt="QR">
        @endif

        <div class="barcode-text">{{ $booking->booking_reference }}</div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        {{ config('app.name') }} <br>
        {{ __('m_bookings.receipt.thanks', ['company' => config('app.name')], $receiptLocale) }}
    </div>

    @php
    app()->setLocale($originalLocale);
    @endphp
</body>

</html>