@php
$locale = $booking->product->lang ?? config('app.locale');
@endphp

{{ __('adminlte::adminlte.email_templates.payment_failed.title', [], $locale) }}
========================

{{ __('adminlte::adminlte.email_templates.payment_failed.intro', [], $locale) }}

{{ __('adminlte::adminlte.email_templates.booking_details', [], $locale) }}
----------------
{{ __('adminlte::adminlte.email_templates.reference', [], $locale) }}: {{ $booking->booking_reference }}
{{ __('adminlte::adminlte.email_templates.tour', [], $locale) }}: {{ $booking->product->title }}
{{ __('adminlte::adminlte.email_templates.date', [], $locale) }}: {{ $booking->details->first()->tour_date ?? 'N/A' }}
{{ __('adminlte::adminlte.email_templates.amount_due', [], $locale) }}: ${{ number_format($booking->total, 2) }}

{{ __('adminlte::adminlte.email_templates.payment_failed.update_payment', [], $locale) }}

{{ __('adminlte::adminlte.email_templates.payment_failed.try_again', [], $locale) }}:
{{ $paymentUrl }}

{{ __('adminlte::adminlte.email_templates.payment_failed.support_footer', [], $locale) }}

{{ __('adminlte::adminlte.email_templates.thanks', [], $locale) }},
{{ config('app.name') }}
