{{-- resources/views/admin/bookings/excel.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('m_bookings.reports.excel_title') }}</title>
</head>

<body>
    @php
    $EM = '—';

    // Helpers
    $normalizeName = function($name) {
    $n = trim((string)$name);
    if ($n === '') return 'Unknown';
    // Sanitizar para cabeceras: quitar saltos y comas
    $n = preg_replace('/\s+/', ' ', $n);
    $n = str_replace([",", "\t", "\r", "\n"], ' ', $n);
    return $n;
    };

    // 1) Escaneo previo: recolectar todas las categorías usadas para crear cabeceras dinámicas
    $ALL_CATS = []; // key => label
    $bookingsIter = ($bookings ?? collect());

    foreach ($bookingsIter as $b) {
    $detail = $b->detail ?? null;
    $tour = $b->tour ?? null;

    $catsRaw = [];
    if (!empty($detail?->categories)) {
    if (is_string($detail->categories)) {
    try { $catsRaw = json_decode($detail->categories, true) ?: []; } catch (\Throwable $e) {}
    } elseif (is_array($detail->categories)) {
    $catsRaw = $detail->categories;
    }
    }

    $addedAny = false;

    if (!empty($catsRaw)) {
    // Soporta array indexado y asociativo
    $iterable = isset($catsRaw[0]) ? $catsRaw : array_values($catsRaw);
    foreach ($iterable as $cat) {
    $label = $cat['i18n_name'] ?? $cat['name'] ?? $cat['translation_name'] ?? $cat['category_name'] ?? null;
    // Si no hay nombre, usa "Category #ID" como última opción
    $cid = $cat['category_id'] ?? $cat['id'] ?? null;
    if (!$label) $label = $cid ? "Category #{$cid}" : 'Category';
    $label = $normalizeName($label);
    $ALL_CATS[$label] = $label;
    $addedAny = true;
    }
    }

    // Fallback legacy si no hubo categorías dinámicas
    if (!$addedAny) {
    $adults = (int)($detail->adults_quantity ?? 0);
    $kids = (int)($detail->kids_quantity ?? 0);

    if ($adults > 0) {
    $nameA = __('m_bookings.categories.adult', [], false) ?: 'Adults';
    $nameA = $normalizeName($nameA);
    $ALL_CATS[$nameA] = $nameA;
    }
    if ($kids > 0) {
    $nameK = __('m_bookings.categories.kid', [], false) ?: 'Kids';
    $nameK = $normalizeName($nameK);
    $ALL_CATS[$nameK] = $nameK;
    }
    }
    }

    // Ordenar alfabéticamente para consistencia
    ksort($ALL_CATS);

    // 2) Acumuladores globales
    $sumPersons = 0;
    $sumSubtotal = 0.0;
    $sumAdjustment = 0.0;
    $sumFinalTotal = 0.0;

    // Acumuladores por categoría
    $sumCatQty = []; // [label => int]
    $sumCatTotal = []; // [label => float]
    foreach ($ALL_CATS as $lbl) {
    $sumCatQty[$lbl] = 0;
    $sumCatTotal[$lbl] = 0.0;
    }
    @endphp

    <table>
        <thead>
            <tr>
                <th>{{ __('m_bookings.bookings.fields.booking_id') }}</th>
                <th>{{ __('m_bookings.bookings.fields.reference') }}</th>
                <th>{{ __('m_bookings.bookings.fields.status') }}</th>
                <th>{{ __('m_bookings.bookings.fields.booking_date') }}</th>
                <th>{{ __('m_bookings.bookings.fields.customer') }}</th>
                <th>{{ __('m_bookings.bookings.fields.email') }}</th>
                <th>{{ __('m_bookings.bookings.fields.phone') }}</th>
                <th>{{ __('m_bookings.bookings.fields.tour') }}</th>
                <th>{{ __('m_bookings.bookings.fields.tour_date') }}</th>
                <th>{{ __('m_bookings.bookings.fields.hotel') }}</th>
                <th>{{ __('m_bookings.bookings.fields.meeting_point') }}</th>
                <th>{{ __('m_bookings.bookings.fields.schedule') }}</th>
                <th>{{ __('m_bookings.bookings.fields.type') }}</th>

                {{-- Columnas dinámicas por categoría: Qty & Total --}}
                @foreach($ALL_CATS as $catLabel)
                <th>{{ 'Qty: ' . $catLabel }}</th>
                <th>{{ 'Total: ' . $catLabel }}</th>
                @endforeach

                {{-- Fijas finales --}}
                <th>{{ __('m_bookings.bookings.fields.persons') }}</th>
                <th>{{ __('m_bookings.reports.subtotal') }}</th>
                <th>{{ __('m_bookings.reports.coupon') }}</th>
                <th>{{ __('m_bookings.reports.adjustment') }}</th>
                <th>{{ __('m_bookings.bookings.fields.total') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach($bookingsIter as $booking)
            @php
            $detail = $booking->detail ?? null;
            $tour = $booking->tour ?? null;
            $hotel = $detail->hotel ?? null;
            $schedule = $detail->schedule ?? null;

            // Preparar mapa de categorías por fila: [label => ['qty'=>x, 'total'=>y]]
            $rowCats = [];
            foreach ($ALL_CATS as $lbl) {
            $rowCats[$lbl] = ['qty' => 0, 'total' => 0.0];
            }

            // Personas y subtotal calculados
            $persons = 0;
            $subtotal = 0.0;

            // Obtener payload de categorías
            $catsRaw = [];
            if (!empty($detail?->categories)) {
            if (is_string($detail->categories)) {
            try { $catsRaw = json_decode($detail->categories, true) ?: []; } catch (\Throwable $e) {}
            } elseif (is_array($detail->categories)) {
            $catsRaw = $detail->categories;
            }
            }

            if (!empty($catsRaw)) {
            $iterable = isset($catsRaw[0]) ? $catsRaw : array_values($catsRaw);
            foreach ($iterable as $cat) {
            $qty = (int) ($cat['quantity'] ?? 0);
            $price = (float) ($cat['price'] ?? 0.0);
            $cid = $cat['category_id'] ?? $cat['id'] ?? null;

            $label = $cat['i18n_name'] ?? $cat['name'] ?? $cat['translation_name'] ?? $cat['category_name'] ?? null;
            if (!$label) $label = $cid ? "Category #{$cid}" : 'Category';
            $label = $normalizeName($label);

            $line = $qty * $price;
            $persons += $qty;
            $subtotal += $line;

            if (isset($rowCats[$label])) {
            $rowCats[$label]['qty'] += $qty;
            $rowCats[$label]['total'] += $line;
            } else {
            // (por si aparece una categoría no detectada en el pre-scan)
            $rowCats[$label] = ['qty' => $qty, 'total' => $line];
            }
            }
            }

            // Fallback legacy
            if ($persons === 0) {
            $adults = (int)($detail->adults_quantity ?? 0);
            $kids = (int)($detail->kids_quantity ?? 0);
            $adultPrice = (float)($tour->adult_price ?? 0.0);
            $kidPrice = (float)($tour->kid_price ?? 0.0);

            if ($adults > 0) {
            $nameA = __('m_bookings.categories.adult', [], false) ?: 'Adults';
            $nameA = $normalizeName($nameA);
            $lineA = $adults * $adultPrice;
            $persons += $adults;
            $subtotal += $lineA;
            if (isset($rowCats[$nameA])) {
            $rowCats[$nameA]['qty'] += $adults;
            $rowCats[$nameA]['total'] += $lineA;
            } else {
            $rowCats[$nameA] = ['qty' => $adults, 'total' => $lineA];
            }
            }

            if ($kids > 0) {
            $nameK = __('m_bookings.categories.kid', [], false) ?: 'Kids';
            $nameK = $normalizeName($nameK);
            $lineK = $kids * $kidPrice;
            $persons += $kids;
            $subtotal += $lineK;
            if (isset($rowCats[$nameK])) {
            $rowCats[$nameK]['qty'] += $kids;
            $rowCats[$nameK]['total'] += $lineK;
            } else {
            $rowCats[$nameK] = ['qty' => $kids, 'total' => $lineK];
            }
            }
            }

            // Código/ajuste (prioriza redención)
            $redemption = $booking->redemption ?? null;
            $promo = $booking->promoCode ?? optional($redemption)->promoCode;
            $couponCode = $EM;
            $adjustmentValue = 0.0; // +recargo / -descuento

            if ($redemption) {
            $couponCode = $redemption->code_snapshot
            ?? optional($redemption->promoCode)->code
            ?? $EM;
            $op = $redemption->operation_snapshot; // add|subtract
            $applied = (float)($redemption->applied_amount ?? 0.0);
            if ($op && $applied > 0) {
            $adjustmentValue = ($op === 'add') ? +$applied : -$applied;
            }
            } elseif ($promo) {
            $couponCode = $promo->code ?? $EM;
            $op = ($promo->operation === 'add') ? 'add' : 'subtract';
            $delta = 0.0;
            if (!empty($promo->discount_percent)) {
            $delta = round($subtotal * ((float)$promo->discount_percent / 100), 2);
            } elseif (!empty($promo->discount_amount)) {
            $delta = (float)$promo->discount_amount;
            }
            if ($delta > 0) {
            $adjustmentValue = ($op === 'add') ? +$delta : -$delta;
            }
            }

            // Total final (prefiere booking->total si existe)
            $finalTotal = isset($booking->total) ? (float)$booking->total : (float)($subtotal + $adjustmentValue);

            // Strings varios
            $statusKey = 'm_bookings.bookings.statuses.' . ($booking->status ?? 'pending');
            $statusTr = __($statusKey);
            if ($statusTr === $statusKey) { $statusTr = ucfirst((string)($booking->status ?? 'pending')); }

            $bookingDateStr = $booking->booking_date instanceof \Carbon\Carbon
            ? $booking->booking_date->format('d-M-Y H:i')
            : ($booking->booking_date ?: $EM);

            $tourName = $tour ? preg_replace('/\s*\([^)]*\)/', '', (string)($tour->name ?? '')) : $EM;

            $tourDateStr = $detail?->tour_date instanceof \Carbon\Carbon
            ? $detail->tour_date->toDateString()
            : ((string)($detail->tour_date ?? $EM));

            $scheduleStr = ($schedule?->start_time && $schedule?->end_time)
            ? ($schedule->start_time . ' - ' . $schedule->end_time)
            : $EM;

            $typeName = $tour && $tour->tourType ? (string)$tour->tourType->name : $EM;

            $customerName = optional($booking->user)->full_name
            ?? trim(((string)optional($booking->user)->first_name.' '.(string)optional($booking->user)->last_name))
            ?: $EM;

            $email = optional($booking->user)->email ?? $EM;
            $phone = optional($booking->user)->phone ?? $EM;

            // Acumular globales
            $sumPersons += (int)$persons;
            $sumSubtotal += (float)$subtotal;
            $sumAdjustment += (float)$adjustmentValue;
            $sumFinalTotal += (float)$finalTotal;

            // Acumular por categoría
            foreach ($rowCats as $lbl => $vals) {
            if (!isset($sumCatQty[$lbl])) { $sumCatQty[$lbl] = 0; }
            if (!isset($sumCatTotal[$lbl])) { $sumCatTotal[$lbl] = 0.0; }
            $sumCatQty[$lbl] += (int)$vals['qty'];
            $sumCatTotal[$lbl] += (float)$vals['total'];
            }
            @endphp

            <tr>
                <td>{{ $booking->booking_id }}</td>
                <td>{{ $booking->booking_reference ?? $EM }}</td>
                <td>{{ $statusTr }}</td>
                <td>{{ $bookingDateStr }}</td>
                <td>{{ $customerName }}</td>
                <td>{{ $email }}</td>
                <td>{{ $phone }}</td>
                <td>{{ $tourName }}</td>
                <td>{{ $tourDateStr }}</td>
                <td>
                    @php
                    $hasHotel = !empty($detail->hotel_id) || !empty($detail->other_hotel_name);
                    $hotelName = $hasHotel
                    ? ($detail->is_other_hotel ? ($detail->other_hotel_name ?: $EM) : ($hotel->name ?? $EM))
                    : $EM;
                    @endphp
                    {{ $hotelName }}
                </td>
                <td>
                    @php
                    $meetingPointName = (!$hasHotel && !empty($detail->meeting_point_id))
                    ? ($detail->meeting_point_name ?? optional($detail->meetingPoint)->name_localized ?? $EM)
                    : $EM;
                    @endphp
                    {{ $meetingPointName }}
                </td>
                <td>{{ $scheduleStr }}</td>
                <td>{{ $typeName }}</td>

                {{-- Columnas dinámicas por categoría (Qty y Total sin símbolo) --}}
                @foreach($ALL_CATS as $catLabel)
                @php
                $q = (int)($rowCats[$catLabel]['qty'] ?? 0);
                $t = (float)($rowCats[$catLabel]['total'] ?? 0.0);
                @endphp
                <td>{{ $q }}</td>
                <td>{{ number_format($t, 2, '.', '') }}</td>
                @endforeach

                {{-- Fijas finales --}}
                <td>{{ $persons }}</td>
                <td>{{ number_format($subtotal, 2, '.', '') }}</td>
                <td>{{ $couponCode }}</td>
                <td>{{ number_format($adjustmentValue, 2, '.', '') }}</td>
                <td>{{ number_format($finalTotal, 2, '.', '') }}</td>
            </tr>
            @endforeach

            {{-- Footer de totales --}}
            <tr style="font-weight:bold;background-color:#f2f2f2;">
                <td colspan="13" style="text-align:right;">{{ __('m_bookings.reports.totals') }}:</td>

                @foreach($ALL_CATS as $catLabel)
                <td>{{ (int)$sumCatQty[$catLabel] }}</td>
                <td>{{ number_format((float)$sumCatTotal[$catLabel], 2, '.', '') }}</td>
                @endforeach

                <td>{{ $sumPersons }}</td>
                <td>{{ number_format($sumSubtotal, 2, '.', '') }}</td>
                <td></td>
                <td>{{ number_format($sumAdjustment, 2, '.', '') }}</td>
                <td>{{ number_format($sumFinalTotal, 2, '.', '') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>