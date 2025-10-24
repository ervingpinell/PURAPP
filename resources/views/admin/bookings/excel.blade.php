<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookings Export</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Reference</th>
                <th>Status</th>
                <th>Booking Date</th>
                <th>Client</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Tour</th>
                <th>Tour Date</th>
                <th>Hotel</th>
                <th>Schedule</th>
                <th>Type</th>
                <th>Adults</th>
                <th>Kids</th>
                <th>Total Price</th>
                <th>Coupon</th>
                <th>Adjustment</th> {{-- + or − --}}
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
                    <td>{{ ucfirst($booking->status) }}</td>
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
                <td colspan="12" style="text-align: right;">Totals:</td>
                <td>{{ $totalAdults }}</td>
                <td>{{ $totalKids }}</td>
                <td>${{ number_format($totalMoney, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
