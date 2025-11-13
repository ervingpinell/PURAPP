{{-- resources/views/emails/auth/email-change.blade.php --}}
@extends('emails.layouts.base')

@section('content')
    @php
        // Solo es/en, como definimos en la notificación
        $mailLocale = str_starts_with(app()->getLocale(), 'es') ? 'es' : 'en';

        $userName = $user->full_name
            ?? ($user->name ?? $user->email);
    @endphp

    <h1 style="margin-bottom: 16px;">
        {{ __('auth.email_change_title', [], $mailLocale) }}
    </h1>

    <p style="margin-bottom: 12px;">
        {{ __('auth.email_change_hello', ['name' => $userName], $mailLocale) }}
    </p>

    <p style="margin-bottom: 12px;">
        {{ __('auth.email_change_intro', [], $mailLocale) }}
    </p>

    <p style="margin-bottom: 20px;">
        <a href="{{ $url }}" style="
            display: inline-block;
            padding: 10px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            background-color: #60a862;
            color: #ffffff;
        ">
            {{ __('auth.email_change_button', [], $mailLocale) }}
        </a>
    </p>

    <p style="font-size: 13px; color: #666;">
        {{ __('auth.email_change_footer', [], $mailLocale) }}
    </p>
@endsection
