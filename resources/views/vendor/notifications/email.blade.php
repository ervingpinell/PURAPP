@extends('emails.layouts.base')


@section('content')
{{-- Greeting --}}
@if (! empty($greeting))
<h1 style="margin-top:0; color:#333; font-size:24px; font-weight:bold; text-align:left;">
    {{ $greeting }}
</h1>
@else
@if ($level === 'error')
<h1 style="margin-top:0; color:#e74c3c; font-size:24px; font-weight:bold; text-align:left;">
    {{ __('Whoops!') }}
</h1>
@else
<h1 style="margin-top:0; color:#333; font-size:24px; font-weight:bold; text-align:left;">
    {{ __('Hello!') }}
</h1>
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
<p style="color:#333; font-size:16px; line-height:1.5em; margin-top:0; text-align:left;">
    {!! $line !!}
</p>
@endforeach

{{-- Action Button --}}
@if (isset($actionText))
<div style="text-align:center; margin:30px 0;">
    <?php
    $buttonColor = match ($level) {
        'success', 'error' => '#e74c3c', // --primary-red
        default => '#60a862', // --primary-color
    };
    ?>
    <a href="{{ $actionUrl }}"
        style="display:inline-block; background-color:{{ $buttonColor }}; color:#ffffff; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:600; font-size:14px;"
        target="_blank"
        rel="noopener">
        {{ $actionText }}
    </a>
</div>
@endif

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
<p style="color:#333; font-size:16px; line-height:1.5em; margin-top:0; text-align:left;">
    {!! $line !!}
</p>
@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
<p style="color:#333; font-size:16px; line-height:1.5em; margin-top:0; text-align:left;">
    {!! $salutation !!}
</p>
@else
<p style="color:#333; font-size:16px; line-height:1.5em; margin-top:0; text-align:left;">
    {{ __('Regards') }},<br>
    {{ config('app.name') }}
</p>
@endif

{{-- Subcopy (Trouble clicking button) --}}
@if (isset($actionText))
<div style="margin-top:25px; padding-top:25px; border-top:1px solid #e8e5ef;">
    <p style="font-size:12px; line-height:1.5; color:#b0adc5;">
        @lang(
        "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below into your web browser:",
        ['actionText' => $actionText]
        )
        <br>
        <span style="word-break:break-all; color:#3869d4;">
            <a href="{{ $actionUrl }}" target="_blank" style="color:#3869d4; text-decoration:none;">{{ $actionUrl }}</a>
        </span>
    </p>
</div>
@endif

@endsection