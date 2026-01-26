@extends('layouts.app')

@section('title', __('payment.already_paid_title') ?? 'Payment Already Completed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    {{-- Success Icon --}}
                    <div class="mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <i class="fas fa-check-circle text-success" style="font-size: 3.5rem;"></i>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h2 class="mb-3 fw-bold">
                        {{ __('payment.already_paid_title') ?? 'Payment Already Completed' }}
                    </h2>

                    {{-- Message --}}
                    <p class="text-muted mb-4">
                        {{ __('payment.already_paid_message') ?? 'This booking has already been paid. No further payment is required.' }}
                    </p>

                    {{-- Booking Details --}}
                    <div class="bg-light rounded p-4 mb-4">
                        <div class="row g-3">
                            <div class="col-6 text-start">
                                <small class="text-muted d-block">{{ __('m_bookings.bookings.booking_reference') ?? 'Booking Reference' }}</small>
                                <strong>{{ $booking->booking_reference }}</strong>
                            </div>
                            <div class="col-6 text-start">
                                <small class="text-muted d-block">{{ __('payment.status') ?? 'Status' }}</small>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ __('payment.paid') ?? 'Paid' }}
                                </span>
                            </div>
                            @if($booking->product)
                            <div class="col-12 text-start">
                                <small class="text-muted d-block">{{ __('m_bookings.bookings.tour') ?? 'Tour' }}</small>
                                <strong>{{ $booking->product->getTranslatedName() ?? $booking->product->name }}</strong>
                            </div>
                            @endif
                            @if($booking->detail && $booking->detail->tour_date)
                            <div class="col-12 text-start">
                                <small class="text-muted d-block">{{ __('m_bookings.bookings.tour_date') ?? 'Tour Date' }}</small>
                                <strong>{{ \Carbon\Carbon::parse($booking->detail->tour_date)->format('l, F d, Y') }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        @auth
                        <a href="{{ route('my-bookings') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>
                            {{ __('m_bookings.bookings.my_bookings') ?? 'My Bookings' }}
                        </a>
                        @endauth

                        <a href="{{ route(app()->getLocale() . '.home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>
                            {{ __('payment.back_to_home') ?? 'Back to Home' }}
                        </a>
                    </div>

                    {{-- Help Text --}}
                    <div class="mt-4 pt-4 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('payment.already_paid_help') ?? 'If you have any questions about your booking, please contact our support team.' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 16px;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }

    .badge {
        padding: 0.5rem 1rem;
        font-weight: 500;
    }
</style>
@endpush
@endsection