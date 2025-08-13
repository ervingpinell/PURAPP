@extends('layouts.app')

@section('title', $policy->translation()?->title ?? $policy->name)

@section('content')
<div class="container py-4">
    @php $t = $policy->translation(); @endphp
    <h1>{{ $t?->title ?? $policy->name }}</h1>
    <div class="mt-3">{!! nl2br(e($t?->content)) !!}</div>
</div>
@endsection
