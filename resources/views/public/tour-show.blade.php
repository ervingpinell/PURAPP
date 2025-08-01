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
    <div class="row">
      {{-- ✅ Mensajes de feedback --}}
      @include('partials.tour.feedback')

      {{-- 📸 CARRUSEL --}}
      <div class="col-md-7">
        @include('partials.tour.carousel', ['tour' => $tour])
        @include('partials.tour.overview', ['tour' => $tour])
      </div>

      {{-- 📅 RESERVATION BOX --}}
      <div class="col-md-5">
        @include('partials.tour.reservation-box', ['tour' => $tour, 'hotels' => $hotels])
        @include('partials.tour.info-box', ['tour' => $tour])
      </div>
    </div>

    {{-- 🔽 ACCORDIONS --}}
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

  <script>
    window.tourId = {{ $tour->tour_id }};
    window.maxCapacity = {{ $tour->max_capacity }};
    window.productCode = @json($tour->viator_code);
  </script>
</section>

{{-- ✅ Travelers Modal --}}
@include('partials.bookmodal')

@endsection
