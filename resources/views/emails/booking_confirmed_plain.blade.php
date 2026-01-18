@php
$mailLocale = $mailLocale ?? 'en';
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);

// Logic reused from HTML view for consistency
$d = collect($booking->details ?? [])->first();
$tourName = $d?->tour_name;
if (!$tourName && $d?->relationLoaded('tour') && $d?->tour) {
    $tour = $d->tour;
    if (isset($tour->translated_name) && filled($tour->translated_name)) {
        $tourName = $tour->translated_name;
    } elseif (method_exists($tour, 'getTranslated')) {
        $tourName = $tour->getTranslated('name', $mailLocale) ?? $tour->name; 
    }
}
$tourName = $tourName ?: ($booking->tour->title ?? 'Tour');

$tourDate = $d?->tour_date ? \Illuminate\Support\Carbon::parse($d->tour_date)->format('d-M-Y') : null;
$scheduleTxt = $d?->schedule 
    ? \Illuminate\Support\Carbon::parse($d->schedule->start_time)->isoFormat('LT') . ' – ' . \Illuminate\Support\Carbon::parse($d->schedule->end_time)->isoFormat('LT')
    : null;
@endphp

{{ $mailLocale === 'es' ? 'Reserva Confirmada' : 'Booking Confirmed' }}
========================

{{ $mailLocale === 'es' ? 'Referencia' : 'Reference' }}: {{ $reference }}

{{ $mailLocale === 'es' ? 'Resumen' : 'Summary' }}
----------------
Tour: {{ $tourName }}
@if($tourDate)
{{ $mailLocale === 'es' ? 'Fecha' : 'Date' }}: {{ $tourDate }}
@endif
@if($scheduleTxt)
{{ $mailLocale === 'es' ? 'Horario' : 'Schedule' }}: {{ $scheduleTxt }}
@endif

{{ $mailLocale === 'es' ? 'Total' : 'Total' }}: ${{ number_format($booking->total, 2) }}

----------------
{{ __('adminlte::adminlte.email_templates.contact_footer', [], $mailLocale) }}

{{ config('app.name') }}
