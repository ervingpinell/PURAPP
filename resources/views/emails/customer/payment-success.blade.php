@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #047857; font-size: 24px; margin-bottom: 20px;">Payment Successful</h1>

    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #065f46; font-weight: 500; font-size: 16px; margin-bottom: 20px;">
            Thank you! Your payment has been received.
        </p>

        <h3 style="border-bottom: 1px solid #d1fae5; padding-bottom: 10px; color: #064e3b; margin-top: 0; font-size: 18px;">
            Booking Details
        </h3>
        
        <p style="margin-bottom: 0; font-size: 15px; line-height: 1.8;">
            <strong>Reference:</strong> {{ $booking->booking_reference }}<br>
            <strong>Tour:</strong> {{ $booking->tour->title }}<br>
            <strong>Date:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}<br>
            <strong>Amount Paid:</strong> ${{ number_format($booking->paid_amount ?? $booking->total, 2) }}<br>
            <strong>Payment Date:</strong> {{ optional($booking->paid_at)->format('F j, Y') ?? now()->format('F j, Y') }}
        </p>
    </div>

    <p style="margin-bottom: 30px; font-size: 16px; color: #374151;">
        Your booking is now confirmed. We'll send you more details closer to your tour date.
    </p>

    <div style="margin-bottom: 30px;">
        @if(!empty($passwordSetupUrl))
            <a href="{{ $passwordSetupUrl }}" style="background-color: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                Create Account & View Booking
            </a>
        @else
            <a href="{{ route('my-bookings') }}" style="background-color: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                View My Bookings
            </a>
        @endif
    </div>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        Thanks,<br>
        {{ config('app.name') }}
    </p>
</div>
@endsection