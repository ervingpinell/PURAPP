{{-- 🌟 Carrusel de reseñas específicas del tour --}}
<div class="col-md-12 my-5">
  {{-- 🧾 Título --}}
  <div class="text-center mb-4">
    <h2 class="fw-bold" style="color: #256d1b">
      {{ __('adminlte::adminlte.what_customers_thinks_about') }}
    </h2>
    <h3 class="text-secondary">
      {{ $tour->getTranslatedName() }}
    </h3>
  </div>

  {{-- 🔁 Carrusel de reseñas --}}
  <div class="tour-review-carousel-wrapper">
    <button
      class="carousel-nav carousel-prev"
      type="button"
      aria-label="{{ __('adminlte::adminlte.previous_review') }}"
      data-tour="{{ $tour->tour_id }}"
    >❮</button>

    <div
      class="tour-review-carousel"
      id="review-carousel-tour-{{ $tour->tour_id }}"
    >
      <p class="text-center text-muted">
        {{ __('adminlte::adminlte.loading_reviews') }}
      </p>
    </div>

    <button
      class="carousel-nav carousel-next"
      type="button"
      aria-label="{{ __('adminlte::adminlte.next_review') }}"
      data-tour="{{ $tour->tour_id }}"
    >❯</button>
  </div>

  {{-- 👣 Créditos Viator --}}
  @php
    // nombre en inglés para slug estable; fallback a traducido/base
    $nameEn = optional($tour->translations)->firstWhere('locale','en')->name
              ?? $tour->getTranslatedName('en')
              ?? $tour->name;

    // params de afiliado opcionales desde config/services.php → ['viator' => ['affiliate' => [...]]]
    $affiliateParams = config('services.viator.affiliate');
  @endphp

  @if (!empty($tour->viator_code))
    <div class="powered-by text-center mt-3">
      <small>
        Powered by
        <a
          href="{{ viator_product_url(
                  $tour->viator_code,
                  $tour->viator_destination_id ?? 821,
                  $tour->viator_city_slug ?? 'La-Fortuna',
                  $tour->viator_slug ?? null,
                  $nameEn,
                  $affiliateParams // ← sin hardcode
              ) }}"
          target="_blank"
          rel="noopener sponsored"
          class="text-decoration-none text-dark fw-semibold"
          title="View {{ $nameEn }} on Viator" hreflang="en">
          Viator
        </a>
      </small>
    </div>
  @endif
</div>

{{-- 🧠 Variables para JS (nombre traducido + code) --}}
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
