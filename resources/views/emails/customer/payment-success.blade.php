@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #047857; font-size: 24px; margin-bottom: 20px;">{{ __('adminlte::adminlte.email_templates.payment_success.title') }}</h1>

    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #065f46; font-weight: 500; font-size: 16px; margin-bottom: 20px;">
            {{ __('adminlte::adminlte.email_templates.payment_success.intro') }}
        </p>

        <h3 style="border-bottom: 1px solid #d1fae5; padding-bottom: 10px; color: #064e3b; margin-top: 0; font-size: 18px;">
            {{ __('adminlte::adminlte.email_templates.booking_details') }}
        </h3>
        
        <p style="margin-bottom: 0; font-size: 15px; line-height: 1.8;">
            <strong>{{ __('adminlte::adminlte.email_templates.reference') }}:</strong> {{ $booking->booking_reference }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.tour') }}:</strong> {{ $booking->tour->title }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.date') }}:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.amount_paid') }}:</strong> ${{ number_format($booking->paid_amount ?? $booking->total, 2) }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.payment_date') }}:</strong> {{ optional($booking->paid_at)->format('F j, Y') ?? now()->format('F j, Y') }}
        </p>
    </div>

    <p style="margin-bottom: 30px; font-size: 16px; color: #374151;">
        {{ __('adminlte::adminlte.email_templates.payment_success.confirmed_message') }}
    </p>

    <div style="margin-bottom: 30px;">
        @if(!empty($passwordSetupUrl))
            <a href="{{ $passwordSetupUrl }}" style="background-color: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                {{ __('adminlte::adminlte.email_templates.payment_success.create_account_view_booking') }}
            </a>
        @else
            <a href="{{ route('my-bookings') }}" style="background-color: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                {{ __('adminlte::adminlte.email_templates.payment_success.view_my_bookings') }}
            </a>
        @endif
    </div>


</div>
@endsection