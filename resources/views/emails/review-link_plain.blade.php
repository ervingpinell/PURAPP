@php
  $company = $brandName ?? (config('mail.from.name', config('app.name', 'Green Vacations CR')));
  $contact = [
      'site'  => rtrim(env('COMPANY_SITE', config('app.url')), '/'),
      'email' => env('MAIL_TO_CONTACT', config('mail.from.address')),
      'phone' => env('COMPANY_PHONE', '+506 2479 1471'),
  ];
  $tourLabel = trim(($tourName ?? '') . (!empty($activityDateText) ? " ({$activityDateText})" : ''));
@endphp

{{ $company }}

{{ __('reviews.emails.request.greeting', ['name' => $userName ?: __('reviews.emails.traveler')]) }}

{{ __('reviews.emails.request.intro', ['tour' => $tourLabel]) }}

{{ __('reviews.emails.request.ask') }}

{{ __('reviews.emails.request.cta') }}:
{{ $ctaUrl }}

@if(!empty($expiresAtText))
{{ __('reviews.emails.request.expires', ['date' => $expiresAtText]) }}
@endif

{{ __('reviews.emails.request.footer') }}

{!! strip_tags(__('reviews.emails.contact_line', [
    'email' => $contact['email'],
    'phone' => $contact['phone'],
    'url'   => $contact['site'],
])) !!}
