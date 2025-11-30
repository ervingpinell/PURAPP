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

    .payment-container {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        background: #fff;
        margin-bottom: 24px;
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
                    <div id="payment-element">
                        <!-- Stripe Elements will be inserted here -->
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
            @php
            use Illuminate\Support\Str;

            $fmt = fn($n) => number_format((float) $n, 2, '.', '');
            $itemsCollection = collect($items);
            $cnt = $itemsCollection->count();
            $promo = session('public_cart_promo'); // Get promo from session

            // Helper for item subtotal
            $itemSub = function($item) {
            $cats = collect($item['categories'] ?? []);
            if($cats->isNotEmpty()){
            return $cats->sum(function ($c) {
            return (int)($c['quantity'] ?? 0) * (float)($c['price'] ?? 0);
            });
            }
            return 0;
            };

            // ===== Resolver de nombres de categoría (igual que en checkout) =====
            $loc = app()->getLocale();
            $fb = config('app.fallback_locale', 'es');

            // category_id presentes en el snapshot / cart
            $categoryIdsInCart = $itemsCollection
            ->flatMap(fn($it) => collect($it['categories'] ?? [])->pluck('category_id')->filter())
            ->unique()
            ->values();

            $categoryNamesById = collect();
            if ($categoryIdsInCart->isNotEmpty()) {
            $catModels = \App\Models\CustomerCategory::whereIn('category_id', $categoryIdsInCart)
            ->with('translations')
            ->get();

            $categoryNamesById = $catModels->mapWithKeys(function ($c) use ($loc, $fb) {
            $name = method_exists($c, 'getTranslated')
            ? ($c->getTranslated('name') ?? $c->name)
            : (optional($c->translations->firstWhere('locale', $loc))->name
            ?? optional($c->translations->firstWhere('locale', $fb))->name
            ?? $c->name);

            return [$c->category_id => $name];
            });
            }

            // Mismo closure que en checkout.blade
            $resolveCatLabel = function (array $cat) use ($categoryNamesById) {
            $name = data_get($cat,'i18n_name')
            ?? data_get($cat,'name')
            ?? data_get($cat,'label')
            ?? data_get($cat,'category_name')
            ?? data_get($cat,'category.name');

            $cid = (int) (data_get($cat,'category_id') ?? data_get($cat,'id') ?? 0);
            if (!$name && $cid && $categoryNamesById->has($cid)) {
            $name = $categoryNamesById->get($cid);
            }

            if (!$name) {
            $code = Str::lower((string) data_get($cat,'code',''));
            if (in_array($code,['adult','adults'])) {
            $name = __('adminlte::adminlte.adult');
            } elseif (in_array($code,['kid','kids','child','children'])) {
            $name = __('adminlte::adminlte.kid');
            } elseif ($code !== '') {
            $tr = __($code);
            $name = ($tr === $code) ? $code : $tr;
            }
            }

            if (!$name) {
            $slug = (string) (data_get($cat,'category_slug') ?? data_get($cat,'slug') ?? '');
            if ($slug) {
            $name = Str::of($slug)->replace(['_','-'],' ')->title();
            }
            }

            return $name ?: __('adminlte::adminlte.category');
            };
            @endphp

            <div class="summary-header">
                <h3>{{ __('payment.order_summary') }}</h3>
                <span class="count">
                    {{ $cnt }}
                    {{ $cnt === 1 ? __('m_checkout.summary.item') : __('m_checkout.summary.items') }}
                </span>
            </div>

            <div class="items-scroll">
                @foreach($itemsCollection as $item)
                @php
                $cats = collect($item['categories'] ?? []);
                $totalPax = $cats->sum('quantity');
                $itemTotal = $itemSub($item);

                // Extract details from array structure
                $tour = $item['tour'];
                $tourDate = $item['tour_date'];
                $schedule = $item['schedule'] ?? null;
                $language = $item['language'] ?? null;
                $hotel = $item['hotel'] ?? null;
                $meetingPoint = $item['meetingPoint'] ?? null;
                $addons = collect($item['addons'] ?? []);
                $notes = $item['notes'] ?? null;
                $duration = $item['duration'] ?? null;
                $guide = $item['guide'] ?? null;
                $tz = config('app.timezone','America/Costa_Rica');
                @endphp

                <div class="tour-item">
                    <div class="tour-name">
                        {{ $tour->getTranslatedName() ?? $tour->name }}
                    </div>

                    <div class="tour-details">
                        <span>
                            <i class="far fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($tourDate)->format('l, F d, Y') }}
                        </span>

                        @if($schedule)
                        <span>
                            <i class="far fa-clock"></i>
                            {{ __('m_checkout.misc.at') }}
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                        </span>
                        @endif

                        @if($totalPax > 0)
                        <span>
                            <i class="fas fa-user"></i>
                            {{ $totalPax }}
                            {{ $totalPax === 1 ? __('m_checkout.misc.participant') : __('m_checkout.misc.participants') }}
                        </span>
                        @endif

                        @if($language)
                        <span>
                            <i class="fas fa-language"></i>
                            {{ $language->name }}
                        </span>
                        @endif
                    </div>

                    {{-- Pickup / Meeting Point --}}
                    @if($hotel || $meetingPoint)
                    <div class="secondary-block">
                        <div class="block-title">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ __('m_checkout.blocks.pickup_meeting') }}</span>
                        </div>
                        <div class="small" style="color:var(--g700)">
                            @if($hotel)
                            <div class="mb-1">
                                <strong>{{ __('m_checkout.blocks.hotel') }}:</strong>
                                {{ $hotel->name ?? '' }}
                                @if(data_get($hotel,'address'))
                                — {{ $hotel->address }}
                                @endif
                            </div>
                            @endif

                            @if($meetingPoint)
                            <div class="mb-1">
                                <strong>{{ __('m_checkout.blocks.meeting_point') }}:</strong>
                                {{ $meetingPoint->name ?? '' }}
                                @if(data_get($meetingPoint,'address'))
                                — {{ $meetingPoint->address }}
                                @endif
                            </div>
                            @if(data_get($meetingPoint,'notes'))
                            <div class="text-muted">{{ $meetingPoint->notes }}</div>
                            @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Addons --}}
                    @if($addons->isNotEmpty())
                    <div class="secondary-block">
                        <div class="block-title">
                            <i class="fas fa-plus-circle"></i>
                            <span>{{ __('m_checkout.blocks.add_ons') }}</span>
                        </div>
                        @foreach($addons as $ad)
                        @php
                        $aq = (int) data_get($ad,'quantity',0);
                        $ap = (float) data_get($ad,'price',0);
                        $at = $aq * $ap;
                        @endphp
                        @if($aq > 0)
                        <div class="d-flex justify-content-between align-items-center" style="gap:.5rem;padding:.25rem 0">
                            <div class="d-flex align-items-center" style="gap:.5rem">
                                <i class="fas fa-tag" style="color:var(--p)"></i>
                                <span class="small">{{ data_get($ad,'name','Extra') }}</span>
                                <span class="qty-badge">{{ $aq }}x</span>
                                <span class="price-detail small">
                                    (${{ $fmt($ap) }} × {{ $aq }})
                                </span>
                            </div>
                            <div class="fw-bold">${{ $fmt($at) }}</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    {{-- Duration / Guide --}}
                    @if($duration || $guide)
                    <div class="secondary-block">
                        <div class="d-flex flex-wrap gap-3 small" style="color:var(--g700)">
                            @if($duration)
                            <div>
                                <i class="far fa-hourglass" style="color:var(--p)"></i>
                                <strong>{{ __('m_checkout.blocks.duration') }}:</strong>
                                {{ $duration }} {{ __('m_checkout.blocks.hours') }}
                            </div>
                            @endif
                            @if($guide)
                            <div>
                                <i class="fas fa-user-tie" style="color:var(--p)"></i>
                                <strong>{{ __('m_checkout.blocks.guide') }}:</strong>
                                {{ $guide }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Notes --}}
                    @if($notes)
                    <div class="secondary-block">
                        <div class="block-title">
                            <i class="fas fa-sticky-note"></i>
                            <span>{{ __('m_checkout.blocks.notes') }}</span>
                        </div>
                        <div class="small" style="color:var(--g700)">{{ $notes }}</div>
                    </div>
                    @endif

                    {{-- Categories --}}
                    @if($cats->isNotEmpty())
                    <div class="categories-section" style="margin-top:.5rem">
                        @foreach($cats as $c)
                        @php
                        $q = (int) ($c['quantity'] ?? 0);
                        $u = (float) ($c['price'] ?? 0);
                        $sub = $q * $u;

                        $code = \Illuminate\Support\Str::lower((string)($c['code'] ?? ''));
                        $isAdult = in_array($code, ['adult', 'adults']);
                        $isKid = in_array($code, ['kid', 'kids', 'child', 'children']);

                        $lab = $resolveCatLabel((array) $c);
                        @endphp
                        @if($q > 0)
                        <div class="category-line">
                            <div class="category-left">
                                @if($isAdult)
                                <i class="fas fa-user"></i>
                                <strong>{{ __('m_checkout.categories.adult') }}</strong>
                                @elseif($isKid)
                                <i class="fas fa-child"></i>
                                <strong>{{ __('m_checkout.categories.kid') }}</strong>
                                @else
                                <i class="fas fa-user-friends"></i>
                                <span>{{ $lab }}</span>
                                @endif

                                <span class="qty-badge">{{ $q }}x</span>
                                <span class="price-detail">
                                    (${{ $fmt($u) }} × {{ $q }})
                                </span>
                            </div>
                            <div class="category-total">
                                ${{ $fmt($sub) }}
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    @if($itemTotal > 0)
                    <div class="tour-price">${{ $fmt($itemTotal) }}</div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="summary-footer">
                <div class="totals-section">
                    {{-- Calculate subtotal before promo --}}
                    @php
                    $calculatedSubtotal = $itemsCollection->sum(function($item) use ($itemSub) {
                    return $itemSub($item);
                    });

                    // Add addons total to subtotal
                    $addonsTotal = $itemsCollection->sum(function($item) {
                    return collect($item['addons'] ?? [])->sum(function($ad) {
                    return (int)($ad['quantity'] ?? 0) * (float)($ad['price'] ?? 0);
                    });
                    });
                    $calculatedSubtotal += $addonsTotal;
                    @endphp

                    <div class="total-row">
                        <span>{{ __('payment.subtotal') }}</span>
                        <span>${{ $fmt($calculatedSubtotal) }}</span>
                    </div>

                    @if($promo)
                    <div class="total-row promo">
                        <span>
                            {{ __('m_checkout.summary.promo_code') }}
                            <span class="promo-code">{{ $promo['code'] }}</span>
                        </span>
                        <span>
                            {{ ($promo['operation'] ?? 'subtract') === 'subtract' ? '-' : '+' }}
                            ${{ $fmt($promo['adjustment'] ?? 0) }}
                        </span>
                    </div>
                    @endif

                    <div class="total-row final">
                        <span>{{ __('payment.total') }}</span>
                        <span>${{ $fmt($total) }}</span>
                    </div>

                    <div class="total-note">
                        {{ __('m_checkout.summary.taxes_included') }}
                    </div>

                    @php
                    // Format free cancellation message (same as checkout)
                    $freeCancelText = null;
                    $showCancellation = false;

                    if (!empty($freeCancelUntil)) {
                    // Only show if deadline hasn't passed
                    $now = \Carbon\Carbon::now(config('app.timezone', 'America/Costa_Rica'));

                    if ($freeCancelUntil->isFuture()) {
                    $showCancellation = true;
                    $tz = config('app.timezone', 'America/Costa_Rica');
                    $locCut = $freeCancelUntil->copy()->setTimezone($tz)->locale(app()->getLocale());

                    $cutTime = $locCut->isoFormat('LT'); // e.g. 3:15 p. m.
                    $cutDate = $locCut->isoFormat('LL'); // e.g. 9 de noviembre de 2025

                    $key = 'policies.checkout.free_cancellation_until';
                    if (\Illuminate\Support\Facades\Lang::has($key)) {
                    $freeCancelText = __($key, ['time' => $cutTime, 'date' => $cutDate]);
                    } else {
                    $freeCancelText = __('m_checkout.summary.free_cancellation') . ' — ' . $cutTime . ' · ' . $cutDate;
                    }
                    }
                    }
                    @endphp

                    @if($showCancellation && $freeCancelText)
                    <div class="cancellation-badge">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>{{ __('m_checkout.summary.free_cancellation') }}</strong>
                            <small>{{ $freeCancelText }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const defaultGateway = '{{ $defaultGateway }}';
        const csrfToken = document.querySelector('input[name="_token"]').value;

        // Tipo de integración:
        const gatewayTypes = {
            stripe: 'stripe',
            paypal: 'redirect',
            tilopay: 'redirect',
            banco_nacional: 'redirect',
            bac: 'redirect',
            bcr: 'redirect',
        };

        let currentGateway = defaultGateway;
        let stripeInstance = null;
        let stripeElements = null;
        let stripeClientSecret = null;

        // redirectUrls[gateway] = 'https://...'
        const redirectUrls = {};

        const stripeContainer = document.getElementById('stripe-container');
        const redirectGatewayBox = document.getElementById('redirect-gateway-container');
        const paymentElementEl = document.getElementById('payment-element');
        const gatewayBadge = document.getElementById('gateway-badge');
        const redirectGatewayText = document.getElementById('redirect-gateway-text');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');

        // Prevenir loop infinito de recargas
        const RETRY_KEY = 'payment_init_retries';
        const MAX_RETRIES = 3;
        let retryCount = parseInt(sessionStorage.getItem(RETRY_KEY) || '0');

        if (retryCount >= MAX_RETRIES) {
            showMessage('Demasiados intentos fallidos. Por favor, vuelve al checkout y intenta de nuevo.');
            sessionStorage.removeItem(RETRY_KEY);
            // Deshabilitar auto-inicialización
            console.warn('Max retries reached, skipping auto-initialization');
        } else {
            // Delay de 500ms antes de inicializar para evitar rate limiting
            setTimeout(async () => {
                await initializeGateway(currentGateway);
                updateGatewayBadge(currentGateway);
            }, 500);
        }

        // Cambio de gateway por radio
        document.querySelectorAll('input[name="payment_gateway"]').forEach(radio => {
            radio.addEventListener('change', async (e) => {
                currentGateway = e.target.value;
                await initializeGateway(currentGateway);
                updateGatewayBadge(currentGateway);
            });
        });

        async function initializeGateway(gateway) {
            stripeContainer.style.display = 'none';
            redirectGatewayBox.style.display = 'none';

            try {
                const response = await fetch('{{ route("payment.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        gateway
                    })
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Non-JSON response received:', response.status, response.statusText);

                    // Incrementar contador de reintentos
                    retryCount++;
                    sessionStorage.setItem(RETRY_KEY, retryCount.toString());

                    if (response.status === 429) {
                        if (retryCount >= MAX_RETRIES) {
                            showMessage('Demasiados intentos. Por favor, espera un momento y vuelve al checkout.');
                            sessionStorage.removeItem(RETRY_KEY);
                            return;
                        }
                        showMessage(`Demasiadas solicitudes (${retryCount}/${MAX_RETRIES}). Recargando en 3 segundos...`);
                        setTimeout(() => window.location.reload(), 3000);
                        return;
                    }

                    showMessage('Error del servidor. Recargando la página en 3 segundos...');
                    setTimeout(() => window.location.reload(), 3000);
                    return;
                }

                // Si llegamos aquí, la respuesta fue exitosa - limpiar contador
                sessionStorage.removeItem(RETRY_KEY);

                const data = await response.json();

                if (!data.success) {
                    console.error('Payment init failed:', data);
                    showMessage(data.message || 'Error al inicializar pago. Recargando...');
                    setTimeout(() => window.location.reload(), 3000);
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
                showMessage('Error al inicializar pago. Recargando la página en 3 segundos...');
                setTimeout(() => window.location.reload(), 3000);
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

            stripeContainer.style.display = 'block';
            redirectGatewayBox.style.display = 'none';
            submitButton.style.display = 'inline-flex';
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
            stripeContainer.style.display = 'none';
            submitButton.style.display = 'inline-flex';
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
                const {
                    error
                } = await stripeInstance.confirmPayment({
                    elements: stripeElements,
                    confirmParams: {
                        return_url: '{{ route("payment.return") }}',
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
                spinner.style.display = 'inline-block';
            } else {
                buttonText.style.display = 'inline';
                spinner.style.display = 'none';
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