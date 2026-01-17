@php
$locale = $booking->tour->lang ?? config('app.locale');
@endphp

{{ __('adminlte::adminlte.email.payment_failed.title', [], $locale) }}
========================

{{ __('adminlte::adminlte.email.payment_failed.intro', [], $locale) }}

{{ __('adminlte::adminlte.email.booking_details', [], $locale) }}
----------------
{{ __('adminlte::adminlte.email.reference', [], $locale) }}: {{ $booking->booking_reference }}
{{ __('adminlte::adminlte.email.tour', [], $locale) }}: {{ $booking->tour->title }}
{{ __('adminlte::adminlte.email.date', [], $locale) }}: {{ $booking->details->first()->tour_date ?? 'N/A' }}
{{ __('adminlte::adminlte.email.amount_due', [], $locale) }}: ${{ number_format($booking->total, 2) }}

{{ __('adminlte::adminlte.email.payment_failed.update_payment', [], $locale) }}

{{ __('adminlte::adminlte.email.payment_failed.try_again', [], $locale) }}:
{{ $paymentUrl }}

{{ __('adminlte::adminlte.email.payment_failed.support_footer', [], $locale) }}

{{ __('adminlte::adminlte.email.thanks', [], $locale) }},
{{ config('app.name') }}
