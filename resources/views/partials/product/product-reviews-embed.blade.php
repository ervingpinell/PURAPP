{{-- resources/views/partials/product/product-reviews-embed.blade.php --}}
<div class="product-review-carousel-wrapper">
  <button
    class="carousel-nav carousel-prev"
    type="button"
    aria-label="{{ __('reviews.previous_review') }}"
    data-product="{{ $product->product_id }}"
  >❮</button>

  <div
    class="product-review-carousel"
    id="review-carousel-product-{{ $product->product_id }}">
    <p class="text-center text-muted">{{ __('reviews.loading') }}</p>
  </div>

  <button
    class="carousel-nav carousel-next"
    type="button"
    aria-label="{{ __('reviews.next_review') }}"
    data-product="{{ $product->product_id }}"
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
