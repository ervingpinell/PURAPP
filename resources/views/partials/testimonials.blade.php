<h2 class="big-title text-center" style="color: var(--primary-dark);">
  {{ __('reviews.what_visitors_say') }}
</h2>

<div id="viator-carousel" class="carousel slide mt-4" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <div class="review-item card shadow-sm border-0 mx-auto w-100">
        <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 300px;">
          <p class="text-muted text-center mb-0">{{ __('reviews.loading') }}</p>
        </div>
      </div>
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#viator-carousel" data-bs-slide="prev" aria-label="{{ __('reviews.previous') }}">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">{{ __('reviews.previous') }}</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#viator-carousel" data-bs-slide="next" aria-label="{{ __('reviews.next') }}">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">{{ __('reviews.next') }}</span>
  </button>
</div>
