@extends('layouts.app')

@section('title', 'Customer Reviews')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@push('styles')
    @vite('resources/css/reviews.css')
@endpush

@section('content')
<div class="container py-5">
    <h2 class="big-title text-center mb-5">{{ __('adminlte::adminlte.reviews') }}</h2>

    <div class="review-grid">
        @foreach ($tours as $tour)
            <div class="review-card expandable" id="card-{{ $tour->tour_id }}">
                {{-- Título con fallback: usa translated_name si existe; si no, name --}}
                <h3 class="review-title">
                  <a href="#"
                     class="text-light d-inline-block tour-link"
                     data-id="{{ $tour->tour_id }}"
                     data-name="{{ $tour->translated_name ?? $tour->name }}"
                     style="text-decoration: underline;">
                     {{ $tour->translated_name ?? $tour->name }}
                  </a>
                </h3>

                <div class="carousel" id="carousel-{{ $tour->tour_id }}">
                    <p class="text-center text-muted">
                        {{ __('adminlte::adminlte.loading_reviews') ?: 'Loading reviews...' }}
                    </p>
                </div>

                <div class="review-footer">
                    <div class="carousel-buttons-row">
                        <button class="carousel-prev" data-tour="{{ $tour->tour_id }}">❮</button>
                        <button class="carousel-next" data-tour="{{ $tour->tour_id }}">❯</button>
                    </div>
                    <div class="powered-by">
                        <small>
                            Powered by
                            <a href="https://www.viator.com/searchResults/all?search={{ urlencode($tour->viator_code) }}"
                               target="_blank" rel="noopener">
                                Viator
                            </a>
                        </small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
@php
    // Exporta tours para JS con fallback seguro: nunca null
    $viatorTours = $tours->map(function ($t) {
        return [
            'id'   => $t->tour_id,
            'code' => $t->viator_code,
            'name' => $t->translated_name ?? $t->name ?? '',
        ];
    })->values();
@endphp

<script>
  window.VIATOR_TOURS = @json($viatorTours, JSON_UNESCAPED_UNICODE);

  // Opcional: exponer traducciones al JS
  window.I18N = Object.assign({}, window.I18N || {}, {
    loading_reviews: @json(__('adminlte::adminlte.loading_reviews') ?: 'Loading reviews...')
  });
</script>

@vite('resources/js/viator/review-carousel-grid.js')
@endpush

@include('partials.show-tour-modal')
