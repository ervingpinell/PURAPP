@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #6b7280; font-size: 24px; margin-bottom: 20px;">Booking Expired</h1>

    <div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #374151; font-weight: 500; font-size: 16px; margin-bottom: 20px;">
            Your booking request has expired because payment was not completed in time.
        </p>

        <h3 style="border-bottom: 1px solid #d1d5db; padding-bottom: 10px; color: #374151; margin-top: 0; font-size: 18px;">
            Booking Details
        </h3>
        
        <p style="margin-bottom: 0; font-size: 15px; line-height: 1.8;">
            <strong>Reference:</strong> {{ $booking->booking_reference }}<br>
            <strong>Tour:</strong> {{ $booking->tour->title }}<br>
            <strong>Date:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}
        </p>
    </div>

    <p style="margin-bottom: 25px; font-size: 16px; color: #374151;">
        If you would like to proceed with this tour, please create a new booking.
    </p>

    <div style="margin-bottom: 30px;">
        <a href="{{ localized_route('tours.show', $booking->tour) }}" style="background-color: #4b5563; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Book Again
        </a>
    </div>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        Thanks,<br>
        {{ config('app.name') }}
    </p>
</div>
@endsection