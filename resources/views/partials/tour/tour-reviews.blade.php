{{-- 🌟 Carrusel de reseñas específicas del tour --}}
<div class="col-md-12 my-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold text-success">
      {{ __('adminlte::adminlte.what_customers_thinks_about') }}
    </h2>
    <h3 class="text-secondary">
      {{ $tour->name }}
    </h3>
  </div>

  <div class="tour-review-carousel-wrapper">
    <button class="carousel-nav carousel-prev" data-tour="{{ $tour->tour_id }}">❮</button>
    <div class="tour-review-carousel" id="review-carousel-tour-{{ $tour->tour_id }}">
      <p class="text-center text-muted">
        {{ __('adminlte::adminlte.loading_reviews') }}
      </p>
    </div>
    <button class="carousel-nav carousel-next" data-tour="{{ $tour->tour_id }}">❯</button>
  </div>

  <div class="powered-by text-center mt-3">
  <small>
    Powered by
    <a href="https://www.viator.com/tours/tour/d1-{{ $tour->viator_code }}?pid=P00137209"
       target="_blank" rel="noopener"
       class="text-decoration-none text-dark fw-semibold">
      Viator
    </a>
  </small>
</div>

</div>
