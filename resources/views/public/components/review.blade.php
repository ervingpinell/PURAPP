<div class="tour-reviews mt-5">
    <h2 class="mb-3">Reseñas del tour</h2>
    <div id="product-reviews-container">
        <p>Cargando reseñas...</p>
    </div>

    <script>
        window.VIATOR_PRODUCT_CODE = "{{ $productCode }}";
    </script>

    @vite([
        'resources/js/viator/render-reviews.js',
        'resources/js/viator/product-reviews.js',
    ])
</div>
