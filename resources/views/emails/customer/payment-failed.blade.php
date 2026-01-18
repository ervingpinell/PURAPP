@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #ef4444; font-size: 24px; margin-bottom: 20px;">{{ __('adminlte::adminlte.email_templates.payment_failed.title') }}</h1>

    <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #991b1b; font-weight: 500; font-size: 16px; margin-bottom: 20px;">
            {{ __('adminlte::adminlte.email_templates.payment_failed.intro') }}
        </p>

        <h3 style="border-bottom: 1px solid #fee2e2; padding-bottom: 10px; color: #991b1b; margin-top: 0; font-size: 18px;">
            {{ __('adminlte::adminlte.email_templates.booking_details') }}
        </h3>
        
        <p style="margin-bottom: 0; font-size: 15px; line-height: 1.8;">
            <strong>{{ __('adminlte::adminlte.email_templates.reference') }}:</strong> {{ $booking->booking_reference }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.tour') }}:</strong> {{ $booking->tour->title }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.date') }}:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.amount_due') }}:</strong> ${{ number_format($booking->total, 2) }}
        </p>
    </div>

    <p style="margin-bottom: 25px; font-size: 16px; color: #374151;">
        {{ __('adminlte::adminlte.email_templates.payment_failed.update_payment') }}
    </p>

    <div style="margin-bottom: 30px;">
        <a href="{{ $paymentUrl }}" style="background-color: #ef4444; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            {{ __('adminlte::adminlte.email_templates.payment_failed.try_again') }}
        </a>
    </div>

    <p style="font-size: 15px; color: #4b5563; margin-bottom: 20px;">
        {{ __('adminlte::adminlte.email_templates.payment_failed.support_footer') }}
    </p>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        {{ __('adminlte::adminlte.email_templates.thanks') }},<br>
        {{ config('app.name') }}
    </p>
</div>
@endsection