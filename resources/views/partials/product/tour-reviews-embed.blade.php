{{-- resources/views/partials/product/tour-reviews-embed.blade.php --}}
<div class="tour-review-carousel-wrapper">
  <button
    class="carousel-nav carousel-prev"
    type="button"
    aria-label="{{ __('reviews.previous_review') }}"
    data-tour="{{ $product->product_id }}"
  >❮</button>

  <div
    class="tour-review-carousel"
    id="review-carousel-tour-{{ $product->product_id }}">
    <p class="text-center text-muted">{{ __('reviews.loading') }}</p>
  </div>

  <button
    class="carousel-nav carousel-next"
    type="button"
    aria-label="{{ __('reviews.next_review') }}"
    data-tour="{{ $product->product_id }}"
  >❯</button>
</div>

<script>
  // Variables mínimas que tu JS usa
  window.VIATOR_PRODUCT_DATA = {
    code: @json($product->viator_code),
    name: @json($product->getTranslatedName()),
    id:   @json($product->product_id),
  };
</script>
