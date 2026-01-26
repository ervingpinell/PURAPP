Payment Reminder

This is a reminder regarding your upcoming booking.

Booking Details
---------------
Reference: {{ $booking->booking_reference }}
Tour: {{ $booking->product->title }}
Date: {{ $booking->details->first()->tour_date ?? 'N/A' }}
Amount Due: ${{ number_format($booking->total, 2) }}

Important: Your card will be automatically charged in {{ $daysUntilCharge }} days on {{ optional($booking->auto_charge_at)->format('F j, Y') ?? 'the scheduled date' }}.

You can also pay now using the link below:
{{ $paymentUrl }}

If you have any questions, please don't hesitate to contact us.

Thanks,
{{ config('app.name') }}
