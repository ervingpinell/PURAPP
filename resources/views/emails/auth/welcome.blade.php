@extends('emails.layouts.base')

@section('content')
<div style="font-family: 'Segoe UI', sans-serif; text-align: center;">
    <h1 style="color: #047857; font-size: 24px; margin-bottom: 20px;">{{ __('password_setup.email_welcome_title', ['name' => $user->name]) }}</h1>

    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 20px; margin-bottom: 25px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;">
        <p style="text-align: center; color: #065f46; font-weight: 500; font-size: 16px; margin-bottom: 0;">
            {{ __('password_setup.email_welcome_text') }}
        </p>
    </div>

    <div style="margin-bottom: 30px;">
        <a href="{{ route('my-bookings') }}" style="background-color: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            {{ __('password_setup.email_action_button') }}
        </a>
    </div>

    <p style="font-size: 12px; color: #9ca3af; margin-top: 20px;">
        {{ __('Si el botón no funciona, copia y pega este enlace en tu navegador:') }}<br>
        <a href="{{ route('my-bookings') }}" style="color: #059669; word-break: break-all;">{{ route('my-bookings') }}</a>
    </p>

    <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
        {{ config('app.name') }}
    </p>
</div>
@endsection