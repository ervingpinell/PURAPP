Payment Successful

Thank you! Your payment has been received.

Booking Details
---------------
Reference: {{ $booking->booking_reference }}
Tour: {{ $booking->tour->title }}
Date: {{ $booking->details->first()->tour_date ?? 'N/A' }}
Amount Paid: ${{ number_format($booking->paid_amount ?? $booking->total, 2) }}
Payment Date: {{ optional($booking->paid_at)->format('F j, Y') ?? now()->format('F j, Y') }}

Your booking is now confirmed. We'll send you more details closer to your tour date.

@if(!empty($passwordSetupUrl))
Create Account & View Booking:
{{ $passwordSetupUrl }}
@else
View My Bookings:
{{ route('my-bookings') }}
@endif

Thanks,
{{ config('app.name') }}
