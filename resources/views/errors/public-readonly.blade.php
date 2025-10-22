@extends('layouts.app')

@section('title', __('Mantenimiento'))

@section('content')
<section class="py-5">
  <div class="container">
    <div class="alert alert-warning shadow-sm">
      <h4 class="mb-2">{{ __('We are preparing something great') }}</h4>
      <p class="mb-0">
{{ __('Registration, cart, and purchases are temporarily disabled.') }}
{{ __('Thank you for your patience.') }}
      </p>
    </div>
  </div>
</section>
@endsection
