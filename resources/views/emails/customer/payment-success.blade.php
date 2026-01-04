@component('mail::message')
# Payment Confirmed! 🎉

Hi {{ $booking->user->name }},

Your payment has been successfully processed!

**Booking Details:**
- **Reference:** {{ $booking->booking_reference }}
- **Tour:** {{ $booking->tour->title }}
- **Date:** {{ $booking->details->first()->tour_date ?? 'N/A' }}
- **Amount Paid:** ${{ number_format($booking->paid_amount, 2) }}
- **Payment Date:** {{ $booking->paid_at->format('F j, Y') }}

Your booking is now confirmed. We'll send you more details closer to your tour date.

@if(!empty($passwordSetupUrl))
@component('mail::button', ['url' => $passwordSetupUrl])
Create Account & View Booking
@endcomponent
@else
@component('mail::button', ['url' => route('user.bookings')])
View My Bookings
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent