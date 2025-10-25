{{-- resources/views/admin/bookings/pdf-summary.blade.php --}}

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('m_bookings.reports.pdf_title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root{--green-dark:#1A5229;--green-base:#2E8B57;--gray-light:#f0f2f5;--text-color:#333;--font-heading:'Montserrat',sans-serif;--font-body:'Lora',serif;}
        body{font-family:var(--font-body);font-size:14px;background:var(--gray-light);color:var(--text-color);margin:0;padding:40px;line-height:1.6;}
        h2{text-align:center;color:var(--green-dark);margin-bottom:30px;font-family:var(--font-heading);font-size:30px;letter-spacing:1.5px;text-transform:uppercase;}
        .report-container{max-width:850px;margin:auto;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.1);padding:30px;}
        .booking-section{background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:20px;margin-bottom:25px;box-shadow:0 4px 10px rgba(0,0,0,0.05);}
        .section-title{font-weight:700;color:var(--green-base);margin-bottom:15px;font-family:var(--font-heading);font-size:18px;}
        .data-item{margin-bottom:8px;display:flex;align-items:baseline;}
        .data-item strong{color:var(--green-dark);min-width:120px;display:inline-block;font-family:var(--font-heading);font-weight:600;font-size:13px;}
        .data-item span,.data-item small{font-family:var(--font-body);font-size:14px;color:var(--text-color);}
        .data-item small{font-size:12px;color:#777;}
        .line-separator{border-top:1px dashed #c0c0c0;margin:15px 0;}
        .total{font-weight:700;color:var(--green-dark);text-align:right;font-size:18px;font-family:var(--font-heading);margin-top:15px;}
        .summary-general{background:#eaf5ed;border:1px solid var(--green-base);border-radius:10px;padding:25px;margin-top:40px;box-shadow:0 5px 15px rgba(0,0,0,0.08);}
        .summary-general .data-item{font-size:16px;font-family:var(--font-heading);font-weight:600;color:var(--green-dark);}
        .summary-general .data-item strong{min-width:150px;}
        @media print{body{background:none;padding:0;margin:0}.report-container{box-shadow:none;border:none;border-radius:0;margin:0;max-width:initial;padding:0}.booking-section{border:1px solid #ccc;box-shadow:none;page-break-inside:avoid;margin-bottom:15px}.line-separator{border-top:1px solid #ddd}h2{font-size:24px}.section-title{font-size:16px}.total{font-size:16px}.summary-general{box-shadow:none;border:1px solid #ccc}}
    </style>
</head>
<body>
<div class="report-container">
    <h2>{{ __('m_bookings.reports.general_report_title') }}</h2>

    @foreach($bookings as $booking)
        @php
            $tour   = $booking->tour;
            $detail = $booking->detail;
            $adultsQty   = (int)($detail->adults_quantity ?? 0);
            $kidsQty   = (int)($detail->kids_quantity ?? 0);
            $adultPrice = $tour->adult_price ?? 0;
            $kidPrice = $tour->kid_price ?? 0;

            $hotel  = $detail->is_other_hotel ? $detail->other_hotel_name : (optional($detail->hotel)->name ?? '—');
            $meetingPoint = $detail->meeting_point_name ?? optional($detail->meetingPoint)->name ?? '—';

            $schedule = $detail->schedule
                ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') . ' — ' . \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A')
                : __('m_bookings.bookings.messages.no_schedules');

            $subtotal = ($adultPrice * $adultsQty) + ($kidPrice * $kidsQty);

            // Promo direct or via redemption
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

            $labelDiscount = __('m_bookings.reports.discount', [], false) ?: 'Discount';
            $labelSurcharge = __('m_bookings.reports.surcharge', [], false) ?: 'Surcharge';
            $adjustLabel = $operation === 'add' ? $labelSurcharge : $labelDiscount;
        @endphp

        <div class="booking-section">
            <div class="section-title">{{ __('m_bookings.bookings.fields.reference') }}: {{ $booking->booking_reference }}</div>

            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.customer') }}:</strong> <span>{{ optional($booking->user)->full_name }}</span> <small>({{ optional($booking->user)->email }})</small></div>
            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.tour') }}:</strong> <span>{{ $tour->name }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.booking_date') }}:</strong> <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('m/d/Y') }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.tour_date') }}:</strong> <span>{{ \Carbon\Carbon::parse($detail->tour_date)->format('m/d/Y') }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.schedule') }}:</strong> <span>{{ $schedule }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.hotel') }}:</strong> <span>{{ $hotel }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.meeting_point') }}:</strong> <span>{{ $meetingPoint }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.bookings.fields.status') }}:</strong> <span>{{ __('m_bookings.bookings.statuses.' . $booking->status) }}</span></div>

            <div class="line-separator"></div>

            <div class="data-item"><strong>{{ __('m_bookings.reports.adults_qty', ['qty' => $adultsQty]) }}:</strong> <span>${{ number_format($adultPrice * $adultsQty, 2) }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.reports.kids_qty', ['qty' => $kidsQty]) }}:</strong> <span>${{ number_format($kidPrice * $kidsQty, 2) }}</span></div>
            <div class="data-item"><strong>{{ __('m_bookings.reports.people') }}:</strong> <span>{{ $adultsQty + $kidsQty }}</span></div>

            <div class="line-separator"></div>

            <div class="data-item"><strong>{{ __('m_bookings.reports.subtotal') }}:</strong> <span>${{ number_format($subtotal, 2) }}</span></div>

            @if ($promo && $delta > 0)
                <div class="data-item">
                    <strong>{{ $adjustLabel }}:</strong>
                    <span style="color: {{ $operation === 'add' ? '#b45309' : 'green' }};">
                        {{ $operation === 'add' ? '+' : '−' }}${{ number_format($delta, 2) }}
                        ({{ $promo->code }})
                    </span>
                </div>
                <div class="data-item">
                    <strong>{{ __('m_bookings.reports.original_price') }}:</strong>
                    <span style="text-decoration: line-through; color: #999;">
                        ${{ number_format($subtotal, 2) }}
                    </span>
                </div>
            @endif

            <div class="total">{{ __('m_bookings.bookings.fields.total') }}: ${{ number_format($booking->total, 2) }}</div>
        </div>
    @endforeach

    <div class="line-separator"></div>

    <div class="summary-general">
        <div class="data-item"><strong>{{ __('m_bookings.reports.total_adults') }}:</strong> <span>{{ $totalAdults }}</span></div>
        <div class="data-item"><strong>{{ __('m_bookings.reports.total_kids') }}:</strong> <span>{{ $totalKids }}</span></div>
        <div class="data-item"><strong>{{ __('m_bookings.reports.total_people') }}:</strong> <span>{{ $totalPersons }}</span></div>
    </div>
</div>
</body>
</html>
