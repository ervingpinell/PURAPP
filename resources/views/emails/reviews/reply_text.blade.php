@php
  $company = $brandName ?? (config('mail.from.name', config('app.name', 'Green Vacations CR')));
  $contact = [
      'site'  => rtrim(env('COMPANY_SITE', config('app.url')), '/'),
      'email' => env('MAIL_TO_CONTACT', config('mail.from.address')),
      'phone' => env('COMPANY_PHONE', '+506 2479 1471'),
  ];
@endphp

{{ $company }}

{{ __('reviews.emails.reply.greeting', ['name' => $customerName ?: __('reviews.emails.traveler')]) }}

{{ strip_tags(__('reviews.emails.reply.intro', [
    'extra' => $productName ? __('reviews.emails.reply.about_text', ['product' => $productName]) : ''
])) }}

{!! __('reviews.emails.reply.quote', ['text' => $body]) !!}

{{ __('reviews.emails.reply.closing') }}

{!! __('reviews.emails.reply.sign', ['admin' => $adminName]) !!}

{!! strip_tags(__('reviews.emails.contact_line', [
    'email' => $contact['email'],
    'phone' => $contact['phone'],
    'url'   => $contact['site'],
])) !!}
