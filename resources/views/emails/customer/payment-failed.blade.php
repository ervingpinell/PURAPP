@component('mail::message')
# Payment Failed - Action Required

Hi {{ $booking->user->name }},

We attempted to charge your card for the upcoming tour, but the payment failed.

**Booking Details:**
- **Reference:** {{ $booking->booking_reference }}
- **Tour:** {{ $booking->tour->title }}
- **Date:** {{ $booking->details->first()->tour_date ?? 'N/A' }}
- **Amount Due:** ${{ number_format($booking->total, 2) }}

**Important:** You have **{{ $graceHours }} hours** to complete payment before your booking is cancelled.

@component('mail::button', ['url' => $paymentUrl])
Pay Now
@endcomponent

If you need assistance, please contact us immediately.

Thanks,<br>
{{ config('app.name') }}
@endcomponent