@extends('layouts.app')
@section('title', __('adminlte::adminlte.tours'))

@push('meta')
  <meta name="robots" content="index, follow">
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

{{-- 🌟 TOUR REVIEWS --}}
<div class="row mt-5">
  <div class="col-12 text-center mb-3">
    <h2 class="fw-bold mb-1" style="color: var(--primary-dark);">
      {{ __('reviews.reviews_title') }}
    </h2>
  </div>
  <div class="col-md-12" data-nosnippet>
    <iframe
      src="{{ route('embed.reviews.show', $tour) }}"
      title="Customer reviews"
      loading="lazy"
      width="100%"
      height="520"
      style="border:0; background:transparent"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
</div>


{{-- Variables globales JS --}}
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

  window.blockedGeneral    = @json($blockedGeneral ?? []);
  window.blockedBySchedule = @json((object)($blockedBySchedule ?? []));
  window.fullyBlockedDates = @json($fullyBlockedDates ?? []);
</script>

@include('partials.ws-widget')
@include('partials.bookmodal')
@endsection
