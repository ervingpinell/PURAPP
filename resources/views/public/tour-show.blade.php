@extends('layouts.app')
@section('title', __('adminlte::adminlte.tours'))

@push('meta')
  <meta name="robots" content="index, follow">
@endpush

@push('styles')
  @vite([
    'resources/css/reviews.css',
    'resources/css/tour.css',
  ])
@endpush

@section('content')
<section class="tour-section py-5">
  <div class="container">
    <div class="row g-4 align-items-start">
      {{-- ✅ Columna Izquierda --}}
      <div class="col-md-7">
        @include('partials.tour.feedback')
        @include('partials.tour.carousel', ['tour' => $tour])
        @include('partials.tour.overview', ['tour' => $tour])
      </div>

      {{-- ✅ Columna Derecha --}}
      <div class="col-md-5 d-flex flex-column justify-content-between" style="min-height: 360px;">
        <div class="flex-grow-1 mb-3">
          @include('partials.tour.reservation-box', [
            'tour'               => $tour,
            'hotels'             => $hotels,
            'blockedGeneral'     => $blockedGeneral ?? [],
            'blockedBySchedule'  => $blockedBySchedule ?? [],
            'fullyBlockedDates'  => $fullyBlockedDates ?? [],
          ])
        </div>
        <div>
          @include('partials.tour.info-box', ['tour' => $tour])
        </div>
      </div>
    </div>

    {{-- 🔽 ACCORDIONS --}}
    <div class="row mt-5">
      <div class="col-md-12">
        @include('partials.tour.accordions', ['tour' => $tour, 'hotels' => $hotels])
      </div>
    </div>

    {{-- 🌟 TOUR REVIEWS (25, locales indexables + proveedores via iframe) --}}
    <div class="row mt-5">
      <div class="col-12 text-center mb-3">
        <h2 class="fw-bold mb-1" style="color: var(--primary-dark);">
          {{ __('reviews.reviews_title') }}
        </h2>
      </div>

      <div class="col-md-12">
        @include('partials.reviews.carousel', [
          'items' => $tourReviews ?? collect(),
          'providerHeight' => 320
        ])
      </div>
    </div>

    {{-- Variables globales JS usadas por el box de reserva / calendario --}}
    @php
      $tourJsData = [
        'id'   => $tour->tour_id,
        'code' => $tour->viator_code,
        'name' => $tour->getTranslatedName(),
      ];
    @endphp
    <script>
      window.tourData            = @json($tourJsData);
      window.tourId              = {{ $tour->tour_id }};
      window.maxCapacity         = {{ $tour->max_capacity }};
      window.productCode         = @json($tour->viator_code);
      window.blockedGeneral      = @json($blockedGeneral ?? []);
      window.blockedBySchedule   = @json((object)($blockedBySchedule ?? []));
      window.fullyBlockedDates   = @json($fullyBlockedDates ?? []);
    </script>

    @include('partials.ws-widget')
    @include('partials.bookmodal')
  </div>
</section>
@endsection
