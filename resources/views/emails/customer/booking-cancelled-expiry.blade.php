@component('mail::message')
# Booking Cancelled

Hi {{ $booking->user->name }},

Unfortunately, your booking has been automatically cancelled due to non-payment.

**Booking Details:**
- **Reference:** {{ $booking->booking_reference }}
- **Tour:** {{ $booking->tour->title }}
- **Date:** {{ $booking->details->first()->tour_date ?? 'N/A' }}
- **Amount:** ${{ number_format($booking->total, 2) }}

If you'd like to rebook this tour, please visit our website or contact us.

@component('mail::button', ['url' => url('/')])
Browse Tours
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent