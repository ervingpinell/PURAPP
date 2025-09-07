{{-- resources/views/partials/tour/tour-reviews-embed.blade.php --}}
<div class="tour-review-carousel-wrapper">
  <button
    class="carousel-nav carousel-prev"
    type="button"
    aria-label="{{ __('reviews.previous_review') }}"
    data-tour="{{ $tour->tour_id }}"
  >❮</button>

  <div
    class="tour-review-carousel"
    id="review-carousel-tour-{{ $tour->tour_id }}">
    <p class="text-center text-muted">{{ __('reviews.loading') }}</p>
  </div>

  <button
    class="carousel-nav carousel-next"
    type="button"
    aria-label="{{ __('reviews.next_review') }}"
    data-tour="{{ $tour->tour_id }}"
  >❯</button>
</div>

<script>
  // Variables mínimas que tu JS usa
  window.VIATOR_PRODUCT_DATA = {
    code: @json($tour->viator_code),
    name: @json($tour->getTranslatedName()),
    id:   @json($tour->tour_id),
  };
</script>
