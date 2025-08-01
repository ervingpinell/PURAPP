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
    <h2 class="text-center mb-5">Customers Reviews</h2>

    <div class="review-grid">
        @foreach ($tours as $tour)
            <div class="review-card">
                <h3 class="review-title">{{ $tour->name }}</h3>

                <div class="carousel" id="carousel-{{ $tour->tour_id }}">
                    <p class="text-center text-muted">Loading reviews...</p>
                </div>

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
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.VIATOR_TOURS = @json($tours->map(fn($t) => [
            'id' => $t->tour_id,
            'code' => $t->viator_code
        ]));
    </script>
    @vite('resources/js/viator/review-carousel-grid.js')
@endpush
