@extends('layouts.app')
@section('title', __('adminlte::adminlte.tours'))

@push('meta')
<meta name="robots" content="index, follow">

{{-- Tour Schema (TouristAttraction) --}}
@php
use App\Helpers\SchemaHelper;
$productSchema = SchemaHelper::generateProductSchema($product, $productReviews ?? null);
@endphp
<script type="application/ld+json">
    {!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Breadcrumb Schema --}}
@php
$breadcrumbItems = [
['name' => __('adminlte::adminlte.home'), 'url' => url('/')],
['name' => __('adminlte::adminlte.tours'), 'url' => url('/tours')],
['name' => $product->getTranslatedName(), 'url' => Request::url()],
];
$breadcrumbSchema = SchemaHelper::generateBreadcrumbSchema($breadcrumbItems);
@endphp
<script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@push('styles')
@vite([
'resources/css/product.css',
'resources/css/product-carousel.css',
'resources/css/breadcrumbs.css',
])
@endpush

@section('content')
<section class="product-section">
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
          {{ $product->getTranslatedName() }}
        </li>
      </ol>
    </nav>
    <div class="row g-4">
      {{-- Left Column: Tour Content (60%) --}}
      <div class="col-lg-8">
        @include('partials.product.feedback')

        {{-- Gallery --}}
        @include('partials.product.carousel', ['product' => $product])

        {{-- Title + Meta Badges + Overview --}}
        @include('partials.product.overview-with-meta', ['product' => $product])

        {{-- 🔽 ACCORDIONS --}}
        <div class="mt-5">
          @include('partials.product.accordions', ['product' => $product, 'hotels' => $hotels])
        </div>

        {{-- 🌟 Reviews --}}
        <div class="mt-5">
          <h2 class="fw-bold text-center mb-3">{{ __('reviews.reviews_title') }}</h2>
          @include('partials.reviews.carousel', [
            'items' => $productReviews ?? collect(),
            'providerHeight' => 320
          ])
        </div>
      </div>

      {{-- Right Column: Sticky Booking Form (40%) --}}
      <div class="col-lg-4">
        <div class="sticky-booking-wrapper">
          @include('partials.product.reservation-box', [
            'product' => $product,
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
    $productJsData = [
    'id' => $product->product_id,
    'code' => $product->viator_code,
    'name' => $product->getTranslatedName(),
    ];
    @endphp
    <script>
      window.productData = @json($productJsData);
      window.productId = {{ $product->product_id }};
      window.maxCapacity = {{ $product->max_capacity }};
      window.productCode = @json($product -> viator_code);
      window.blockedGeneral = @json($blockedGeneral ?? []);
      window.blockedBySchedule = @json((object)($blockedBySchedule ?? []));
      window.fullyBlockedDates = @json($fullyBlockedDates ?? []);
    </script>

    @include('partials.ws-widget')
    @include('partials.bookmodal')
  </div>
</section>
@endsection
