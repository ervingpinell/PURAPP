@extends('layouts.app')

@section('title', 'Inicio')

{{-- ✅ Estilos específicos del home --}}
@push('styles')
    @vite([
        'resources/css/review.css',
        'resources/css/homereview.css',
        'resources/css/tour.css'
    ])
@endpush

@section('content')
    <section class="tours-section">
        @include('partials.tours', ['tours' => $tours])
    </section>

    <section class="home-testimonials">
        {{-- 🔁 Testimonios (Viator y otros) --}}
        @include('partials.testimonials')
    </section>

    <section class="ws-section">
        @include('partials.ws-widget')
    </section>
@endsection

@push('scripts')
    <script>
        window.VIATOR_CAROUSEL_PRODUCT_CODE = @json($carouselProductCode);
    </script>

    @once
        @vite('resources/js/viator/carousel-reviews.js')
    @endonce
@endpush
