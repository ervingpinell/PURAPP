@php
$mailLocale = $mailLocale ?? 'en';
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

$d = collect($booking->details ?? [])->first();
$tourName = $d?->relationLoaded('tour') ? ($d->tour->title ?? 'Tour') : ($booking->tour->title ?? 'Tour');
@endphp

{{ $mailLocale === 'es' ? 'Reserva Actualizada' : 'Booking Updated' }}
========================

{{ $mailLocale === 'es' ? 'Referencia' : 'Reference' }}: {{ $reference }}

{{ $mailLocale === 'es' ? 'Su reserva ha sido actualizada con los siguientes detalles:' : 'Your booking has been updated with the following details:' }}

{{ $mailLocale === 'es' ? 'Resumen' : 'Summary' }}
----------------
Tour: {{ $tourName }}
{{ $mailLocale === 'es' ? 'Fecha' : 'Date' }}: {{ $d?->tour_date ? \Illuminate\Support\Carbon::parse($d->tour_date)->format('d-M-Y') : 'N/A' }}

{{ $mailLocale === 'es' ? 'Total' : 'Total' }}: ${{ number_format($booking->total, 2) }}

----------------
{{ __('adminlte::adminlte.email_templates.contact_footer', [], $mailLocale) }}

{{ config('app.name') }}
