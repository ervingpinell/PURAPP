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
    <div class="row g-4 align-items-start">
      {{-- Columna izquierda --}}
      <div class="col-md-7">
        @include('partials.tour.feedback')

        {{-- Carrusel --}}
        @include('partials.tour.carousel', ['tour' => $tour])

        {{-- Encabezado del tour + resumen --}}
        @include('partials.tour.overview', ['tour' => $tour])
      </div>

      {{-- Columna derecha --}}
      <div class="col-md-5 d-flex flex-column justify-content-between tour-right-col">
        <div class="flex-grow-1 mb-3">
          @include('partials.tour.reservation-box', [
          'tour' => $tour,
          'hotels' => $hotels,
          'blockedGeneral' => $blockedGeneral ?? [],
          'blockedBySchedule' => $blockedBySchedule ?? [],
          'fullyBlockedDates' => $fullyBlockedDates ?? [],
          ])
        </div>

        <div>
          @include('partials.tour.info-box', ['tour' => $tour])
        </div>
      </div>
    </div>

    {{-- 🔽 ACCORDIONS --}}
    <div class="row mt-5">
      <div class="col-12">
        @include('partials.tour.accordions', ['tour' => $tour, 'hotels' => $hotels])
      </div>
    </div>

    {{-- 🌟 Reviews --}}
    <div class="row mt-5">
      <div class="col-12 text-center mb-3">
        <h2 class="fw-bold">{{ __('reviews.reviews_title') }}</h2>
      </div>
      <div class="col-12">
        @include('partials.reviews.carousel', [
        'items' => $tourReviews ?? collect(),
        'providerHeight' => 320
        ])
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
      window.tourId = {
        {
          $tour -> tour_id
        }
      };
      window.maxCapacity = {
        {
          $tour -> max_capacity
        }
      };
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
