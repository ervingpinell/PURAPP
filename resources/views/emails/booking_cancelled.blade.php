<!DOCTYPE html>
<html lang="{{ $mailLocale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('adminlte::email.booking_cancelled_title', [], $mailLocale) }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height:1.5;">
    <h2 style="color:#B00020; margin-bottom:8px;">
        {{ __('adminlte::email.greeting', ['name' => $booking->user->full_name], $mailLocale) }}
    </h2>

    <p style="margin-top:0;">
        {{ __('adminlte::email.booking_cancelled_message', ['company' => $company], $mailLocale) }}
    </p>

    @php
        $detail = $booking->detail;
        $meetingValue = '—';
        if ($detail) {
            $mpName = $detail->meeting_point_name ?? null;
            $mpTime = $detail->meeting_point_pickup_time ?? null;
            if ($mpName) {
                $meetingValue = $mpName . ($mpTime ? ' — ' . \Carbon\Carbon::parse($mpTime)->format('g:i A') : '');
            }
        }
    @endphp

    <h3 style="margin-top:24px;">🗂️ {{ __('adminlte::email.booking_cancelled_details', [], $mailLocale) }}</h3>
    <ul style="padding-left:18px;">
        <li><strong>{{ __('adminlte::email.booking_reference', [], $mailLocale) }}:</strong> {{ $reference }}</li>
        <li><strong>{{ __('adminlte::email.tour', [], $mailLocale) }}:</strong> {{ $booking->tour->translated_name ?? $booking->tour->name }}</li>
        <li><strong>{{ __('adminlte::email.tour_language', [], $mailLocale) }}:</strong> {{ $tourLangLabel }}</li>
        <li><strong>{{ __('adminlte::email.date', [], $mailLocale) }}:</strong> {{ optional($booking->detail->tour_date)->format('d/m/Y') }}</li>
        <li><strong>{{ __('adminlte::email.adults', [], $mailLocale) }}:</strong> {{ $booking->detail->adults_quantity }}</li>
        <li><strong>{{ __('adminlte::email.kids', [], $mailLocale) }}:</strong> {{ $booking->detail->kids_quantity }}</li>
        <li><strong>{{ __('adminlte::email.hotel', [], $mailLocale) }}:</strong> {{ $booking->detail->hotel->name ?? $booking->detail->other_hotel_name ?? '—' }}</li>
        <li><strong>{{ __('adminlte::email.meeting_point', [], $mailLocale) }}:</strong> {{ $meetingValue }}</li>
        <li><strong>{{ __('adminlte::email.total', [], $mailLocale) }}:</strong> ${{ number_format($booking->total, 2) }}</li>
        <li><strong>{{ __('adminlte::email.status', [], $mailLocale) }}:</strong> {{ $statusLabel }}</li>
    </ul>

    <p style="margin-top:16px;">
        {{ __('adminlte::email.contact_us', ['email' => $contactEmail], $mailLocale) }}
    </p>

    <p style="margin-top:16px;">
        {{ __('adminlte::email.farewell', [], $mailLocale) }}<br>
        {{ __('adminlte::email.team_name', ['company' => $company], $mailLocale) }}
    </p>
</body>
</html>
