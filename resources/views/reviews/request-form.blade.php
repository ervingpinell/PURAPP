@extends('layouts.app')

@section('title', __('reviews.public.form_title'))

@section('content')
<div class="review-page">
  <div class="container py-3 py-md-4 py-lg-5">
    <div class="row">

      {{-- Main Column: Review Form (appears first on mobile) --}}
      <div class="col-12 col-lg-6 order-1 order-lg-2 mb-4">
        {{-- Header --}}
        <div class="card header-card shadow-sm mb-3 mb-md-4">
          <div class="card-body text-center py-3 py-md-4">
            <h1 class="h4 h-md-3 mb-2">{{ __('reviews.public.form_heading') }}</h1>
            <p class="text-muted mb-0 small">
              {{ __('reviews.public.form_description') }}
            </p>
          </div>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ __('reviews.public.error_title') }}</strong>
            <ul class="mb-0 mt-2 small">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        {{-- Review Form Card --}}
        <div class="card form-card shadow-sm">
          <div class="card-body p-3 p-md-4">
            <form method="POST" action="{{ route('reviews.request.submit', $rr->token) }}" id="reviewForm">
              @csrf

              {{-- Star Rating --}}
              <div class="form-group mb-4">
                <label class="font-weight-bold mb-3">
                  {{ __('reviews.public.labels.rating') }}
                  <span class="text-danger">*</span>
                </label>
                <div class="star-rating-container text-center">
                  <div class="star-rating" id="starRating">
                    <i class="star far fa-star" data-rating="1"></i>
                    <i class="star far fa-star" data-rating="2"></i>
                    <i class="star far fa-star" data-rating="3"></i>
                    <i class="star far fa-star" data-rating="4"></i>
                    <i class="star far fa-star" data-rating="5"></i>
                  </div>
                  <div class="rating-text mt-2">
                    <span id="ratingText" class="text-muted small">{{ __('reviews.public.select_rating') }}</span>
                  </div>
                  <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating') }}" required>
                </div>
              </div>

              {{-- Title (Required) --}}
              <div class="form-group mb-4">
                <label class="font-weight-bold" for="title">
                  {{ __('reviews.public.labels.title') }}
                  <span class="text-danger">*</span>
                </label>
                <input
                  type="text"
                  name="title"
                  id="title"
                  class="form-control form-control-lg"
                  value="{{ old('title') }}"
                  maxlength="120"
                  required
                  placeholder="{{ __('reviews.public.title_placeholder') }}">
              </div>

              {{-- Review Body --}}
              <div class="form-group mb-4">
                <label class="font-weight-bold" for="body">
                  {{ __('reviews.public.labels.body') }}
                  <span class="text-danger">*</span>
                </label>
                <textarea
                  name="body"
                  id="body"
                  class="form-control"
                  rows="6"
                  required
                  minlength="10"
                  maxlength="1000"
                  placeholder="{{ __('reviews.public.body_placeholder') }}">{{ old('body') }}</textarea>
                <small class="form-text text-muted">
                  {{ __('reviews.public.body_help') }}
                </small>
              </div>

              {{-- Submit Button --}}
              <div class="text-center mt-4 pt-2">
                <button type="submit" class="btn btn-success btn-lg px-5 submit-button">
                  <i class="fas fa-paper-plane mr-2"></i>
                  {{ __('reviews.public.labels.submit') }}
                </button>
              </div>
            </form>
          </div>
        </div>

        {{-- Footer Note --}}
        <div class="text-center mt-3 mt-md-4">
          <small class="text-muted">
            <i class="fas fa-shield-alt mr-1"></i>
            {{ __('reviews.public.privacy_note') }}
          </small>
        </div>
      </div>

      {{-- Sidebar Column: Tour & Booking Info (appears second on mobile) --}}
      <div class="col-12 col-lg-6 order-2 order-lg-1">
        {{-- Tour Card --}}
        @if($product)
        <div class="card info-card shadow-sm mb-3 mb-md-4">
          @if($product->cover_image_url)
          <div class="tour-image-wrapper">
            <img src="{{ $product->cover_image_url }}"
                 alt="{{ $product->name }}"
                 class="card-img-top">
          </div>
          @endif
          <div class="card-body p-3 p-md-4">
            <h5 class="card-title mb-3">
              <i class="fas fa-map-marked-alt text-success mr-2"></i>
              {{ $product->name }}
            </h5>

            @if($rr->booking)
            <div class="booking-details">
              @if($rr->booking->detail && $rr->booking->detail->product_date)
              <div class="detail-item">
                <div class="detail-icon">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="detail-content">
                  <span class="detail-label">{{ __('reviews.public.booking_date') }}</span>
                  <span class="detail-value">{{ $rr->booking->detail->product_date->format('d/m/Y') }}</span>
                </div>
              </div>
              @endif

              @if($rr->booking->detail && $rr->booking->detail->total_pax > 0)
              <div class="detail-item">
                <div class="detail-icon">
                  <i class="fas fa-users"></i>
                </div>
                <div class="detail-content">
                  <span class="detail-label">{{ __('reviews.public.participants') }}</span>
                  <span class="detail-value">
                    @if($rr->booking->detail->adults_quantity > 0)
                      {{ $rr->booking->detail->adults_quantity }} {{ __('reviews.public.adults') }}
                    @endif
                    @if($rr->booking->detail->kids_quantity > 0)
                      @if($rr->booking->detail->adults_quantity > 0) + @endif
                      {{ $rr->booking->detail->kids_quantity }} {{ __('reviews.public.children') }}
                    @endif
                  </span>
                </div>
              </div>
              @endif

              <div class="detail-item">
                <div class="detail-icon">
                  <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="detail-content">
                  <span class="detail-label">{{ __('reviews.public.booking_code') }}</span>
                  <span class="detail-value font-weight-bold">{{ $rr->booking->booking_reference }}</span>
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
        @endif

        {{-- Help Card --}}
        <div class="card help-card shadow-sm">
          <div class="card-body p-3 p-md-4">
            <h6 class="mb-3">
              <i class="fas fa-lightbulb text-warning mr-2"></i>
              {{ __('reviews.public.help_title') }}
            </h6>
            <p class="small text-muted mb-0">
              {{ __('reviews.public.help_text') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Page Background */
  .review-page {
    background: #f8f9fa;
    min-height: 100vh;
  }

  /* Cards */
  .info-card, .help-card, .header-card, .form-card {
    border: none;
    border-radius: 1rem;
    overflow: hidden;
  }

  .tour-image-wrapper {
    position: relative;
    height: 220px;
    overflow: hidden;
  }

  .tour-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

/* Star Rating */
  .star-rating-container {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 1rem;
    margin-bottom: 0.5rem;
  }

  .star-rating {
    display: inline-flex;
    gap: 0.5rem;
    font-size: 2rem;
    cursor: pointer;
    user-select: none;
  }

  .star {
    color: #ddd;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .star:hover,
  .star.hover {
    color: #ffd700;
    transform: scale(1.2) rotate(-10deg);
  }

  .star.selected {
    color: #ffd700;
    animation: starPop 0.3s ease;
  }

  @keyframes starPop {
    0% { transform: scale(1); }
    50% { transform: scale(1.3) rotate(-15deg); }
    100% { transform: scale(1); }
  }

  .rating-text {
    font-size: 1.1rem;
    font-weight: 500;
    min-height: 1.5rem;
  }

  .rating-excellent { color: #28a745; }
  .rating-good { color: #20c997; }
  .rating-average { color: #ffc107; }
  .rating-below-average { color: #fd7e14; }
  .rating-poor { color: #dc3545; }

  /* Booking Details */
  .booking-details {
    background: #f8f9fa;
    border-radius: 1rem;
    padding: 1.25rem;
  }

  .detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
  }

  .detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .detail-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #28a745;
    color: white;
    border-radius: 0.75rem;
    flex-shrink: 0;
    font-size: 1.1rem;
  }

  .detail-content {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }

  .detail-label {
    color: #6c757d;
    font-size: 0.9rem;
  }

  .detail-value {
    font-weight: 600;
    color: #212529;
  }

  /* Form Controls */
  .form-control {
    border-radius: 0.75rem;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
    font-size: 1rem;
    padding: 0.75rem 1rem;
  }

  .form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.15);
  }

  .form-control-lg {
    padding: 1rem 1.25rem;
    font-size: 1.1rem;
  }

  /* Submit Button */
  .submit-button {
    border-radius: 2rem;
    transition: all 0.3s ease;
    font-weight: 600;
    background: #28a745;
    border: none;
    padding: 1rem 3rem;
    font-size: 1.1rem;
  }

  .submit-button:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(40, 167, 69, 0.3);
  }

  .submit-button:active {
    transform: translateY(0);
  }

  /* Help Card */
  .help-card {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
  }

  /* Header Card */
  .header-card {
    background: #ffffff;
  }

  /* Shadows */
  .shadow-sm {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
  }

  /* Responsive Adjustments */
  @media (max-width: 991px) {
    .star-rating {
      font-size: 2.5rem;
      gap: 0.5rem;
    }

    .tour-image-wrapper {
      height: 200px;
    }
  }

  @media (max-width: 576px) {
    .star-rating {
      font-size: 2rem;
      gap: 0.35rem;
    }

    .detail-icon {
      width: 35px;
      height: 35px;
      font-size: 1rem;
    }

    .detail-label,
    .detail-value {
      font-size: 0.85rem;
    }

    .submit-button {
      width: 100%;
      padding: 0.875rem 2rem;
      font-size: 1rem;
    }

    .booking-details {
      padding: 1rem;
    }
  }

  @media (max-width: 375px) {
    .star-rating {
      font-size: 1.75rem;
      gap: 0.3rem;
    }

    h1.h4 {
      font-size: 1.25rem !important;
    }
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const stars = document.querySelectorAll('.star');
  const ratingInput = document.getElementById('ratingInput');
  const ratingText = document.getElementById('ratingText');

  const ratingLabels = {
    1: '{{ __("reviews.public.rating_poor") ?? "Poor" }}',
    2: '{{ __("reviews.public.rating_below_average") ?? "Below Average" }}',
    3: '{{ __("reviews.public.rating_average") ?? "Average" }}',
    4: '{{ __("reviews.public.rating_good") ?? "Good" }}',
    5: '{{ __("reviews.public.rating_excellent") ?? "Excellent" }}'
  };

  const ratingClasses = {
    1: 'rating-poor',
    2: 'rating-below-average',
    3: 'rating-average',
    4: 'rating-good',
    5: 'rating-excellent'
  };

  // Set initial rating if exists
  const initialRating = ratingInput.value;
  if (initialRating) {
    setRating(parseInt(initialRating));
  }

  // Click handler
  stars.forEach(star => {
    star.addEventListener('click', function() {
      const rating = parseInt(this.getAttribute('data-rating'));
      setRating(rating);
      ratingInput.value = rating;
    });

    // Hover effect
    star.addEventListener('mouseenter', function() {
      const rating = parseInt(this.getAttribute('data-rating'));
      highlightStars(rating);
    });
  });

  // Reset on mouse leave
  document.getElementById('starRating').addEventListener('mouseleave', function() {
    const currentRating = parseInt(ratingInput.value) || 0;
    highlightStars(currentRating);
  });

  function setRating(rating) {
    // Update stars
    stars.forEach(star => {
      const starRating = parseInt(star.getAttribute('data-rating'));
      if (starRating <= rating) {
        star.classList.remove('far');
        star.classList.add('fas', 'selected');
      } else {
        star.classList.remove('fas', 'selected');
        star.classList.add('far');
      }
    });

    // Update text
    ratingText.textContent = ratingLabels[rating];
    // Remove old rating classes
    ratingText.classList.remove('rating-poor', 'rating-below-average', 'rating-average', 'rating-good', 'rating-excellent');
    ratingText.classList.add('font-weight-bold', ratingClasses[rating]);
  }

  function highlightStars(rating) {
    stars.forEach(star => {
      const starRating = parseInt(star.getAttribute('data-rating'));
      if (starRating <= rating) {
        star.classList.add('hover');
      } else {
        star.classList.remove('hover');
      }
    });
  }

  // Form validation
  document.getElementById('reviewForm').addEventListener('submit', function(e) {
    if (!ratingInput.value) {
      e.preventDefault();
      alert('{{ __("reviews.public.please_select_rating") ?? "Please select a rating" }}');
      document.getElementById('starRating').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const stars = document.querySelectorAll('.star');
  const ratingInput = document.getElementById('ratingInput');
  const ratingText = document.getElementById('ratingText');

  const ratingLabels = {
    1: '{{ __("reviews.public.rating_poor") ?? "Poor" }}',
    2: '{{ __("reviews.public.rating_below_average") ?? "Below Average" }}',
    3: '{{ __("reviews.public.rating_average") ?? "Average" }}',
    4: '{{ __("reviews.public.rating_good") ?? "Good" }}',
    5: '{{ __("reviews.public.rating_excellent") ?? "Excellent" }}'
  };

  const ratingClasses = {
    1: 'rating-poor',
    2: 'rating-below-average',
    3: 'rating-average',
    4: 'rating-good',
    5: 'rating-excellent'
  };

  // Set initial rating if exists
  const initialRating = ratingInput.value;
  if (initialRating) {
    setRating(parseInt(initialRating));
  }

  // Click handler
  stars.forEach(star => {
    star.addEventListener('click', function() {
      const rating = parseInt(this.getAttribute('data-rating'));
      setRating(rating);
      ratingInput.value = rating;
    });

    // Hover effect
    star.addEventListener('mouseenter', function() {
      const rating = parseInt(this.getAttribute('data-rating'));
      highlightStars(rating);
    });
  });

  // Reset on mouse leave
  document.getElementById('starRating').addEventListener('mouseleave', function() {
    const currentRating = parseInt(ratingInput.value) || 0;
    highlightStars(currentRating);
  });

  function setRating(rating) {
    // Update stars
    stars.forEach(star => {
      const starRating = parseInt(star.getAttribute('data-rating'));
      if (starRating <= rating) {
        star.classList.remove('far');
        star.classList.add('fas', 'selected');
      } else {
        star.classList.remove('fas', 'selected');
        star.classList.add('far');
      }
    });

    // Update text
    ratingText.textContent = ratingLabels[rating];
    // Remove old rating classes
    ratingText.classList.remove('rating-poor', 'rating-below-average', 'rating-average', 'rating-good', 'rating-excellent');
    ratingText.classList.add('fw-bold', ratingClasses[rating]);
  }

  function highlightStars(rating) {
    stars.forEach(star => {
      const starRating = parseInt(star.getAttribute('data-rating'));
      if (starRating <= rating) {
        star.classList.add('hover');
      } else {
        star.classList.remove('hover');
      }
    });
  }

  // Form validation
  document.getElementById('reviewForm').addEventListener('submit', function(e) {
    if (!ratingInput.value) {
      e.preventDefault();
      alert('{{ __("reviews.public.please_select_rating") ?? "Please select a rating" }}');
      document.getElementById('starRating').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
});
</script>
@endpush
