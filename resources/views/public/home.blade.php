@extends('layouts.app')

@section('meta_title', __('adminlte::adminlte.meta.home_title'))
@section('meta_description', __('adminlte::adminlte.meta.home_description'))

@push('meta')
<meta name="robots" content="index, follow">
@endpush

@push('styles')
@vite([
'resources/css/hero.css',
'resources/css/reviews.css',
'resources/css/homereviews.css',
'resources/css/home.css',
'resources/css/tour.css',
'resources/css/app.css'
])
<style>
:root {
    /* Reviews Embed Colors - Applied to both local and iframe reviews */
    --reviews-embed-link-color: {{ branding('reviews_embed_link_color', '#1A5229') }};
    --green: {{ branding('reviews_embed_green', '#96B95B') }};
    --warn: {{ branding('reviews_embed_warn', '#e74c3c') }};
    --text-dark: {{ branding('reviews_embed_text_dark', '#222') }};
    --text-muted: {{ branding('reviews_embed_text_muted', '#6c757d') }};
    --text-rating: {{ branding('reviews_embed_text_rating', '#555') }};
    --bg-white: {{ branding('reviews_embed_bg_white', '#fff') }};
    --bg-avatar: {{ branding('reviews_embed_bg_avatar', '#e9ecef') }};
    --toggle-color: {{ branding('reviews_embed_toggle_color', '#256D1B') }};
    --stars-color: {{ branding('reviews_embed_stars_color', '#ffc107') }};
}
</style>
@endpush

@section('content')
{{-- Hero Section --}}
@if(branding('hero_enabled', '1') == '1')
    @include('partials.hero')
@endif

<h2 class="big-title text-center page-first mt-5 mb-0">
  {{ __('adminlte::adminlte.our_services') }}
</h2>

<section class="tours-section" id="tours">
  @include('partials.tours', ['toursByType' => $toursByType, 'typeMeta' => $typeMeta])
</section>

<section class="home-testimonials">
  <h2 class="big-title text-center">
    {{ __('reviews.what_visitors_say') }}
  </h2>

  @include('partials.reviews.hero-carousel', [
  'items' => $homeReviews ?? collect(),
  'carouselId' => 'homeReviewsHero'
  ])
</section>

<section class="ws-section">
  @include('partials.ws-widget')
</section>
@endsection

@push('scripts')
@vite(['resources/js/reviews-carousel.js'])
@endpush