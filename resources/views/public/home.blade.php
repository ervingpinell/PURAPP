@extends('layouts.app')

@section('title', __('adminlte::adminlte.home'))

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

{{-- ✅ Estilos específicos del home --}}
@push('styles')
    @vite([
        'resources/css/reviews.css',
        'resources/css/homereview.css',
        'resources/css/tour.css',
    ])
@endpush

@section('content')
<section class="tours-section" id="tours">
    @include('partials.tours', ['toursByType' => $toursByType, 'typeMeta' => $typeMeta])
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
  // 👇 Enviamos exactamente lo que armó el controlador
  // Formato esperado: [{ id, code, name }]
  window.VIATOR_CAROUSEL_PRODUCTS = @json($carouselProductCodes, JSON_UNESCAPED_UNICODE);
</script>

@vite('resources/js/viator/carousel-reviews.js')
@endpush
