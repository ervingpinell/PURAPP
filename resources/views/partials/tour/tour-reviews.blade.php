<div class="col-md-12 my-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold" style="color: #256d1b">
      {{ __('reviews.what_customers_think_about') }}
    </h2>
    <h3 class="text-secondary">
      {{ $tour->getTranslatedName() }}
    </h3>
  </div>

  <div class="tour-review-carousel-wrapper">
    <button
      class="carousel-nav carousel-prev"
      type="button"
      aria-label="{{ __('reviews.previous_review') }}"
      data-tour="{{ $tour->tour_id }}"
    >❮</button>

    <div
      class="tour-review-carousel"
      id="review-carousel-tour-{{ $tour->tour_id }}"
    >
      <p class="text-center text-muted">
        {{ __('reviews.loading') }}
      </p>
    </div>

    <button
      class="carousel-nav carousel-next"
      type="button"
      aria-label="{{ __('reviews.next_review') }}"
      data-tour="{{ $tour->tour_id }}"
    >❯</button>
  </div>

  @php
    $nameEn = optional($tour->translations)->firstWhere('locale','en')->name
              ?? $tour->getTranslatedName('en')
              ?? $tour->name;

    $affiliateParams = config('services.viator.affiliate');
  @endphp

  @if (!empty($tour->viator_code))
    <div class="powered-by text-center mt-3">
      <small>
        {{ __('reviews.powered_by') }}
        <a
          href="{{ viator_product_url(
                  $tour->viator_code,
                  $tour->viator_destination_id ?? 821,
                  $tour->viator_city_slug ?? 'La-Fortuna',
                  $tour->viator_slug ?? null,
                  $nameEn,
                  $affiliateParams
              ) }}"
          target="_blank"
          rel="noopener sponsored"
          class="text-decoration-none text-dark fw-semibold"
          title="{{ __('reviews.view_on_viator', ['name' => $nameEn]) }}"
          hreflang="en">
          Viator
        </a>
      </small>
    </div>
  @endif
</div>

@php
  $VIATOR_PRODUCT_DATA = [
    'code' => $tour->viator_code,
    'name' => $tour->getTranslatedName(),
    'id'   => $tour->tour_id,
  ];
@endphp
<script>
  window.VIATOR_PRODUCT_DATA = @json($VIATOR_PRODUCT_DATA);
</script>
