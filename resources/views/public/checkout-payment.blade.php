@extends('layouts.app')

@section('title', ('Payment'))

@push('styles')
<style>
    :root {
        --p: var(--primary-color);
        --p2: var(--primary-dark);
        --ok: var(--primary-color);
        --hdr-dark: var(--primary-header);
        --danger: var(--primary-red);
        --g50: #f9fafb;
        --g100: #f3f4f6;
        --g200: #e5e7eb;
        --g300: #d1d5db;
        --g400: #9ca3af;
        --g600: #4b5563;
        --g700: #374151;
        --g800: #1f2937;
        --g900: #111827
    }

    .payment-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem
    }

    .progress-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-bottom: 3rem;
        padding: 1.5rem;
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px #0000001a
    }

    .step {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1.5rem;
        border-radius: .75rem;
        background: var(--g100);
        color: var(--g600);
        font-weight: 600;
        transition: .3s
    }

    .step .num {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: #ffffff33;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700
    }

    .step.active {
        background: linear-gradient(135deg, var(--p), var(--p2));
        color: #fff;
        box-shadow: 0 4px 6px -1px color-mix(in srgb, var(--p) 30%, transparent)
    }

    .step.completed {
        background: var(--ok);
        color: #fff
    }

    .step-connector {
        width: 3rem;
        height: 2px;
        background: var(--g300)
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        align-items: start
    }

    .payment-panel,
    .summary-panel {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px #0000001a;
        padding: 2rem
    }

    .panel-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--g900);
        margin-bottom: .5rem;
        display: flex;
        align-items: center;
        gap: .75rem
    }

    .panel-subtitle {
        color: var(--g600);
        font-size: .95rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: .5rem
    }

    .panel-subtitle i {
        color: var(--ok)
    }

    /* Stripe Elements */
    .stripe-element-container {
        margin: 2rem 0
    }

    #payment-element {
        padding: 1rem;
        background: var(--g50);
        border: 2px solid var(--g200);
        border-radius: .75rem;
        min-height: 200px
    }

    #payment-element.StripeElement--focus {
        border-color: var(--p);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--p) 20%, transparent)
    }

    #payment-element.StripeElement--invalid {
        border-color: var(--danger)
    }

    .payment-message {
        color: var(--danger);
        font-size: .9rem;
        margin-top: 1rem;
        padding: 1rem;
        background: #fee;
        border-left: 4px solid var(--danger);
        border-radius: .5rem;
        display: none
    }

    .payment-message.show {
        display: block
    }

    .secure-badge {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1px solid var(--ok);
        padding: 1rem;
        border-radius: .75rem;
        margin: 1.5rem 0;
        display: flex;
        align-items: center;
        gap: .75rem
    }

    .secure-badge i {
        color: var(--ok);
        font-size: 1.5rem
    }

    .secure-badge div {
        flex: 1
    }

    .secure-badge strong {
        display: block;
        color: var(--g900);
        margin-bottom: .25rem
    }

    .secure-badge small {
        color: var(--g700)
    }

    /* Summary */
    .summary-panel {
        position: sticky;
        top: 2rem;
        border: 1px solid var(--g200)
    }

    .summary-header {
        background: linear-gradient(135deg, var(--hdr-dark), #111);
        color: #fff;
        padding: 1rem 1.25rem;
        margin: -2rem -2rem 1.5rem;
        border-radius: 1rem 1rem 0 0
    }

    .summary-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0
    }

    .booking-item {
        padding: 1rem;
        background: var(--g50);
        border-radius: .75rem;
        margin-bottom: .75rem;
        border: 1px solid var(--g200)
    }

    .booking-name {
        font-weight: 600;
        color: var(--g900);
        margin-bottom: .5rem
    }

    .booking-details {
        display: flex;
        flex-direction: column;
        gap: .35rem;
        font-size: .86rem;
        color: var(--g600)
    }

    .booking-details span {
        display: flex;
        align-items: center;
        gap: .5rem
    }

    .booking-details i {
        color: var(--p);
        width: 1rem
    }

    .totals-section {
        background: var(--g50);
        padding: 1rem;
        border-radius: .75rem;
        margin-top: 1.5rem
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .5rem;
        color: var(--g700)
    }

    .total-row.final {
        padding-top: .75rem;
        margin-top: .75rem;
        border-top: 2px solid var(--g300);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--g900)
    }

    /* Buttons */
    .btn {
        padding: .9rem 1.25rem;
        border-radius: .65rem;
        font-weight: 600;
        border: 0;
        cursor: pointer;
        transition: .3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        text-decoration: none;
        font-size: 1rem
    }

    .btn-back {
        background: #fff;
        color: var(--g700);
        border: 2px solid var(--g300)
    }

    .btn-back:hover {
        background: var(--g50);
        border-color: var(--g400)
    }

    .btn-pay {
        flex: 1;
        background: linear-gradient(135deg, var(--p), var(--p2));
        color: #fff;
        box-shadow: 0 4px 6px -1px color-mix(in srgb, var(--p) 30%, transparent)
    }

    .btn-pay:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px color-mix(in srgb, var(--p) 40%, transparent)
    }

    .btn-pay:disabled {
        background: var(--g300);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
        opacity: .6
    }

    .spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #ffffff33;
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    /* Responsive */
    @media (max-width:1024px) {
        .payment-grid {
            grid-template-columns: 1fr
        }

        .summary-panel {
            position: relative;
            top: 0
        }
    }

    @media (max-width:768px) {
        .progress-steps {
            flex-direction: column;
            gap: .75rem
        }

        .step-connector {
            display: none
        }

        .step {
            width: 100%
        }

        .btn {
            width: 100%
        }
    }
</style>
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
            <div class="panel-title"><i class="fas fa-credit-card"></i>{{ __('payment.payment_information') }}</div>
            <div class="panel-subtitle"><i class="fas fa-lock"></i>{{ __('payment.payment_secure_encrypted') }}</div>

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
                        <span id="button-text">{{ __('payment.pay') }} ${{ number_format($total, 2) }}</span>
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
                <div class="booking-name">{{ $item['tour']->getTranslatedName() ?? $item['tour']->name }}</div>
                <div class="booking-details">
                    <span><i class="far fa-calendar-alt"></i>{{ \Carbon\Carbon::parse($item['tour_date'])->format('M d, Y') }}</span>
                    <span><i class="fas fa-user"></i>{{ collect($item['categories'])->sum('quantity') }} {{ __('payment.participants') }}</span>
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
                    colorPrimary: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#0066cc',
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
                const {
                    error
                } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: '{{ route("payment.confirm") }}?payment_intent_id=' + clientSecret.split('_secret_')[0],
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