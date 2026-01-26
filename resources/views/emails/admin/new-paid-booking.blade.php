@extends('emails.layouts.base')

@section('content')
<div style="text-align: center; font-family: 'Segoe UI', sans-serif;">
    <h1 style="color: #047857; font-size: 24px; margin-bottom: 20px;">@lang('emails.new_paid_booking.subject')</h1>

    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; font-size: 16px; margin-bottom: 20px; color: #065f46;">
            @lang('emails.new_paid_booking.intro')
        </p>

        <h3 style="border-bottom: 1px solid #d1fae5; padding-bottom: 10px; color: #064e3b; margin-top: 0;">
            @lang('emails.new_paid_booking.label_details'):
        </h3>
        
        <p style="margin-bottom: 15px; font-size: 15px; line-height: 1.8;">
            <strong>@lang('emails.booking_expiring.label_reference'):</strong> {{ $booking->booking_reference }}<br>
            <strong>@lang('emails.booking_expiring.label_customer'):</strong> {{ $booking->user->full_name }} ({{ $booking->user->email }})<br>
            <strong>@lang('emails.booking_expiring.label_tour'):</strong> {{ $booking->product->title }}<br>
            <strong>@lang('emails.booking_expiring.label_date'):</strong> {{ $booking->details->first()->tour_date ?? __('emails.booking_expiring.na') }}<br>
            <strong>@lang('emails.booking_expiring.label_amount'):</strong> ${{ number_format($booking->paid_amount ?? $booking->total, 2) }}<br>
            <strong>@lang('emails.new_paid_booking.label_paid_at'):</strong> {{ optional($booking->paid_at)->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s') }}
        </p>
    </div>

    <div style="margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ route('admin.bookings.show', $booking->booking_id) }}" style="background-color: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            @lang('emails.new_paid_booking.button_view')
        </a>
    </div>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        @lang('emails.common.thanks'),<br>
        {{ config('app.name') }} - @lang('emails.common.system')
    </p>
</div>
@endsection