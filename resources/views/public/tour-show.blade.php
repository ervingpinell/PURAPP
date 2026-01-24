@extends('layouts.app')
@section('title', __('adminlte::adminlte.tours'))

@push('meta')
<meta name="robots" content="index, follow">

{{-- Tour Schema (TouristAttraction) --}}
@php
use App\Helpers\SchemaHelper;
$tourSchema = SchemaHelper::generateTourSchema($tour, $tourReviews ?? null);
@endphp
<script type="application/ld+json">
    {!! json_encode($tourSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Breadcrumb Schema --}}
@php
$breadcrumbItems = [
['name' => __('adminlte::adminlte.home'), 'url' => url('/')],
['name' => __('adminlte::adminlte.tours'), 'url' => url('/tours')],
['name' => $tour->getTranslatedName(), 'url' => Request::url()],
];
$breadcrumbSchema = SchemaHelper::generateBreadcrumbSchema($breadcrumbItems);
@endphp
<script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@push('styles')
@vite([
'resources/css/tour.css',
'resources/css/tour-carousel.css',
'resources/css/breadcrumbs.css',
])
@endpush

@section('content')
<section class="tour-section">
  <div class="container">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('/') }}">{{ __('adminlte::adminlte.home') }}</a>
        </li>
        <li class="breadcrumb-item">
          <a href="{{ url('/tours') }}">{{ __('adminlte::adminlte.tours') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ $tour->getTranslatedName() }}
        </li>
      </ol>
    </nav>
    <div class="row g-4">
      {{-- Left Column: Tour Content (60%) --}}
      <div class="col-lg-8">
        @include('partials.tour.feedback')

        {{-- Gallery --}}
        @include('partials.tour.carousel', ['tour' => $tour])

        {{-- Title + Meta Badges + Overview --}}
        @include('partials.tour.overview-with-meta', ['tour' => $tour])

        {{-- 🔽 ACCORDIONS --}}
        <div class="mt-5">
          @include('partials.tour.accordions', ['tour' => $tour, 'hotels' => $hotels])
        </div>

        {{-- 🌟 Reviews --}}
        <div class="mt-5">
          <h2 class="fw-bold text-center mb-3">{{ __('reviews.reviews_title') }}</h2>
          @include('partials.reviews.carousel', [
            'items' => $tourReviews ?? collect(),
            'providerHeight' => 320
          ])
        </div>
      </div>

      {{-- Right Column: Sticky Booking Form (40%) --}}
      <div class="col-lg-4">
        <div class="sticky-booking-wrapper">
          @include('partials.tour.reservation-box', [
            'tour' => $tour,
            'hotels' => $hotels,
            'blockedGeneral' => $blockedGeneral ?? [],
            'blockedBySchedule' => $blockedBySchedule ?? [],
            'fullyBlockedDates' => $fullyBlockedDates ?? [],
          ])
        </div>
      </div>
    </div>

    {{-- JS globals para el box/calendario --}}
    @php
    $tourJsData = [
    'id' => $tour->tour_id,
    'code' => $tour->viator_code,
    'name' => $tour->getTranslatedName(),
    ];
    @endphp
    <script>
      window.tourData = @json($tourJsData);
      window.tourId = {{ $tour->tour_id }};
      window.maxCapacity = {{ $tour->max_capacity }};
      window.productCode = @json($tour -> viator_code);
      window.blockedGeneral = @json($blockedGeneral ?? []);
      window.blockedBySchedule = @json((object)($blockedBySchedule ?? []));
      window.fullyBlockedDates = @json($fullyBlockedDates ?? []);
    </script>

    @include('partials.ws-widget')
    @include('partials.bookmodal')
  </div>
</section>
@endsection
