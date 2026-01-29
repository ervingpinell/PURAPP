@php
$mailLocale = $mailLocale ?? 'en';
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

$d = collect($booking->details ?? [])->first();
$productName = $d?->relationLoaded('product') ? ($d->product->title ?? 'Product') : ($booking->product->title ?? 'Product');
@endphp

{{ $mailLocale === 'es' ? 'Reserva Actualizada' : 'Booking Updated' }}
========================

{{ $mailLocale === 'es' ? 'Referencia' : 'Reference' }}: {{ $reference }}

{{ $mailLocale === 'es' ? 'Su reserva ha sido actualizada con los siguientes detalles:' : 'Your booking has been updated with the following details:' }}

{{ $mailLocale === 'es' ? 'Resumen' : 'Summary' }}
----------------
{{ __('adminlte::email.service') }}: {{ $productName }}
{{ $mailLocale === 'es' ? 'Fecha' : 'Date' }}: {{ $d?->product_date ? \Illuminate\Support\Carbon::parse($d->product_date)->format('d-M-Y') : 'N/A' }}

{{ $mailLocale === 'es' ? 'Total' : 'Total' }}: ${{ number_format($booking->total, 2) }}

----------------
{{ __('adminlte::adminlte.email_templates.contact_footer', [], $mailLocale) }}

{{ config('app.name') }}
