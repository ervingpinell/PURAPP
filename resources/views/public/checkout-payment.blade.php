{{-- resources/views/public/payment.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment')

{{-- Para ocultar widgets en el paso de pago --}}
@section('body_class', 'payment-page')

@push('styles')
@vite(entrypoints: 'resources/css/checkout.css')
<style>
    .gateway-selector {
        margin-bottom: 1.5rem;
    }

    .gateway-options {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .gateway-option {
        flex: 1;
        min-width: 200px;
    }

    .gateway-option input[type="radio"] {
        display: none;
    }

    .gateway-option label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }

    .gateway-option label:hover {
        border-color: var(--primary-color, #0066cc);
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.1);
    }

    .gateway-option input[type="radio"]:checked+label {
        border-color: var(--primary-color, #0066cc);
        background: rgba(0, 102, 204, 0.05);
    }

    .gateway-option label i {
        font-size: 2rem;
        color: var(--primary-color, #0066cc);
    }

    .gateway-option label div {
        display: flex;
        flex-direction: column;
    }

    .gateway-option label strong {
        font-size: 1rem;
        color: #1f2937;
    }

    .gateway-option label small {
        font-size: 0.875rem;
        color: #6b7280;
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
            <div class="panel-title">
                <i class="fas fa-credit-card"></i>
                {{ __('payment.payment_information') }}
            </div>
            <div class="panel-subtitle">
                <i class="fas fa-lock"></i>
                {{ __('payment.payment_secure_encrypted') }}
            </div>

            {{-- Gateway Selector --}}
            @if(count($enabledGateways) > 1)
                <div class="gateway-selector mb-4">
                    <label class="form-label fw-bold">{{ __('payment.select_payment_method') }}</label>
                    <div class="gateway-options">
                        @foreach($enabledGateways as $gateway)
                            <div class="gateway-option">
                                <input type="radio"
                                       name="payment_gateway"
                                       id="gateway_{{ $gateway['id'] }}"
                                       value="{{ $gateway['id'] }}"
                                       {{ $gateway['id'] === $defaultGateway ? 'checked' : '' }}>
                                <label for="gateway_{{ $gateway['id'] }}">
                                    <i class="{{ $gateway['icon'] }}"></i>
                                    <div>
                                        <strong>{{ $gateway['name'] }}</strong>
                                        <small>{{ $gateway['description'] }}</small>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="secure-badge">
                <i class="fas fa-shield-check"></i>
                <div>
                    <strong>{{ __('payment.secure_payment') }}</strong>
                    <small id="gateway-badge">
                        @if($defaultGateway === 'stripe')
                            {{ __('payment.powered_by_stripe') }}
                        @elseif($defaultGateway === 'paypal')
                            Powered by PayPal
                        @else
                            Powered by {{ ucfirst($defaultGateway) }}
                        @endif
                    </small>
                </div>
            </div>

            <form id="payment-form">
                @csrf

                {{-- Stripe Payment Element --}}
                <div id="stripe-container" class="payment-container" style="display:none;">
                    <div class="stripe-element-container">
                        <div id="payment-element">
                            <!-- Stripe Elements will be inserted here -->
                        </div>
                    </div>
                </div>

                {{-- Generic redirect gateway container (PayPal, Tilopay, Bancos, etc.) --}}
                <div id="redirect-gateway-container" class="payment-container" style="display:none;">
                    <div class="alert alert-info mb-0" style="font-size:0.875rem;">
                        <i class="fas fa-external-link-alt"></i>
                        <span id="redirect-gateway-text">
                            {{ __('payment.redirect_external_gateway') ?? 'You will be redirected to the external payment page to complete your payment.' }}
                        </span>
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
{{-- SDK de PayPal sólo si en el futuro usas los botones JS.
     Por ahora toda la lógica es por redirect_url / approval_url. --}}
{{-- <script src="https://www.paypal.com/sdk/js?client-id={{ config('payment.gateways.paypal.client_id') }}&currency={{ $currency }}"></script> --}}

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const defaultGateway = '{{ $defaultGateway }}';

    // Tipo de integración:
    const gatewayTypes = {
        stripe: 'stripe',
        paypal: 'redirect',
        tilopay: 'redirect',
        banco_nacional: 'redirect',
        bac: 'redirect',
        bcr: 'redirect',
    };

    let currentGateway     = defaultGateway;
    let stripeInstance     = null;
    let stripeElements     = null;
    let stripeClientSecret = null;

    // redirectUrls[gateway] = 'https://...'
    const redirectUrls = {};

    const stripeContainer     = document.getElementById('stripe-container');
    const redirectGatewayBox  = document.getElementById('redirect-gateway-container');
    const paymentElementEl    = document.getElementById('payment-element');
    const gatewayBadge        = document.getElementById('gateway-badge');
    const redirectGatewayText = document.getElementById('redirect-gateway-text');

    const form         = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText   = document.getElementById('button-text');
    const spinner      = document.getElementById('spinner');

    // Inicializar gateway por defecto
    await initializeGateway(currentGateway);
    updateGatewayBadge(currentGateway);

    // Cambio de gateway por radio
    document.querySelectorAll('input[name="payment_gateway"]').forEach(radio => {
        radio.addEventListener('change', async (e) => {
            currentGateway = e.target.value;
            await initializeGateway(currentGateway);
            updateGatewayBadge(currentGateway);
        });
    });

    async function initializeGateway(gateway) {
        stripeContainer.style.display    = 'none';
        redirectGatewayBox.style.display = 'none';

        try {
            const response = await fetch('{{ route("payment.initiate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ gateway })
            });

            const data = await response.json();

            if (!data.success) {
                console.error('Payment init failed:', data);
                showMessage(data.message || 'Failed to initialize payment');
                return;
            }

            const type = gatewayTypes[gateway] || 'redirect';

            if (type === 'stripe') {
                await initializeStripe(data.client_secret);
            } else if (type === 'redirect') {
                const url = data.redirect_url || data.approval_url || null;
                await initializeRedirectGateway(gateway, url);
            }
        } catch (error) {
            console.error('Payment initialization error:', error);
            showMessage('Failed to initialize payment. Please try again.');
        }
    }

    async function initializeStripe(clientSecret) {
        if (!clientSecret) {
            showMessage('Stripe not ready. Please refresh the page.');
            return;
        }

        stripeClientSecret = clientSecret;

        if (!stripeInstance) {
            stripeInstance = Stripe('{{ $stripeKey }}');
        }

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

        stripeElements = stripeInstance.elements({
            clientSecret,
            appearance
        });

        paymentElementEl.innerHTML = '';
        const paymentElement = stripeElements.create('payment');
        paymentElement.mount('#payment-element');

        stripeContainer.style.display    = 'block';
        redirectGatewayBox.style.display = 'none';
        submitButton.style.display       = 'inline-flex';
    }

    async function initializeRedirectGateway(gateway, url) {
        redirectUrls[gateway] = url || null;

        if (!redirectUrls[gateway]) {
            console.warn(`No redirect URL for gateway "${gateway}"`);
            showMessage('Failed to initialize payment gateway. Please try again.');
            return;
        }

        if (gateway === 'paypal') {
            redirectGatewayText.textContent =
                '{{ __("payment.redirect_paypal") ?? "You will be redirected to PayPal to complete your payment." }}';
        } else if (gateway === 'tilopay') {
            redirectGatewayText.textContent =
                '{{ __("payment.redirect_tilopay") ?? "You will be redirected to Tilopay to complete your payment." }}';
        } else {
            redirectGatewayText.textContent =
                '{{ __("payment.redirect_external_gateway") ?? "You will be redirected to the external payment page to complete your payment." }}';
        }

        redirectGatewayBox.style.display = 'block';
        stripeContainer.style.display    = 'none';
        submitButton.style.display       = 'inline-flex';
    }

    function updateGatewayBadge(gateway) {
        if (!gatewayBadge) return;

        if (gateway === 'stripe') {
            gatewayBadge.textContent = '{{ __("payment.powered_by_stripe") }}';
        } else if (gateway === 'paypal') {
            gatewayBadge.textContent = 'Powered by PayPal';
        } else if (gateway === 'tilopay') {
            gatewayBadge.textContent = 'Powered by Tilopay';
        } else {
            gatewayBadge.textContent = '';
        }
    }

    // Submit:
    // - redirect => window.location.href
    // - stripe   => confirmPayment
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const type = gatewayTypes[currentGateway] || 'redirect';

        if (type === 'redirect') {
            const url = redirectUrls[currentGateway];
            if (!url) {
                showMessage('Payment is not ready. Please select the gateway again or refresh the page.');
                return;
            }
            setLoading(true);
            window.location.href = url;
            return;
        }

        // Stripe
        if (!stripeClientSecret || !stripeInstance || !stripeElements) {
            showMessage('Payment not initialized. Please refresh the page.');
            return;
        }

        setLoading(true);

        try {
            const { error } = await stripeInstance.confirmPayment({
                elements: stripeElements,
                confirmParams: {
                    return_url: '{{ route("payment.confirm") }}?payment_intent_id=' +
                        stripeClientSecret.split('_secret_')[0],
                },
            });

            if (error) {
                showMessage(error.message);
                setLoading(false);
            }
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
            spinner.style.display    = 'inline-block';
        } else {
            buttonText.style.display = 'inline';
            spinner.style.display    = 'none';
        }
    }

    function showMessage(message) {
        const messageDiv = document.getElementById('payment-message');
        if (!messageDiv) return;

        messageDiv.textContent = message;
        messageDiv.classList.add('show');

        setTimeout(() => {
            messageDiv.classList.remove('show');
        }, 5000);
    }
});
</script>
@endpush
