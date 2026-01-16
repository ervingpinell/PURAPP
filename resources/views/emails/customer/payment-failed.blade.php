@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #ef4444; font-size: 24px; margin-bottom: 20px;">Payment Failed</h1>

    <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #991b1b; font-weight: 500; font-size: 16px; margin-bottom: 20px;">
            We were unable to process your payment for the following booking:
        </p>

        <h3 style="border-bottom: 1px solid #fee2e2; padding-bottom: 10px; color: #991b1b; margin-top: 0; font-size: 18px;">
            Booking Details
        </h3>
        
        <p style="margin-bottom: 0; font-size: 15px; line-height: 1.8;">
            <strong>Reference:</strong> {{ $booking->booking_reference }}<br>
            <strong>Tour:</strong> {{ $booking->tour->title }}<br>
            <strong>Date:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}<br>
            <strong>Amount Due:</strong> ${{ number_format($booking->total, 2) }}
        </p>
    </div>

    <p style="margin-bottom: 25px; font-size: 16px; color: #374151;">
        Please update your payment method or try again to secure your booking.
    </p>

    <div style="margin-bottom: 30px;">
        <a href="{{ $paymentUrl }}" style="background-color: #ef4444; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Try Again
        </a>
    </div>

    <p style="font-size: 15px; color: #4b5563; margin-bottom: 20px;">
        If you continue to experience issues, please contact our support team.
    </p>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        Thanks,<br>
        {{ config('app.name') }}
    </p>
</div>
@endsection