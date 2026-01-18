@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #d97706; font-size: 24px; margin-bottom: 20px;">{{ __('adminlte::adminlte.email_templates.payment_reminder.title') }}</h1>

    <p style="margin-bottom: 25px; font-size: 16px;">
        {{ __('adminlte::adminlte.email_templates.payment_reminder.intro') }}
    </p>

    <div style="background-color: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <h3 style="border-bottom: 1px solid #fde68a; padding-bottom: 10px; color: #92400e; margin-top: 0; font-size: 18px;">
            {{ __('adminlte::adminlte.email_templates.booking_details') }}
        </h3>
        
        <p style="margin-bottom: 15px; font-size: 15px; line-height: 1.8;">
            <strong>{{ __('adminlte::adminlte.email_templates.reference') }}:</strong> {{ $booking->booking_reference }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.tour') }}:</strong> {{ $booking->tour->title }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.date') }}:</strong> {{ $booking->details->first()->tour_date ?? 'N/A' }}<br>
            <strong>{{ __('adminlte::adminlte.email_templates.amount_due') }}:</strong> ${{ number_format($booking->total, 2) }}
        </p>

        <p style="margin-top: 15px; font-weight: 500; color: #b45309;">
            <strong>{{ __('adminlte::adminlte.email_templates.important') }}:</strong>
            {!! __('adminlte::adminlte.email_templates.payment_reminder.auto_charge_warning', [
                'days' => $daysUntilCharge,
                'date' => optional($booking->auto_charge_at)->format('F j, Y') ?? 'the scheduled date'
            ]) !!}
        </p>
    </div>

    <p style="margin-bottom: 20px; font-size: 16px;">
        {{ __('adminlte::adminlte.email_templates.payment_reminder.pay_now_intro') }}
    </p>

    <div style="margin-bottom: 30px;">
        <a href="{{ $paymentUrl }}" style="background-color: #d97706; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            {{ __('adminlte::adminlte.email_templates.payment_reminder.pay_now') }}
        </a>
    </div>

    <p style="font-size: 15px; color: #4b5563; margin-bottom: 20px;">
        {{ __('adminlte::adminlte.email_templates.contact_footer') }}
    </p>


</div>
@endsection