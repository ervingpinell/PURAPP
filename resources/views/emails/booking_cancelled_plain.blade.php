@php
$mailLocale = $mailLocale ?? 'en';
$reference = $reference ?? ($booking->booking_reference ?? $booking->booking_id);
@endphp

{{ $mailLocale === 'es' ? 'Reserva Cancelada' : 'Booking Cancelled' }}
========================

{{ $mailLocale === 'es' ? 'Referencia' : 'Reference' }}: {{ $reference }}

{{ $mailLocale === 'es' ? 'Su reserva ha sido cancelada.' : 'Your booking has been cancelled.' }}

{{ $mailLocale === 'es' ? 'Si tiene alguna pregunta, por favor contáctenos.' : 'If you have any questions, please contact us.' }}

----------------
{{ __('adminlte::adminlte.email_templates.contact_footer', [], $mailLocale) }}

{{ config('app.name') }}
