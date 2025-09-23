{{ $brandName }}

{{ __('reviews.emails.request.greeting', ['name' => $userName ?: __('reviews.emails.traveler')]) }}

{{ __('reviews.emails.request.intro', [
    'tour' => trim($tourName . ($activityDateText ? " ({$activityDateText})" : ''))
]) }}

{{ __('reviews.emails.request.ask') }}

{{ __('reviews.emails.request.cta') }}:
{{ $ctaUrl }}

@if($expiresAtText)
{{ __('reviews.emails.request.expires', ['date' => $expiresAtText]) }}
@endif

{{ __('reviews.emails.request.footer') }}

@php
  $contact = $contact ?? [
      'site'  => 'https://greenvacationscr.com',
      'email' => 'info@greenvacationscr.com',
      'phone' => '+506 2479 1471',
  ];
@endphp

{!! strip_tags(__('reviews.emails.contact_line', [
    'email' => $contact['email'],
    'phone' => $contact['phone'],
    'url'   => $contact['site'],
])) !!}
