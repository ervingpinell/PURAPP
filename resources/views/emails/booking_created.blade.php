{{-- resources/views/emails/booking_created.blade.php --}}
<!DOCTYPE html>
<html lang="{{ $lang === 'es' ? 'es' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('adminlte::email.booking_created_title', [], $lang) }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height:1.45;">
    <h2 style="color:#2E8B57; margin: 0 0 8px;">
        {{ __('adminlte::email.greeting', ['name' => $booking->user->full_name], $lang) }}
    </h2>

    <p style="margin: 0 0 12px;">
        {{ __('adminlte::email.booking_created_message', ['company' => $company, 'reference' => $booking->booking_reference], $lang) }}
    </p>

    {{-- Si hay varias reservas, mostramos un resumen de referencias arriba --}}
    @php
        $hasMultipleBookings = $details->pluck('booking_id')->unique()->count() > 1;
    @endphp

    @if($hasMultipleBookings)
        <div style="background:#f7f7f7; border:1px solid #e5e5e5; padding:10px 12px; margin:16px 0;">
            <strong>{{ $lang === 'es' ? 'Resumen de referencias' : 'References summary' }}:</strong>
            <ul style="margin:8px 0 0 18px; padding:0;">
                @foreach($details->groupBy('booking_id') as $bid => $items)
                    @php
                        $ref = optional($items->first()->booking)->booking_reference ?? '—';
                        $date = \Carbon\Carbon::parse($items->first()->tour_date)->format('d/m/Y');
                    @endphp
                    <li style="margin-bottom:4px;">
                        {{ __('adminlte::email.booking_reference', [], $lang) }}: <strong>{{ $ref }}</strong>
                        — {{ $lang === 'es' ? 'Fecha' : 'Date' }}: {{ $date }}
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        {{-- Caso una sola reserva: mostramos la referencia principal arriba --}}
        <p style="margin: 0 0 12px;">
            <strong>{{ __('adminlte::email.booking_reference', [], $lang) }}:</strong>
            {{ optional($details->first()->booking)->booking_reference ?? $booking->booking_reference }}
        </p>
    @endif

    <h4 style="margin: 16px 0 8px;">🧾 {{ __('adminlte::email.booking_details', [], $lang) }}</h4>

    {{-- Iteramos cada item (cada uno pertenece a una reserva, muestra su reference propia) --}}
    @foreach($details as $d)
        @php
            $ref = optional($d->booking)->booking_reference ?? '—';
            $hotel = $d->is_other_hotel ? ($d->other_hotel_name ?? '—') : ($d->hotel->name ?? '—');
        @endphp

        <table role="presentation" cellpadding="6" cellspacing="0" style="border:1px solid #e5e5e5; margin-bottom:12px; width:100%;">
            <tr style="background:#fafafa;">
                <td style="width:35%;"><strong>{{ __('adminlte::email.booking_reference', [], $lang) }}:</strong></td>
                <td>#{{ $ref }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('adminlte::email.tour', [], $lang) }}:</strong></td>
                <td>{{ $d->tour->name ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('adminlte::email.tour_language', [], $lang) }}:</strong></td>
                <td>{{ $d->tourLanguage->name ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('adminlte::email.date', [], $lang) }}:</strong></td>
                <td>{{ \Carbon\Carbon::parse($d->tour_date)->format('d/m/Y') }}</td>
            </tr>
            @if(!empty($d->schedule))
                <tr>
                    <td><strong>{{ $lang === 'es' ? 'Horario' : 'Schedule' }}:</strong></td>
                    <td>
                        {{ \Carbon\Carbon::parse($d->schedule->start_time)->format('g:i A') }}
                        –
                        {{ \Carbon\Carbon::parse($d->schedule->end_time)->format('g:i A') }}
                    </td>
                </tr>
            @endif
            <tr>
                <td><strong>{{ __('adminlte::email.adults', [], $lang) }}:</strong></td>
                <td>{{ $d->adults_quantity }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('adminlte::email.kids', [], $lang) }}:</strong></td>
                <td>{{ $d->kids_quantity }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('adminlte::email.hotel', [], $lang) }}:</strong></td>
                <td>{{ $hotel }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('adminlte::email.total', [], $lang) }}:</strong></td>
                <td>${{ number_format($d->total, 2) }}</td>
            </tr>
        </table>
    @endforeach

    {{-- Total combinado de todo lo enviado en este correo (útil si son varias reservas) --}}
    @php
        $grand = $details->sum('total');
    @endphp
    <p style="font-size:16px; margin-top:8px;">
        <strong>{{ $lang === 'es' ? 'Total de esta confirmación' : 'Total in this confirmation' }}:</strong>
        ${{ number_format($grand, 2) }}
    </p>

    {{-- Mensaje de seguimiento y contacto --}}
    <p style="margin-top:16px;">
        {{ __('adminlte::email.notify_on_confirmation', [], $lang) }}
    </p>
    <p>
        {{ __('adminlte::email.contact_us', ['email' => 'info@greenvacations.com'], $lang) }}
    </p>

    <p style="margin-top:8px;">
        {{ __('adminlte::email.enjoy_experience', [], $lang) }}<br>
        {{ __('adminlte::email.team_name', ['company' => $company], $lang) }}
    </p>
</body>
</html>
