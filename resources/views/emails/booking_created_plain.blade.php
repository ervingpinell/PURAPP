@php
$mailLocale = $mailLocale ?? 'en';
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

$d = collect($booking->details ?? [])->first();
$productName = $d?->relationLoaded('product') ? ($d->product->title ?? 'Product') : ($booking->product->title ?? 'Product');
$productDate = $d?->product_date ? \Illuminate\Support\Carbon::parse($d->product_date)->format('d-M-Y') : null;
@endphp

{{ $mailLocale === 'es' ? 'Solicitud de Reserva' : 'Booking Request' }}
========================

{{ $mailLocale === 'es' ? 'Referencia' : 'Reference' }}: {{ $reference }}

{{ $mailLocale === 'es' ? 'Gracias por su solicitud. Estamos procesando su reserva.' : 'Thank you for your request. We are processing your booking.' }}

{{ $mailLocale === 'es' ? 'Resumen' : 'Summary' }}
----------------
{{ __('adminlte::email.service') }}: {{ $productName }}
@if($productDate)
{{ $mailLocale === 'es' ? 'Fecha' : 'Date' }}: {{ $productDate }}
@endif

{{ $mailLocale === 'es' ? 'Total' : 'Total' }}: ${{ number_format($booking->total, 2) }}

----------------
{{ __('adminlte::adminlte.email_templates.contact_footer', [], $mailLocale) }}

{{ config('app.name') }}
