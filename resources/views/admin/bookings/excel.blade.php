{{-- resources/views/admin/bookings/excel.blade.php --}}

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('m_bookings.reports.excel_title') }}</title>
</head>
<body>
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
                <th>{{ __('m_bookings.bookings.fields.schedule') }}</th>
                <th>{{ __('m_bookings.bookings.fields.type') }}</th>
                <th>{{ __('m_bookings.bookings.fields.adults') }}</th>
                <th>{{ __('m_bookings.bookings.fields.children') }}</th>
                <th>{{ __('m_bookings.bookings.fields.total') }}</th>
                <th>{{ __('m_bookings.reports.coupon') }}</th>
                <th>{{ __('m_bookings.reports.adjustment') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAdults = 0;
                $totalKids = 0;
                $totalMoney = 0;
            @endphp

            @foreach($bookings as $booking)
                @php
                    $detail  = $booking->detail->first();
                    $tour     = $detail->tour ?? null;
                    $hotel    = $detail->hotel ?? null;
                    $schedule = $detail->schedule ?? null;

                    $adults = (int)($detail->adults_quantity ?? 0);
                    $kids   = (int)($detail->kids_quantity ?? 0);

                    $totalAdults += $adults;
                    $totalKids   += $kids;
                    $totalMoney  += (float)$booking->total;

                    // Subtotal to calculate adjustment
                    $adultPrice = $tour->adult_price ?? 0;
                    $kidPrice = $tour->kid_price ?? 0;
                    $subtotal = ($adultPrice * $adults) + ($kidPrice * $kids);

                    // Coupon (direct or redemption)
                    $promo = $booking->promoCode ?? optional($booking->redemption)->promoCode;
                    $operation    = $promo ? ($promo->operation === 'add' ? 'add' : 'subtract') : null;

                    $delta = 0.0;
                    if ($promo) {
                        if ($promo->discount_percent) {
                            $delta = round($subtotal * ((float)$promo->discount_percent / 100), 2);
                        } elseif ($promo->discount_amount) {
                            $delta = (float)$promo->discount_amount;
                        }
                    }

                    $adjustmentText = $promo && $delta > 0
                        ? (($operation === 'add' ? '+' : '-') . '$' . number_format($delta, 2))
                        : '—';
                    $couponCode = $promo->code ?? '—';
                @endphp
                <tr>
                    <td>{{ $booking->booking_id }}</td>
                    <td>{{ $booking->booking_reference }}</td>
                    <td>{{ __('m_bookings.bookings.statuses.' . $booking->status) }}</td>
                    <td>{{ $booking->booking_date ?? '—' }}</td>
                    <td>{{ $booking->user->full_name ?? '—' }}</td>
                    <td>{{ $booking->user->email ?? '—' }}</td>
                    <td>{{ $booking->user->phone ?? '—' }}</td>
                    <td>{{ $tour ? preg_replace('/\s*\([^)]*\)/', '', $tour->name) : '—' }}</td>
                    <td>{{ $detail->tour_date ?? '—' }}</td>
                    <td>{{ $detail->is_other_hotel ? $detail->other_hotel_name : ($hotel->name ?? '—') }}</td>
                    <td>{{ $schedule ? $schedule->start_time . ' - ' . $schedule->end_time : '—' }}</td>
                    <td>{{ $tour && $tour->tourType ? $tour->tourType->name : '—' }}</td>
                    <td>{{ $adults }}</td>
                    <td>{{ $kids }}</td>
                    <td>{{ number_format($booking->total, 2) }}</td>
                    <td>{{ $couponCode }}</td>
                    <td>{{ $adjustmentText }}</td>
                </tr>
            @endforeach

            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="12" style="text-align: right;">{{ __('m_bookings.reports.totals') }}:</td>
                <td>{{ $totalAdults }}</td>
                <td>{{ $totalKids }}</td>
                <td>${{ number_format($totalMoney, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
