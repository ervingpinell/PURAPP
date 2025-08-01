@extends('layouts.app')

@section('title', 'Inicio')
@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush
{{-- ✅ Estilos específicos del home --}}
@push('styles')
    @vite([
        'resources/css/reviews.css',
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
        window.VIATOR_CAROUSEL_PRODUCTS = @json($carouselProductCodes ?? []);
    </script>
    @vite('resources/js/viator/carousel-reviews.js')
@endpush


