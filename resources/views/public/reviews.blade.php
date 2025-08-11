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
{{-- Título y link del tour con nombre TRADUCIDO --}}
<h3 class="review-title">
  <a href="#" class="text-light d-inline-block tour-link"
     data-id="{{ $tour->tour_id }}"
     data-name="{{ $tour->getTranslatedName() }}"
     style="text-decoration: underline;">
     {{ $tour->getTranslatedName() }}
  </a>
</h3>

                <div class="carousel" id="carousel-{{ $tour->tour_id }}">
                    <p class="text-center text-muted">Loading reviews...</p>
                </div>

                <div class="review-footer">
                    <div class="carousel-buttons-row">
                        <button class="carousel-prev" data-tour="{{ $tour->tour_id }}">❮</button>
                        <button class="carousel-next" data-tour="{{ $tour->tour_id }}">❯</button>
                    </div>
                    <div class="powered-by">
                        <small>
                            Powered by
                            <a href="https://www.viator.com/searchResults/all?search={{ $tour->viator_code }}" target="_blank" rel="noopener">
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
    $viatorTours = $tours->map(function ($t) {
        return [
            'id'   => $t->tour_id,
            'code' => $t->viator_code,
            'name' => $t->getTranslatedName(), // ya traducido
        ];
    })->values();
@endphp

<script>
  window.VIATOR_TOURS = @json($viatorTours);
</script>
@vite('resources/js/viator/review-carousel-grid.js')
@endpush


@include('partials.show-tour-modal')
