@extends('emails.layouts.base')

@section('content')
{{-- Render all sections dynamically --}}
@if(isset($greeting))
<p>{!! nl2br(e($greeting)) !!}</p>
@endif

@if(isset($intro))
<p>{!! nl2br(e($intro)) !!}</p>
@endif

@if(isset($message))
<p>{!! nl2br(e($message)) !!}</p>
@endif

@if(isset($cta_button) && isset($cta_url))
<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $cta_url ?? '#' }}" class="button" style="display: inline-block; padding: 12px 30px; background-color: #3869d4; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: 600;">
        {{ $cta_button }}
    </a>
</div>
@endif

@if(isset($details))
<div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
    {!! nl2br(e($details)) !!}
</div>
@endif

@if(isset($closing))
<p>{!! nl2br(e($closing)) !!}</p>
@endif

@if(isset($signature))
<p style="margin-top: 20px;"><strong>{!! nl2br(e($signature)) !!}</strong></p>
@endif
@endsection