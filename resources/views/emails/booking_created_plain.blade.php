@php
$mailLocale = $mailLocale ?? 'en';
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

$d = collect($booking->details ?? [])->first();
$tourName = $d?->relationLoaded('tour') ? ($d->tour->title ?? 'Tour') : ($booking->product->title ?? 'Tour');
$tourDate = $d?->tour_date ? \Illuminate\Support\Carbon::parse($d->tour_date)->format('d-M-Y') : null;
@endphp

{{ $mailLocale === 'es' ? 'Solicitud de Reserva' : 'Booking Request' }}
========================

{{ $mailLocale === 'es' ? 'Referencia' : 'Reference' }}: {{ $reference }}

{{ $mailLocale === 'es' ? 'Gracias por su solicitud. Estamos procesando su reserva.' : 'Thank you for your request. We are processing your booking.' }}

{{ $mailLocale === 'es' ? 'Resumen' : 'Summary' }}
----------------
{{ __('adminlte::email.service') }}: {{ $tourName }}
@if($tourDate)
{{ $mailLocale === 'es' ? 'Fecha' : 'Date' }}: {{ $tourDate }}
@endif

{{ $mailLocale === 'es' ? 'Total' : 'Total' }}: ${{ number_format($booking->total, 2) }}

----------------
{{ __('adminlte::adminlte.email_templates.contact_footer', [], $mailLocale) }}

{{ config('app.name') }}
