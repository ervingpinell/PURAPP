@extends('layouts.app')

@push('meta')
  <meta name="robots" content="noindex, nofollow">
@endpush

@vite([
  'resources/css/tour-review.css',
  'resources/css/tour.css',
  'resources/js/viator/tour-reviews.js'
])

@section('content')

<section class="tour-section py-5">
  <div class="container">
<div class="row g-4 align-items-start">


      {{-- ✅ Columna Izquierda: Carousel y Overview --}}
      <div class="col-md-7">
        {{-- ✅ Mensajes de feedback --}}
        @include('partials.tour.feedback')

        {{-- 📸 CARRUSEL --}}
        @include('partials.tour.carousel', ['tour' => $tour])

        {{-- 📄 OVERVIEW (Nombre, duración, descripción) --}}
        @include('partials.tour.overview', ['tour' => $tour])
      </div>

      {{-- ✅ Columna Derecha: Reserva e Información --}}
<div class="col-md-5 d-flex flex-column justify-content-between" style="min-height: 360px;">
  <div class="flex-grow-1 mb-3">
    @include('partials.tour.reservation-box', ['tour' => $tour, 'hotels' => $hotels])
  </div>
  <div>
    @include('partials.tour.info-box', ['tour' => $tour])
  </div>
</div>


    {{-- 🔽 ACCORDIONS: Itinerario, Incluido, Hoteles --}}
    <div class="row mt-5">
      <div class="col-md-12">
        @include('partials.tour.accordions', ['tour' => $tour, 'hotels' => $hotels])
      </div>
    </div>

    {{-- 🌟 TOUR REVIEWS (debajo de acordeones) --}}
    <div class="row mt-5">
      <div class="col-md-12">
        @include('partials.tour.tour-reviews')
      </div>
    </div>
  </div>

{{-- 🧠 Variables globales JS --}}
@php
    $tourJsData = [
        'id'   => $tour->tour_id,
        'code' => $tour->viator_code,
        'name' => $tour->getTranslatedName(),
    ];
@endphp

<script>
    window.tourData = @json($tourJsData);
    window.tourId = {{ $tour->tour_id }};
    window.maxCapacity = {{ $tour->max_capacity }};
    window.productCode = @json($tour->viator_code);
</script>


{{-- ✅ Travelers Modal --}}
@include('partials.bookmodal')

@endsection
