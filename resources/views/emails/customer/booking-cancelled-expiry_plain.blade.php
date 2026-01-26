Booking Cancelled

Your booking request has expired because payment was not completed in time.

Booking Details
---------------
Reference: {{ $booking->booking_reference }}
Tour: {{ $booking->product->title }}
Date: {{ $booking->details->first()->tour_date ?? 'N/A' }}

If you would like to proceed with this tour, please create a new booking using the link below:
{{ localized_route('products.show', $booking->product) }}

Thanks,
{{ config('app.name') }}
