{{ $brandName }}

{{ __('reviews.emails.reply.greeting', ['name' => $customerName ?: __('reviews.emails.traveler')]) }}

{{ strip_tags(__('reviews.emails.reply.intro', [
    'extra' => $tourName ? __('reviews.emails.reply.about_text', ['tour' => $tourName]) : ''
])) }}

{!! __('reviews.emails.reply.quote', ['text' => $body]) !!}

{{ __('reviews.emails.reply.closing') }}

{!! __('reviews.emails.reply.sign', ['admin' => $adminName]) !!}

{!! strip_tags(__('reviews.emails.contact_line', [
    'email' => $contact['email'],
    'phone' => $contact['phone'],
    'url'   => $contact['site'],
])) !!}
