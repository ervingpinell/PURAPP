<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reviews</title>
  @vite(['resources/css/tour-review.css'])
  <style>
    body { margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; }
  </style>
</head>
<body>
  <div class="tour-review-carousel-wrapper">
    <button
      class="carousel-nav carousel-prev"
      type="button"
      aria-label="{{ __('reviews.previous_review') }}"
      data-tour="{{ $tour->tour_id }}">❮</button>

    <div class="tour-review-carousel" id="review-carousel-tour-{{ $tour->tour_id }}">
      <p class="text-center text-muted">{{ __('reviews.loading') }}</p>
    </div>

    <button
      class="carousel-nav carousel-next"
      type="button"
      aria-label="{{ __('reviews.next_review') }}"
      data-tour="{{ $tour->tour_id }}">❯</button>
  </div>

  {{-- 🔗 Enlace a Viator con sponsored + nofollow --}}
  @php
    $nameEn = optional($tour->translations)->firstWhere('locale','en')->name
              ?? $tour->getTranslatedName('en')
              ?? $tour->name;

    $affiliateParams = config('services.viator.affiliate');
  @endphp

@if (!empty($tour->viator_code))
  <div class="powered-by mt-3">
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
        rel="noopener sponsored nofollow"
        class="text-decoration-none text-dark fw-semibold"
        title="{{ __('reviews.view_on_viator', ['name' => $nameEn]) }}"
        hreflang="en">
        Viator
      </a>
    </small>
  </div>
@endif


  <script>
    window.tourId       = {{ $tour->tour_id }};
    window.productCode  = @json($tour->viator_code);

    window.I18N = Object.assign({}, window.I18N, {
      loading_reviews: @json(__('reviews.loading')),
      no_reviews:      @json(__('reviews.no_reviews') ?? 'No reviews available.'),
      see_more:        @json(__('reviews.see_more') ?? 'See more'),
      see_less:        @json(__('reviews.see_less') ?? 'See less'),
      anonymous:       @json(__('reviews.anonymous') ?? 'Anonymous'),
    });
  </script>

  @vite('resources/js/viator/tour-reviews.js')
</body>
</html>
