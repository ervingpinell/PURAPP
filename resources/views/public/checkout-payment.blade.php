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

                {{-- Terms & Conditions Modal Trigger --}}
                <div class="acceptance-box disabled" id="accept-box">
                    <div class="acceptance-checkbox">
                        <input type="checkbox" id="terms_accepted" name="terms_accepted" value="1" disabled>
                        <label for="terms_accepted" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#termsModal">
                            {!! __('m_checkout.accept.label_html') !!}
                        </label>
                    </div>
                    <div id="terms-error" class="text-danger small mt-2" style="display:none;">
                        {{ __('m_checkout.accept.error') }}
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('public.carts.index') }}" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i>{{ __('payment.back') }}
                    </a>
                    <button type="submit" id="submit-button" class="btn btn-pay" disabled>
                        <span id="button-text">
                            {{ __('payment.pay') }} ${{ number_format($total, 2) }}
                        </span>
                        <span id="spinner" class="spinner" style="display:none"></span>
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-lock"></i>
                    {{ __('payment.secure_payment') }}
                </small>
            </div>
        </div>

        {{-- Terms Modal --}}
        <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-shield-check me-2"></i>
                            {{ __('m_checkout.panels.terms_block_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="policy-content" id="policy-content" style="max-height: 60vh; overflow-y: auto; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            @include('policies.checkout.content')
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between align-items-center">
                        <div class="small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('m_checkout.panels.required_read_accept') }}
                        </div>
                        <button type="button" class="btn btn-primary" id="btn-accept-terms" disabled data-bs-dismiss="modal">
                            {{ __('m_checkout.buttons.close') }} & {{ __('payment.pay') }}
                        </button>
                    </div>
                </div>
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

            @if(isset($booking) && $booking->user)
            <div class="customer-details mb-3 p-3 bg-light rounded" style="border: 1px solid #e5e7eb;">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-user-circle me-2 text-primary"></i>
                    <strong>{{ __('m_bookings.bookings.customer') }}</strong>
                </div>
                <div class="small">
                    <div class="fw-bold">{{ $booking->user->name }}</div>
                    <div class="text-muted">{{ $booking->user->email }}</div>
                </div>
            </div>
            @endif

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
{{ ucfirst(\Carbon\Carbon::parse($tourDate)->locale(app()->getLocale())->translatedFormat('l, d F, Y')) }}
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
    // Manual initialization of cartCountdown for booking payments
    @if(isset($expiresAt))
        (function() {
            if (window.cartCountdown) return; // Already initialized

            const expiresAtStr = @json($expiresAt);
            if (!expiresAtStr) return;

            let serverExpires = new Date(expiresAtStr).getTime();

            window.cartCountdown = {
                getRemainingSeconds: () => {
                    const now = Date.now();
                    return Math.max(0, Math.ceil((serverExpires - now) / 1000));
                },
                getTotalSeconds: () => 1800, // Default 30m
                getExpiresAt: () => new Date(serverExpires),
                isExpired: () => {
                    return window.cartCountdown.getRemainingSeconds() <= 0;
                }
            };
        })();
    @endif
</script>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const defaultGateway = '{{ $defaultGateway }}';
        const csrfToken = document.querySelector('input[name="_token"]').value;

        let currentGateway = defaultGateway;
        let stripeInstance = null;
        let stripeElements = null;
        let stripeClientSecret = null;

        const stripeContainer = document.getElementById('stripe-container');
        const redirectGatewayBox = document.getElementById('redirect-gateway-container');
        const paymentElementEl = document.getElementById('payment-element');
        const gatewayBadge = document.getElementById('gateway-badge');
        const redirectGatewayText = document.getElementById('redirect-gateway-text');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');
        const paymentMessage = document.getElementById('payment-message');

        // Terms & Scroll Logic
        const termsCheckbox = document.getElementById('terms_accepted');
        const acceptBox = document.getElementById('accept-box');
        const policyContent = document.getElementById('policy-content');
        const btnAcceptTerms = document.getElementById('btn-accept-terms');
        const termsError = document.getElementById('terms-error');
        let termsScrolled = false;

        // Disable pay button initially
        submitButton.disabled = true;

        if (policyContent) {
            policyContent.addEventListener('scroll', function() {
                if (this.scrollHeight - this.scrollTop <= this.clientHeight + 50) {
                    if (!termsScrolled) {
                        termsScrolled = true;
                        btnAcceptTerms.disabled = false;
                        btnAcceptTerms.classList.remove('btn-secondary');
                        btnAcceptTerms.classList.add('btn-success');
                    }
                }
            });
        }

        if (btnAcceptTerms) {
            btnAcceptTerms.addEventListener('click', function() {
                if (termsScrolled) {
                    termsCheckbox.checked = true;
                    termsCheckbox.disabled = false;
                    acceptBox.classList.remove('disabled');
                    acceptBox.classList.add('accepted');
                    submitButton.disabled = false;
                    termsError.style.display = 'none';
                }
            });
        }

        // --- Payment Logic ---

        // Initial Setup
        if (currentGateway === 'stripe') {
            // Auto-init Stripe (setup phase, no terms required yet)
            setTimeout(() => initializeGateway('stripe', false), 500);
        } else {
            // Redirect gateway: Show UI, wait for user action
            stripeContainer.style.display = 'none';
            redirectGatewayBox.style.display = 'block';
            updateRedirectText(currentGateway);
        }

        // Gateway Selection
        document.querySelectorAll('input[name="payment_gateway"]').forEach(input => {
            input.addEventListener('change', async (e) => {
                const newGateway = e.target.value;
                if (newGateway === currentGateway) return;

                currentGateway = newGateway;
                updateGatewayBadge(currentGateway);
                paymentMessage.textContent = '';
                paymentMessage.classList.remove('show');

                if (currentGateway === 'stripe') {
                    redirectGatewayBox.style.display = 'none';
                    stripeContainer.style.display = 'block';
                    await initializeGateway('stripe', false);
                } else {
                    stripeContainer.style.display = 'none';
                    redirectGatewayBox.style.display = 'block';
                    updateRedirectText(currentGateway);
                }
            });
        });

        // Submit Handler
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // 1. Validate Terms Locally
            if (!termsCheckbox.checked) {
                termsError.style.display = 'block';
                showMessage('{{ __("m_checkout.accept.error") }}');
                return;
            }

            setLoading(true);

            if (currentGateway === 'stripe') {
                await handleStripePayment();
            } else {
                await handleRedirectPayment();
            }
        });

        async function handleStripePayment() {
            if (!stripeInstance || !stripeElements) {
                showMessage('Stripe not initialized. Please refresh.');
                setLoading(false);
                return;
            }

            // 1. Record Terms Acceptance
            try {
                const termsResponse = await fetch('{{ route("payment.record-terms") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        terms_accepted: 1
                    })
                });

                if (!termsResponse.ok) {
                    throw new Error('Failed to record terms acceptance');
                }
            } catch (err) {
                console.error('Terms record error:', err);
                showMessage('{{ __("m_checkout.accept.error") }}');
                setLoading(false);
                return;
            }

            // 2. Confirm Payment
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
        }

        async function handleRedirectPayment() {
            // Initiate payment (action phase) - this time WITH terms
            // This will return the redirect URL
            await initializeGateway(currentGateway, true);
        }

        async function initializeGateway(gateway, isActionPhase = false) {
            try {
                const payload = {
                    gateway
                };
                // Only send terms if we are in action phase (clicking Pay)
                // or if we want to enforce it. For Stripe setup, we don't send it.
                if (isActionPhase) {
                    payload.terms_accepted = termsCheckbox.checked ? 1 : 0;
                }

                const response = await fetch('{{ route("payment.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // Handle non-JSON errors (don't reload loop)
                    if (response.status === 422) {
                        showMessage('{{ __("m_checkout.accept.error") }}');
                    } else {
                        showMessage('Error initializing gateway. Please try again.');
                    }
                    setLoading(false);
                    return;
                }

                const data = await response.json();

                if (!data.success) {
                    showMessage(data.message || 'Error al inicializar pago.');
                    setLoading(false);
                    return;
                }

                if (gateway === 'stripe') {
                    if (data.client_secret) {
                        stripeClientSecret = data.client_secret;
                        const appearance = {
                            theme: 'stripe'
                        };
                        stripeInstance = Stripe('{{ $stripeKey }}');
                        stripeElements = stripeInstance.elements({
                            appearance,
                            clientSecret: stripeClientSecret
                        });
                        const paymentElement = stripeElements.create('payment');
                        paymentElement.mount('#payment-element');
                        stripeContainer.style.display = 'block';
                    }
                } else {
                    // Redirect Gateway
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        showMessage('Error: No redirect URL received.');
                        setLoading(false);
                    }
                }

            } catch (error) {
                console.error('Init error:', error);
                showMessage('Connection error. Please try again.');
                setLoading(false);
            }
        }

        function updateRedirectText(gateway) {
            if (gateway === 'paypal') {
                redirectGatewayText.textContent = "{{ __('payment.paypal_description') }}";
            } else {
                redirectGatewayText.textContent = "{{ __('payment.redirect_external_gateway') }}";
            }
        }

        function updateGatewayBadge(gateway) {
            if (gateway === 'stripe') gatewayBadge.textContent = "{{ __('payment.powered_by_stripe') }}";
            else if (gateway === 'paypal') gatewayBadge.textContent = "Powered by PayPal";
            else gatewayBadge.textContent = "Powered by " + gateway.charAt(0).toUpperCase() + gateway.slice(1);
        }

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
            if (!paymentMessage) return;
            paymentMessage.textContent = message;
            paymentMessage.classList.add('show');
            setTimeout(() => {
                paymentMessage.classList.remove('show');
            }, 5000);
        }
    });
</script>
@endpush
