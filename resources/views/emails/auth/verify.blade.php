@extends('emails.layouts.base')

@section('content')
<div class="content-box">
    <h1>{{ __('auth.verify_email.title') }}</h1>
    
    <p>{{ __('auth.verify_email.line_1') }}</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $url }}" class="button primary">{{ __('auth.verify_email.action') }}</a>
    </div>
    
    <p>{{ __('auth.verify_email.line_2') }}</p>
    
    <div class="divider"></div>
    
    <p style="font-size: 13px; color: #6b7280;">
        {{ __('auth.verify_email.button_trouble', ['actionText' => __('auth.verify_email.action')]) }}
        <br>
        <a href="{{ $url }}" style="color: #60a862; word-break: break-all;">{{ $url }}</a>
    </p>
</div>
@endsection