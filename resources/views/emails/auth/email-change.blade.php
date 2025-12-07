{{-- resources/views/emails/auth/email-change.blade.php --}}
@extends('emails.layouts.base')

@section('content')
@php
// Solo es/en, como definimos en la notificación
$mailLocale = str_starts_with(app()->getLocale(), 'es') ? 'es' : 'en';

$userName = $user->full_name
?? ($user->name ?? $user->email);
@endphp

<h1 style="margin-top:0; color:#333; font-size:24px; font-weight:bold; text-align:left;">
    {{ __('auth.email_change_title', [], $mailLocale) }}
</h1>

<p style="color:#333; font-size:16px; line-height:1.5em; margin-top:0; text-align:left;">
    {{ __('auth.email_change_hello', ['name' => $userName], $mailLocale) }}
</p>

<p style="color:#333; font-size:16px; line-height:1.5em; margin-top:0; text-align:left;">
    {{ __('auth.email_change_intro', [], $mailLocale) }}
</p>

<div style="text-align:center; margin:30px 0;">
    <a href="{{ $url }}" style="
            display:inline-block;
            background-color: #60a862;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        ">
        {{ __('auth.email_change_button', [], $mailLocale) }}
    </a>
</div>

{{-- Standard standard disclaimer (footer) added via base or not needed if base handles it? --}}
{{-- Actually, 'email_change_footer' might be the "If you didn't req this..." text, which is body text, not footer. --}}
{{-- If it's body text (disclaimer), it should effectively be paragraph text, or the gray small text? --}}
{{-- User liked the "small gray" text. So I will keep it small gray but standard format. --}}

<p style="font-size: 12px; color: #666; margin-top: 25px;">
    {{ __('auth.email_change_footer', [], $mailLocale) }}
</p>
@endsection