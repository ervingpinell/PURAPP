{{-- resources/views/public/payment-confirmation.blade.php --}}
@extends('layouts.app')

@section('title', __('payment.confirmation'))

{{-- ocultar widgets + estilos de checkout/payment --}}
@section('body_class', 'payment-page confirmation-page')

@push('styles')
  @vite(entrypoints: 'resources/css/checkout.css')
@endpush

@section('content')
  <div class="confirmation-container py-4">
    <div class="confirmation-card">
      {{-- Icono de éxito --}}
      <div class="success-icon">
        <i class="fas fa-check"></i>
      </div>

      {{-- Título principal --}}
      <h1 class="confirmation-title">
        {{ __('payment.payment_successful') }}
      </h1>

      <p class="confirmation-subtitle">
        {{ __('payment.booking_confirmed') }}
      </p>

      {{-- Referencia de reserva --}}
      @if (isset($booking))
        @php
          $reference = $booking->booking_reference
            ?? $booking->booking_id
            ?? null;
        @endphp

        <div class="booking-reference mt-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <span class="booking-reference-label">
                {{ __('payment.booking_reference') }}
              </span>
              <div class="booking-reference-code">
                {{ $reference ?: '—' }}
              </div>
            </div>

            @if (!empty($booking->created_at))
              <div class="text-right small text-muted">
                <div>{{ $booking->created_at->format('d-M-Y') }}</div>
                <div>{{ $booking->created_at->format('H:i') }}</div>
              </div>
            @endif
          </div>
        </div>
      @endif

      {{-- Qué pasa ahora / siguientes pasos --}}
      <div class="next-steps">
        <h3>{{ __('payment.what_happens_next') }}</h3>

        <ul class="mb-0">
          <li>
            <i class="fas fa-check-circle"></i>
            <span>{{ __('payment.next_step_confirmed') }}</span>
          </li>
          <li>
            <i class="fas fa-envelope-open-text"></i>
            <span>{{ __('payment.next_step_email') }}</span>
          </li>
          <li>
            <i class="fas fa-calendar-check"></i>
            <span>{{ __('payment.next_step_manage') }}</span>
          </li>
          <li>
            <i class="fas fa-headset"></i>
            <span>{{ __('payment.next_step_support') }}</span>
          </li>
        </ul>
      </div>

      {{-- Botones inferiores --}}
      <div class="mt-4 d-flex flex-wrap justify-content-center">
            <a href="{{ route('my-bookings') }}" class="btn btn-primary">
          <i class="fas fa-receipt"></i>
          {{ __('payment.view_my_bookings') }}
        </a>

            <a href="{{ route(app()->getLocale() . '.home') }}" class="btn btn-secondary">
          <i class="fas fa-home"></i>
          {{ __('payment.back_to_home') }}
        </a>
      </div>
    </div>
  </div>
@endsection
