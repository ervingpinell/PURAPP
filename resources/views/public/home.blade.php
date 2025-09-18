@extends('layouts.app')

@section('title', __('adminlte::adminlte.home'))

@push('meta')
  <meta name="robots" content="index, follow">
@endpush

@push('styles')
  @vite([
    'resources/css/reviews.css',
    'resources/css/homereviews.css',
    'resources/css/tour.css',
  ])
@endpush

@section('content')
<section class="tours-section" id="tours">
  @include('partials.tours', ['toursByType' => $toursByType, 'typeMeta' => $typeMeta])
</section>

<section class="home-testimonials">
  <h2 class="big-title text-center" style="color: var(--primary-dark);">
    {{ __('reviews.what_visitors_say') }}
  </h2>

  {{-- Carrusel unificado con look “hero” --}}
  @include('partials.reviews.hero-carousel', [
    'items'      => $homeReviews ?? collect(),
    'carouselId' => 'homeReviewsHero'
  ])
</section>

<section class="ws-section">
  @include('partials.ws-widget')
</section>

@endsection

@push('scripts')
    @vite('resources/js/reviews-carousel.js')
@endpush
