@extends('layouts.app')

@section('title', __('reviews.public.used'))

@section('content')
  <div class="container py-5">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <h1 class="h4 mb-3">{{ __('reviews.public.used') }}</h1>

        <p class="text-muted mb-4">
          {{ __('reviews.public.used_help') }}
        </p>

        <a class="btn btn-primary" href="{{ route('home') }}">
          {{ __('reviews.common.back') }}
        </a>
      </div>
    </div>
  </div>
@endsection
