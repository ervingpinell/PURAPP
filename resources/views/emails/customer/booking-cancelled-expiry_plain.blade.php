Booking Cancelled

Your booking request has expired because payment was not completed in time.

Booking Details
---------------
Reference: {{ $booking->booking_reference }}
Tour: {{ $booking->tour->title }}
Date: {{ $booking->details->first()->tour_date ?? 'N/A' }}

If you would like to proceed with this tour, please create a new booking using the link below:
{{ localized_route('tours.show', $booking->tour) }}

Thanks,
{{ config('app.name') }}
