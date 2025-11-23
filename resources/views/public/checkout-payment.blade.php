{{-- resources/views/public/payment.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment')

{{-- Para ocultar widgets en el paso de pago --}}
@section('body_class', 'payment-page')

@push('styles')
  @vite(entrypoints: 'resources/css/checkout.css')
@endpush

@section('content')
<div class="payment-container">
    <div class="progress-steps">
        <div class="step completed">
            <div class="num">✓</div><span>{{ __('payment.checkout') }}</span>
        </div>
        <div class="step-connector"></div>
        <div class="step active">
            <div class="num">2</div><span>{{ __('payment.payment') }}</span>
        </div>
        <div class="step-connector"></div>
        <div class="step">
            <div class="num">3</div><span>{{ __('payment.confirmation') }}</span>
        </div>
    </div>

    {{-- Countdown Timer --}}
    @include('components.payment-countdown')

    <div class="payment-grid">
        {{-- Left: Payment Form --}}
        <div class="payment-panel">
            <div class="panel-title">
                <i class="fas fa-credit-card"></i>
                {{ __('payment.payment_information') }}
            </div>
            <div class="panel-subtitle">
                <i class="fas fa-lock"></i>
                {{ __('payment.payment_secure_encrypted') }}
            </div>

            <div class="secure-badge">
                <i class="fas fa-shield-check"></i>
                <div>
                    <strong>{{ __('payment.secure_payment') }}</strong>
                    <small>{{ __('payment.powered_by_stripe') }}</small>
                </div>
            </div>

            <form id="payment-form">
                @csrf
                <div class="stripe-element-container">
                    <div id="payment-element">
                        <!-- Stripe Elements will be inserted here -->
                    </div>
                </div>

                <div id="payment-message" class="payment-message"></div>

                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('public.checkout.show') }}" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i>{{ __('payment.back') }}
                    </a>
                    <button type="submit" id="submit-button" class="btn btn-pay">
                        <span id="button-text">
                            {{ __('payment.pay') }} ${{ number_format($total, 2) }}
                        </span>
                        <span id="spinner" class="spinner" style="display:none"></span>
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    {{ __('payment.terms_agreement') }}
                </small>
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="summary-panel">
            <div class="summary-header">
                <h3>{{ __('payment.order_summary') }}</h3>
            </div>

            @foreach($items as $item)
            <div class="booking-item">
                <div class="booking-name">
                    {{ $item['tour']->getTranslatedName() ?? $item['tour']->name }}
                </div>
                <div class="booking-details">
                    <span>
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($item['tour_date'])->format('M d, Y') }}
                    </span>
                    <span>
                        <i class="fas fa-user"></i>
                        {{ collect($item['categories'])->sum('quantity') }}
                        {{ __('payment.participants') }}
                    </span>
                </div>
            </div>
            @endforeach

            <div class="totals-section">
                <div class="total-row">
                    <span>{{ __('payment.subtotal') }}</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                <div class="total-row final">
                    <span>{{ __('payment.total') }}</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="mt-3 text-center">
                <small class="text-muted">
                    <i class="fas fa-check-circle text-success"></i>
                    {{ __('payment.free_cancellation') }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const stripe = Stripe('{{ $stripeKey }}');

        let elements;
        let clientSecret;

        // Initialize payment
        try {
            const response = await fetch('{{ route("payment.initiate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                // No need to send booking_ids anymore - using cart snapshot from session
                body: JSON.stringify({})
            });

            const data = await response.json();

            if (!data.success) {
                showMessage(data.message || 'Failed to initialize payment');
                return;
            }

            clientSecret = data.client_secret;

            // Create Stripe Elements
            const appearance = {
                theme: 'stripe',
                variables: {
                    colorPrimary: getComputedStyle(document.documentElement)
                        .getPropertyValue('--primary-color').trim() || '#0066cc',
                    colorBackground: '#f9fafb',
                    colorText: '#1f2937',
                    colorDanger: '#dc2626',
                    fontFamily: 'system-ui, -apple-system, sans-serif',
                    spacingUnit: '4px',
                    borderRadius: '8px'
                }
            };

            elements = stripe.elements({
                clientSecret,
                appearance
            });
            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

        } catch (error) {
            console.error('Payment initialization error:', error);
            showMessage('Failed to initialize payment. Please try again.');
        }

        // Handle form submission
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!clientSecret) {
                showMessage('Payment not initialized. Please refresh the page.');
                return;
            }

            setLoading(true);

            try {
                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: '{{ route("payment.confirm") }}?payment_intent_id='
                            + clientSecret.split('_secret_')[0],
                    },
                });

                if (error) {
                    showMessage(error.message);
                    setLoading(false);
                }
                // If successful, Stripe will redirect to return_url
            } catch (error) {
                console.error('Payment error:', error);
                showMessage('An unexpected error occurred. Please try again.');
                setLoading(false);
            }
        });

        function setLoading(isLoading) {
            submitButton.disabled = isLoading;
            if (isLoading) {
                buttonText.style.display = 'none';
                spinner.style.display = 'inline-block';
            } else {
                buttonText.style.display = 'inline';
                spinner.style.display = 'none';
            }
        }

        function showMessage(message) {
            const messageDiv = document.getElementById('payment-message');
            messageDiv.textContent = message;
            messageDiv.classList.add('show');

            setTimeout(() => {
                messageDiv.classList.remove('show');
            }, 5000);
        }
    });
</script>
@endpush
