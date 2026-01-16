@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #047857; font-size: 24px; margin-bottom: 20px;">{{ __('password_setup.title') }}</h1>

    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #065f46; font-weight: 500; font-size: 16px; margin-bottom: 20px;">
            {{ __('password_setup.welcome', ['name' => $user->full_name]) }}
        </p>

        @if($bookingReference)
        <p style="text-align: center; margin: 0; color: #065f46; font-size: 14px; margin-bottom: 20px;">
            <strong>{{ __('password_setup.booking_confirmed', ['reference' => $bookingReference]) }}</strong>
        </p>
        @endif

        <p style="margin-bottom: 15px; font-size: 15px; line-height: 1.8; color: #374151;">
            {{ __('password_setup.create_password') }}
        </p>

        <ul style="color: #4b5563; font-size: 14px; list-style-type: none; padding: 0; text-align: left; margin-top: 0;">
            <li style="margin-bottom: 8px;">✅ {{ __('password_setup.benefits.view_bookings') }}</li>
            <li style="margin-bottom: 8px;">✅ {{ __('password_setup.benefits.manage_profile') }}</li>
            <li>✅ {{ __('password_setup.benefits.exclusive_offers') }}</li>
        </ul>
    </div>

    <div style="margin-bottom: 30px;">
        <a href="{{ $setupUrl }}" style="background-color: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            {{ __('password_setup.submit_button') }}
        </a>
    </div>

    <div style="background-color: #fef3c7; border: 1px solid #fcd34d; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; color: #92400e;">
        ⏰ {{ __('password_setup.expires_in', ['days' => $expiresInDays]) }}
    </div>

    <p style="font-size: 12px; color: #9ca3af; margin-top: 20px;">
        {{ __('password_setup.fallback_link') }}<br>
        <a href="{{ $setupUrl }}" style="color: #059669; word-break: break-all;">{{ $setupUrl }}</a>
    </p>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        {{ config('app.name') }}
    </p>
</div>
@endsection