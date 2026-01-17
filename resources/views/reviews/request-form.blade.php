@extends('layouts.app')

@section('title', __('reviews.public.form_title'))

@section('content')
<div class="review-page">
  <div class="container py-3 py-md-4 py-lg-5">
    <div class="row g-3 g-md-4">

      {{-- Main Column: Review Form (appears first on mobile) --}}
      <div class="col-12 col-lg-6 order-1 order-lg-2">
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
              <div class="form-group mb-3 mb-md-4">
                <label class="font-weight-bold mb-2 mb-md-3">
                  {{ __('reviews.public.labels.rating') }}
                  <span class="text-danger">*</span>
                </label>
                <div class="text-center">
                  <select name="rating" id="ratingInput" class="form-control text-center" style="max-width: 200px; margin: 0 auto; font-size: 1.2rem;" required>
                    <option value="" disabled {{ old('rating') ? '' : 'selected' }}>-- {{ __('reviews.public.labels.rating') }} --</option>
                    @foreach(range(5, 1) as $rating)
                      <option value="{{ $rating }}" {{ old('rating') == $rating ? 'selected' : '' }}>
                        {{ $rating }} ⭐
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- Title (Required) --}}
              <div class="form-group mb-3 mb-md-4">
                <label class="font-weight-bold" for="title">
                  {{ __('reviews.public.labels.title') }}
                  <span class="text-danger">*</span>
                </label>
                <input
                  type="text"
                  name="title"
                  id="title"
                  class="form-control"
                  value="{{ old('title') }}"
                  maxlength="120"
                  required
                  placeholder="{{ __('reviews.public.title_placeholder') }}">
              </div>

              {{-- Review Body --}}
              <div class="form-group mb-3 mb-md-4">
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
              <div class="text-center mt-3 mt-md-4">
                <button type="submit" class="btn btn-success btn-lg px-4 px-md-5 w-100 w-sm-auto">
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
        @if($tour)
        <div class="card info-card shadow-sm mb-3 mb-md-4">
          @if($tour->cover_image_url)
          <div class="tour-image-wrapper">
            <img src="{{ $tour->cover_image_url }}"
                 alt="{{ $tour->name }}"
                 class="card-img-top">
          </div>
          @endif
          <div class="card-body p-3">
            <h5 class="card-title mb-3 h6 h-md-5">
              <i class="fas fa-map-marked-alt text-success mr-2"></i>
              {{ $tour->name }}
            </h5>

            @if($rr->booking)
            <div class="booking-details">
              @if($rr->booking->detail && $rr->booking->detail->tour_date)
              <div class="detail-item">
                <i class="fas fa-calendar-alt text-muted"></i>
                <span class="label">{{ __('reviews.public.booking_date') }}:</span>
                <span class="value">{{ $rr->booking->detail->tour_date->format('d/m/Y') }}</span>
              </div>
              @endif

              @if($rr->booking->detail && $rr->booking->detail->total_pax > 0)
              <div class="detail-item">
                <i class="fas fa-users text-muted"></i>
                <span class="label">{{ __('reviews.public.participants') }}:</span>
                <span class="value">
                  @if($rr->booking->detail->adults_quantity > 0)
                    {{ $rr->booking->detail->adults_quantity }} {{ __('reviews.public.adults') }}
                  @endif
                  @if($rr->booking->detail->kids_quantity > 0)
                    @if($rr->booking->detail->adults_quantity > 0) + @endif
                    {{ $rr->booking->detail->kids_quantity }} {{ __('reviews.public.children') }}
                  @endif
                </span>
              </div>
              @endif

              <div class="detail-item">
                <i class="fas fa-ticket-alt text-muted"></i>
                <span class="label">{{ __('reviews.public.booking_code') }}:</span>
                <span class="value font-weight-bold">{{ $rr->booking->booking_reference }}</span>
              </div>
            </div>
            @endif
          </div>
        </div>
        @endif

        {{-- Help Card --}}
        <div class="card help-card shadow-sm">
          <div class="card-body p-3">
            <h6 class="mb-2 mb-md-3">
              <i class="fas fa-info-circle text-info mr-2"></i>
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

@section('css')
<style>
  /* Page Background */
  .review-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
  }

  /* Cards */
  .info-card, .help-card, .header-card, .form-card {
    border: none;
    border-radius: 0.75rem;
    overflow: hidden;
  }

  .tour-image-wrapper {
    position: relative;
    height: 180px;
    overflow: hidden;
  }

  .tour-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Booking Details */
  .booking-details {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 0.75rem;
  }

  .detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0;
    border-bottom: 1px solid #e9ecef;
    font-size: 0.875rem;
  }

  .detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .detail-item i {
    width: 18px;
    text-align: center;
    flex-shrink: 0;
  }

  .detail-item .label {
    color: #6c757d;
    white-space: nowrap;
  }

  .detail-item .value {
    margin-left: auto;
    font-weight: 500;
    text-align: right;
  }



  /* Form Controls */
  .form-control {
    border-radius: 0.5rem;
    border: 2px solid #e0e0e0;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    font-size: 1rem;
  }

  .form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
  }

  /* Button */
  .btn-success {
    border-radius: 2rem;
    transition: all 0.2s ease;
    font-weight: 600;
  }

  .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(40, 167, 69, 0.3);
  }

  /* Shadows */
  .shadow-sm {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
  }

  /* Responsive Adjustments */
  @media (min-width: 576px) {
    .tour-image-wrapper {
      height: 200px;
    }
  }

  @media (min-width: 992px) {
    .tour-image-wrapper {
      height: 220px;
    }

    .booking-details {
      padding: 1rem;
    }

    .detail-item {
      padding: 0.5rem 0;
    }
  }

  @media (max-width: 991px) {
    .star-rating {
      font-size: 2.2rem;
      gap: 0.35rem;
    }
  }

  @media (max-width: 576px) {
    .star-rating {
      font-size: 2rem;
      gap: 0.3rem;
    }

    .btn-lg {
      font-size: 1rem;
      padding: 0.75rem 1.5rem;
    }

    .detail-item {
      font-size: 0.8rem;
    }

    .detail-item i {
      width: 16px;
      font-size: 0.85rem;
    }
  }

  @media (max-width: 375px) {
    .star-rating {
      font-size: 1.75rem;
      gap: 0.25rem;
    }

    h1.h4 {
      font-size: 1.25rem !important;
    }

    .card-body {
      padding: 0.75rem !important;
    }
  }
</style>
@endsection
