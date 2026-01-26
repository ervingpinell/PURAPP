@extends('emails.layouts.base')

@section('content')
<div style="text-align: center; font-family: 'Segoe UI', sans-serif;">
    <h1 style="color: #b91c1c; font-size: 24px; margin-bottom: 20px;">@lang('emails.booking_expiring.subject')</h1>

    <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 20px; margin-bottom: 20px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="margin-bottom: 15px; font-size: 16px;">
            <strong>@lang('emails.booking_expiring.label_reference'):</strong> {{ $booking->booking_reference }}<br>
            <strong>@lang('emails.booking_expiring.label_customer'):</strong> {{ $booking->user->full_name }}<br>
            <strong>@lang('emails.booking_expiring.label_email'):</strong> {{ $booking->user->email }}<br>
            <strong>@lang('emails.booking_expiring.label_tour'):</strong> {{ $booking->product->title }}<br>
            <strong>@lang('emails.booking_expiring.label_date'):</strong> {{ $booking->details->first()->tour_date ?? __('emails.booking_expiring.na') }}<br>
            <strong>@lang('emails.booking_expiring.label_amount'):</strong> ${{ number_format($booking->total, 2) }}<br>
            <strong>@lang('emails.booking_expiring.label_expires'):</strong> 
            {{ optional($booking->pending_expires_at)->format('Y-m-d H:i:s') ?? 'N/A' }} 
            ({{ optional($booking->pending_expires_at)->diffForHumans() ?? '' }})
        </p>
        
        <p style="text-align: center; color: #7f1d1d; font-weight: 600;">
            @lang('emails.booking_expiring.intro')
        </p>
    </div>

    <div style="margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ $extendUrl }}" style="background-color: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;">
            @lang('emails.booking_expiring.button_extend')
        </a>
        
        <a href="{{ route('admin.bookings.show', $booking->booking_id) }}" style="background-color: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            @lang('emails.booking_expiring.button_view')
        </a>
    </div>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        @lang('emails.common.thanks'),<br>
        {{ config('app.name') }} - @lang('emails.common.system')
    </p>
</div>
@endsection