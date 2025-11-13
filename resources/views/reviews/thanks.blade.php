@extends('layouts.app')

@section('title', __('reviews.public.thanks'))

@section('content')
  <div class="container py-5">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <h1 class="h4 mb-3">{{ __('reviews.public.thanks') }}</h1>
        @if(session('ok'))
          <p class="text-success mb-2">{{ session('ok') }}</p>
        @endif
        <p class="text-muted mb-4">{{ __('reviews.public.thanks') }}</p>
<a class="btn btn-primary" href="{{ route(app()->getLocale().'.home') }}">
  {{ __('reviews.public.back_home') }}
</a>
      </div>
    </div>
  </div>
@endsection
