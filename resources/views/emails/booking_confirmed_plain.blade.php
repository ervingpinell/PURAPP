@php
$mailLocale = $mailLocale ?? 'en';
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

// Logic reused from HTML view for consistency
$d = collect($booking->details ?? [])->first();
$productName = $d?->product_name;
if (!$productName && $d?->relationLoaded('tour') && $d?->tour) {
    $product = $d->tour;
    if (isset($product->translated_name) && filled($product->translated_name)) {
        $productName = $product->translated_name;
    } elseif (method_exists($product, 'getTranslated')) {
        $productName = $product->getTranslated('name', $mailLocale) ?? $product->name; 
    }
}
$productName = $productName ?: ($booking->product->title ?? 'Product');

$productDate = $d?->product_date ? \Illuminate\Support\Carbon::parse($d->product_date)->format('d-M-Y') : null;
$scheduleTxt = $d?->schedule 
    ? \Illuminate\Support\Carbon::parse($d->schedule->start_time)->isoFormat('LT') . ' – ' . \Illuminate\Support\Carbon::parse($d->schedule->end_time)->isoFormat('LT')
    : null;
@endphp

{{ $mailLocale === 'es' ? 'Reserva Confirmada' : 'Booking Confirmed' }}
========================

{{ $mailLocale === 'es' ? 'Referencia' : 'Reference' }}: {{ $reference }}

{{ $mailLocale === 'es' ? 'Resumen' : 'Summary' }}
----------------
{{ __('adminlte::email.service') }}: {{ $productName }}
@if($productDate)
{{ $mailLocale === 'es' ? 'Fecha' : 'Date' }}: {{ $productDate }}
@endif
@if($scheduleTxt)
{{ $mailLocale === 'es' ? 'Horario' : 'Schedule' }}: {{ $scheduleTxt }}
@endif

{{ $mailLocale === 'es' ? 'Total' : 'Total' }}: ${{ number_format($booking->total, 2) }}

----------------
{{ __('adminlte::adminlte.email_templates.contact_footer', [], $mailLocale) }}

{{ config('app.name') }}
