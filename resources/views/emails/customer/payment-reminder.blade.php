@component('mail::message')
# Payment Reminder - Your Tour is Coming Soon!

Hi {{ $booking->user->name }},

Your tour is coming up and we wanted to remind you about your pending payment.

**Booking Details:**
- **Reference:** {{ $booking->booking_reference }}
- **Tour:** {{ $booking->tour->title }}
- **Date:** {{ $booking->details->first()->tour_date ?? 'N/A' }}
- **Amount Due:** ${{ number_format($booking->total, 2) }}

**Important:** Your card will be automatically charged in **{{ $daysUntilCharge }} days** on {{ $booking->auto_charge_at->format('F j, Y') }}.

You can also pay now using the link below:

@component('mail::button', ['url' => $paymentUrl])
Pay Now
@endcomponent

If you have any questions, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent