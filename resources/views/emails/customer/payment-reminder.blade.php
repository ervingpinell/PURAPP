@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #d97706; font-size: 24px; margin-bottom: 20px;">Payment Reminder</h1>

    <p style="margin-bottom: 25px; font-size: 16px;">
        This is a reminder regarding your upcoming booking.
    </p>

    <div style="background-color: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <h3 style="border-bottom: 1px solid #fde68a; padding-bottom: 10px; color: #92400e; margin-top: 0; font-size: 18px;">
            Booking Details
        </h3>
        
        <p style="margin-bottom: 15px; font-size: 15px; line-height: 1.8;">
            <strong>Reference:</strong> {{ $booking->booking_reference }}<br>
            <strong>Tour:</strong> {{ $booking->tour->title }}<br>
            <strong>Date:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}<br>
            <strong>Amount Due:</strong> ${{ number_format($booking->total, 2) }}
        </p>

        <p style="margin-top: 15px; font-weight: 500; color: #b45309;">
            <strong>Important:</strong> Your card will be automatically charged in 
            <strong>{{ $daysUntilCharge }} days</strong> on 
            {{ optional($booking->auto_charge_at)->format('F j, Y') ?? 'the scheduled date' }}.
        </p>
    </div>

    <p style="margin-bottom: 20px; font-size: 16px;">
        You can also pay now using the link below:
    </p>

    <div style="margin-bottom: 30px;">
        <a href="{{ $paymentUrl }}" style="background-color: #d97706; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Pay Now
        </a>
    </div>

    <p style="font-size: 15px; color: #4b5563; margin-bottom: 20px;">
        If you have any questions, please don't hesitate to contact us.
    </p>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        Thanks,<br>
        {{ config('app.name') }}
    </p>
</div>
@endsection