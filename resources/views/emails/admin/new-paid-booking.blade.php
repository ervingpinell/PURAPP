@component('mail::message')
# ✅ New Paid Booking

A new booking has been paid and confirmed!

**Booking Details:**
- **Reference:** {{ $booking->booking_reference }}
- **Customer:** {{ $booking->user->name }} ({{ $booking->user->email }})
- **Tour:** {{ $booking->tour->title }}
- **Date:** {{ $booking->details->first()->tour_date ?? 'N/A' }}
- **Amount:** ${{ number_format($booking->paid_amount, 2) }}
- **Paid At:** {{ $booking->paid_at->format('Y-m-d H:i:s') }}

@component('mail::button', ['url' => route('admin.bookings.show', $booking->booking_id)])
View Booking
@endcomponent

Thanks,<br>
{{ config('app.name') }} - Booking System
@endcomponent