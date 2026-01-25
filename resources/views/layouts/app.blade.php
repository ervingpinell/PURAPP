{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Route;

$appLocale = str_replace('_', '-', app()->getLocale() ?? 'es');
$ASSET_ROOT = rtrim(asset(''), '/');

$metaTitle = trim($__env->yieldContent('meta_title') ?? '');
$pageTitle = trim($__env->yieldContent('title') ?? '');

if ($metaTitle) {
$fullTitle = $metaTitle;
} else {
$fullTitle = config('company.brand_name') . ' | ' . ($pageTitle !== '' ? $pageTitle : config('company.short_name'));
}

$metaDescSlot = $__env->yieldContent('meta_description');
$metaDesc = $metaDescSlot ?: config('company.seo.meta_description');

$isProd = app()->environment('production');

$consentCookie = request()->cookie('gv_cookie_consent');
$hasConsent = !is_null($consentCookie);
$cookiesOk = ($consentCookie === '1') || (bool) session('cookies.accepted', false);

$gaId = config('services.google.analytics_id') ?: 'G-VZNMJV1T29';
$pixelId = config('services.meta.pixel_id');

$themeColor = $__env->yieldContent('theme_color') ?: '#0f2419';
$isHome = request()->routeIs('home');

// 1. Default Fallbacks (Homepage)
$homeEs = (function_exists('localized_route') && Route::has('home')) ? localized_route('home', 'es') : url('/');
$homeEn = (function_exists('localized_route') && Route::has('home')) ? localized_route('home', 'en') : url('/en');
$homeFr = (function_exists('localized_route') && Route::has('home')) ? localized_route('home', 'fr') : url('/fr');
$homeDe = (function_exists('localized_route') && Route::has('home')) ? localized_route('home', 'de') : url('/de');
$homePt = (function_exists('localized_route') && Route::has('home')) ? localized_route('home', 'pt_BR') : url('/pt');

// 2. Dynamic Override (Current Page)
try {
$currentRoute = Route::current();
$currentRouteName = $currentRoute ? $currentRoute->getName() : null;
$currentRouteParams = $currentRoute ? $currentRoute->parameters() : [];

if (function_exists('localized_route') && $currentRouteName) {
$segments = explode('.', $currentRouteName);
$first = $segments[0] ?? '';
$knownPrefixes = ['es', 'en', 'fr', 'de', 'pt'];
$baseName = $currentRouteName;

if (in_array($first, $knownPrefixes)) {
array_shift($segments);
$baseName = implode('.', $segments);
}

if (Route::has('es.' . $baseName)) {
$homeEs = localized_route($baseName, $currentRouteParams, 'es');
$homeEn = localized_route($baseName, $currentRouteParams, 'en');
$homeFr = localized_route($baseName, $currentRouteParams, 'fr');
$homeDe = localized_route($baseName, $currentRouteParams, 'de');
$homePt = localized_route($baseName, $currentRouteParams, 'pt_BR');
}
}
} catch (\Throwable $e) {
// Silent failure: keep defaults
}

// Cart count route with safe fallback
if (Route::has('cart.count')) {
$cartCountUrl = route('cart.count');
} elseif (Route::has('cart.count.public')) {
$cartCountUrl = route('cart.count.public');
} else {
$cartCountUrl = url('/cart/count');
}

// Determine body classes dynamically
$bodyClasses = [];

// Add class based on route
if (request()->routeIs('public.checkout.show')) {
$bodyClasses[] = 'checkout-page';
} elseif (request()->routeIs('payment.process')) {
$bodyClasses[] = 'payment-page';
}

// Add home class if needed
if ($isHome) {
$bodyClasses[] = 'is-home';
}

// Add additional classes from section
$additionalBodyClass = trim($__env->yieldContent('body_class') ?? '');
if ($additionalBodyClass) {
$bodyClasses[] = $additionalBodyClass;
}

$bodyClassString = implode(' ', array_unique(array_filter($bodyClasses)));

// Safe cookie consent checks
$analyticsAllowed = function_exists('cookie_allowed') ? cookie_allowed('analytics') : false;
$marketingAllowed = function_exists('cookie_allowed') ? cookie_allowed('marketing') : false;

// Schema.org JSON-LD seguro usando json_encode
$schemaOrg = [
'@context' => 'https://schema.org',
'@type' => 'TravelAgency',
'name' => config('company.name'),
'image' => asset('images/logo.png'),
'description' => $metaDesc,
'@id' => url('/'),
'url' => url('/'),
'telephone' => config('company.phone', '+506-8888-8888'),
'email' => config('company.email', 'info@greenvacationscr.com'),
'address' => [
'@type' => 'PostalAddress',
'streetAddress' => config('company.address.street', 'La Fortuna, San Carlos'),
'addressLocality' => config('company.address.city', 'La Fortuna'),
'addressRegion' => config('company.address.state', 'Alajuela'),
'postalCode' => config('company.address.postal_code', '21007'),
'addressCountry' => config('company.address.country_code', 'CR')
],
'geo' => [
'@type' => 'GeoCoordinates',
'latitude' => config('company.map.latitude', '10.4678'),
'longitude' => config('company.map.longitude', '-84.6427')
],
'openingHoursSpecification' => [
[
'@type' => 'OpeningHoursSpecification',
'dayOfWeek' => [
'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
],
'opens' => '07:00',
'closes' => '22:00'
]
],
'sameAs' => [
'https://www.facebook.com/GreenVacationsCR',
'https://www.instagram.com/greenvacationscr'
],
'priceRange' => '$$'
];
@endphp
<html lang="{{ $appLocale }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ $appLocale }}">
    <meta name="cart-count-url" content="{{ $cartCountUrl }}">

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="keywords" content="{{ config('company.seo.meta_keywords') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ⚡ Performance: Preconnect to external domains --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//connect.facebook.net">

    {{-- ⚡ Preload critical CSS --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">

    {{-- Alternate hreflangs --}}
    <link rel="alternate" hreflang="es" href="{{ $homeEs }}">
    <link rel="alternate" hreflang="en" href="{{ $homeEn }}">
    <link rel="alternate" hreflang="fr" href="{{ $homeFr }}">
    <link rel="alternate" hreflang="de" href="{{ $homeDe }}">
    <link rel="alternate" hreflang="pt-BR" href="{{ $homePt }}">
    <link rel="alternate" hreflang="x-default" href="{{ $homeEs }}">

    {{-- OG / Twitter --}}
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image" content="{{ $ASSET_ROOT }}/images/og-image.jpg">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('company.name') }}">
    <meta property="og:locale" content="{{ $appLocale }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $ASSET_ROOT }}/images/og-image.jpg">

    {{-- LocalBusiness Schema usando json_encode seguro --}}
    <script type="application/ld+json">
        {!! json_encode($schemaOrg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- Favicon / PWA --}}
    <link rel="icon" href="{{ $ASSET_ROOT }}/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ $ASSET_ROOT }}/favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ $ASSET_ROOT }}/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $ASSET_ROOT }}/apple-touch-icon.png">
    <link rel="manifest" href="{{ $ASSET_ROOT }}/site.webmanifest" crossorigin="use-credentials">

    <meta id="themeColorMeta" name="theme-color" content="{{ $themeColor }}">
    <meta name="theme-color" content="#0b2e13" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    @if ($isHome)
    <style>
        html {
            background-color: {
                    {
                    $themeColor
                }
            }

            ;
        }

        @supports (padding: max(0px)) {
            body {
                padding-bottom: max(0px, env(safe-area-inset-bottom));
            }
        }
    </style>
    @endif

    {{-- Icons and public Bootstrap --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('meta')

    @vite([
    'resources/js/app.js',
    'resources/css/adminlte-fix.css',
    'resources/css/app.css',
    'resources/css/checkout.css'
    ])

    {{-- Dynamic Branding CSS - Comes AFTER app.css to override variables --}}
    @php
        try {
            $brandingCss = \App\Models\BrandingSetting::getCssVariables();
            \Log::info('Branding CSS generated', ['length' => strlen($brandingCss), 'empty' => empty($brandingCss)]);
            $backgroundEnabled = branding('background_enabled', '0') == '1';
            $backgroundImage = branding('background_image', '');
            $backgroundOpacity = branding('background_opacity', '0.95');
            $headerOpacity = branding('header_opacity', '0.95');
            $headerBlur = branding('header_blur', '10');
            $footerOpacity = branding('footer_opacity', '0.95');
            $textShadowEnabled = branding('text_shadow_enabled', '1') == '1';
        } catch (\Exception $e) {
            \Log::error('Branding CSS failed', ['error' => $e->getMessage()]);
            $brandingCss = '';
            $backgroundEnabled = false;
            $backgroundImage = '';
            $backgroundOpacity = '0.95';
            $headerOpacity = '0.95';
            $headerBlur = '10';
            $footerOpacity = '0.95';
            $textShadowEnabled = true;
        }
    @endphp

    @if($brandingCss)
    <style id="branding-css">
        {!! $brandingCss !!}

        @if($backgroundEnabled && $backgroundImage && file_exists(public_path($backgroundImage)))
        /* Make body transparent so background image is visible */
        html, body {
            background: transparent !important;
        }
        
        /* Background image layer - behind everything */
        body::before {
            content: '' !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background-image: url('{{ asset($backgroundImage) }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
            background-repeat: no-repeat !important;
            z-index: -2 !important;
        }
        
        /* White overlay layer */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, {{ $backgroundOpacity }});
            pointer-events: none;
            z-index: -1;
        }
        @endif

        /* Text Shadow */
        @php
            $shadowX = branding('text_shadow_x', '2');
            $shadowY = branding('text_shadow_y', '2');
            $shadowBlur = branding('text_shadow_blur', '4');
            $shadowOpacity = branding('text_shadow_opacity', '0.5');
            $shadowColor = branding('text_shadow_color', '#000000');
            
            // Convert hex to RGB
            $r = hexdec(substr($shadowColor, 1, 2));
            $g = hexdec(substr($shadowColor, 3, 2));
            $b = hexdec(substr($shadowColor, 5, 2));
            
            $textShadow = "{$shadowX}px {$shadowY}px {$shadowBlur}px rgba({$r}, {$g}, {$b}, {$shadowOpacity})";
        @endphp
        
        @if(branding('text_shadow_headings', '1') == '1')
        h1:not(.tour-title-abs), 
        h2:not(.tour-title-abs), 
        h3:not(.tour-title-abs), 
        h4:not(.tour-title-abs), 
        h5:not(.tour-title-abs), 
        h6:not(.tour-title-abs) {
            text-shadow: {!! $textShadow !!};
        }
        @endif
        
        @if(branding('text_shadow_big_title', '1') == '1')
        .big-title {
            text-shadow: {!! $textShadow !!};
        }
        @endif
        
        @if(branding('text_shadow_lead', '1') == '1')
        .lead {
            text-shadow: {!! $textShadow !!};
        }
        @endif
        
        @if(branding('text_shadow_text_muted', '0') == '1')
        .text-muted {
            text-shadow: {!! $textShadow !!};
        }
        @endif
        
        @if(branding('text_shadow_breadcrumbs', '0') == '1')
        .breadcrumb-item,
        .breadcrumb-item a {
            text-shadow: {!! $textShadow !!};
        }
        @endif

        /* Header Glass Effect */
        .navbar,
        header.navbar,
        nav.navbar,
        .navbar-custom,
        header {
            background-color: var(--color-surface-dark, #0f2419) !important;
            opacity: {{ $headerOpacity }};
            backdrop-filter: blur({{ $headerBlur }}px) !important;
            -webkit-backdrop-filter: blur({{ $headerBlur }}px) !important;
            will-change: transform;
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        /* Footer Glass Effect */
        footer,
        .footer,
        footer.footer-nature {
            background-color: var(--color-surface-dark, #0f2419) !important;
            opacity: {{ $footerOpacity }};
            backdrop-filter: blur({{ $headerBlur }}px) !important;
            -webkit-backdrop-filter: blur({{ $headerBlur }}px) !important;
            will-change: transform;
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        @if($textShadowEnabled)
        /* Text Shadows for Better Readability */
        h1, h2, h3, .big-title {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        @endif
    </style>
    @endif

    @stack('styles')

    {{-- Google Analytics --}}
    @if (!empty($gaId))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', '{{ $gaId }}');
    </script>
    @endif

    {{-- Facebook Pixel - only if marketing is allowed --}}
    @if ($isProd && $marketingAllowed && !empty($pixelId))
    <link rel="preconnect" href="https://connect.facebook.net" crossorigin>
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $pixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1" />
    </noscript>
    @endif

</head>

<body class="d-flex flex-column min-vh-100 {{ $bodyClassString }}">
    @if (config('site.public_readonly'))
    <div class="alert alert-warning text-center mb-0 rounded-0">
        {{ __('Site is under maintenance. Registration and purchases are temporarily disabled.') }}
    </div>
    @endif

    @include('partials.header')

    <main class="flex-grow-1">
        {{-- Cart Timer Widget (floating) --}}
        @include('components.cart.timer-widget')

        @yield('content')
    </main>

    @include('partials.footer')

    @if (!request()->routeIs('contact'))
    @include('partials.ws-widget', ['variant' => 'floating'])
    @endif

    {{-- Global JS libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Cart badge --}}
    <script>
        (function() {
            const setBadge = (n) => {
                document.querySelectorAll('.cart-count-badge').forEach(el => {
                    el.textContent = n;
                    el.style.display = n > 0 ? 'inline-block' : 'none';
                });
            };
            window.setCartCount = setBadge;

            const fetchAndSet = async () => {
                const url = document.querySelector('meta[name="cart-count-url"]')?.getAttribute('content');
                if (!url) return;
                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (res.status === 401) {
                        setBadge(0);
                        return;
                    }
                    const data = await res.json();
                    if (typeof data?.count !== 'undefined') setBadge(Number(data.count || 0));
                } catch (_) {}
            };

            fetchAndSet();
            window.addEventListener('cart:changed', fetchAndSet);
        })();
    </script>

    {{-- Global Cart Countdown System --}}
    @auth
    @php
    $userCart = auth()->user()->cart()->where('is_active', true)->latest('cart_id')->first();
    $hasActiveCart = $userCart && $userCart->items()->count() > 0;
    $cartExpiresAt = $hasActiveCart ? $userCart->expires_at : null;
    $cartExpiryMinutes = $hasActiveCart ? $userCart->expiryMinutes() : 0;
    @endphp

    @if ($hasActiveCart)
    <script>
        (function() {
            var DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
            var debug = function() { DEBUG && console.log.apply(console, arguments); };

            var expiresAtStr = @json($cartExpiresAt);
            var totalMinutes = @json($cartExpiryMinutes);

            if (!expiresAtStr) return;

            var serverExpires = new Date(expiresAtStr).getTime();
            var totalSeconds = totalMinutes * 60;

            window.cartCountdown = {
                getRemainingSeconds: function() { return Math.max(0, Math.ceil((serverExpires - Date.now()) / 1000)); },
                getTotalSeconds: function() { return totalSeconds; },
                getExpiresAt: function() { return new Date(serverExpires); },
                isExpired: function() { return window.cartCountdown.getRemainingSeconds() <= 0; }
            };

            // Emit event so widgets know cartCountdown is ready
            window.dispatchEvent(new CustomEvent('cartCountdown:ready'));
            debug('✅ cartCountdown initialized for authenticated user');

            // Auto-expire logic for Auth Users (Global)
            var expireUrl = '{{ route("public.carts.expire") }}';
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            var expirationHandled = false;

            var updateBadgesToZero = function() {
                document.querySelectorAll('.cart-count-badge').forEach(function(b) {
                    b.textContent = '0';
                    b.style.display = 'none';
                });
            };

            var checkExpiration = function() {
                if (window.cartCountdown.isExpired() && !expirationHandled) {
                    expirationHandled = true;
                    debug('Cart expired (auth global check)');
                    updateBadgesToZero();

                    fetch(expireUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }).finally(function() {
                        window.location.reload();
                    });
                }
            };

            setInterval(checkExpiration, 5000);
        })();
    </script>
    @endif
    @endauth

    {{-- cartCountdown for guests --}}
    @guest
    @php
    $guestCartCreated = session('guest_cart_created_at');
    $hasGuestCart = !empty(session('guest_cart_items')) && $guestCartCreated;
    $guestExpiryMinutes = \App\Models\Setting::getValue('cart.expiration_minutes', 30);

    // Calculate home URL outside of script
    $currentLocale = app()->getLocale();
    $homeRoute = $currentLocale . '.home';
    $guestHomeUrl = Route::has($homeRoute) ? route($homeRoute) : url('/');
    @endphp

    @if ($hasGuestCart)
    <script>
        (function() {
            var DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
            var debug = function() { DEBUG && console.log.apply(console, arguments); };

            var createdAt = @json($guestCartCreated);
            var expiryMinutes = @json($guestExpiryMinutes);
            if (!createdAt) return;

            var created = new Date(createdAt).getTime();
            var expires = created + (expiryMinutes * 60 * 1000);
            var totalSeconds = expiryMinutes * 60;

            window.cartCountdown = {
                getRemainingSeconds: function() { return Math.max(0, Math.ceil((expires - Date.now()) / 1000)); },
                getTotalSeconds: function() { return totalSeconds; },
                getExpiresAt: function() { return new Date(expires); },
                isExpired: function() { return window.cartCountdown.getRemainingSeconds() <= 0; }
            };

            // Emit event so widgets know cartCountdown is ready
            window.dispatchEvent(new CustomEvent('cartCountdown:ready'));
            debug('✅ cartCountdown initialized for guest user');

            var hasReloaded = false;
            var expireUrl = '{{ route("public.guest-carts.expire") }}';
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            var homeUrl = '{{ $guestHomeUrl }}';

            var checkExpiration = function() {
                if (window.cartCountdown.isExpired() && !hasReloaded) {
                    hasReloaded = true;
                    fetch(expireUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }).finally(function() {
                        window.location.href = homeUrl;
                    });
                }
            };

            setInterval(checkExpiration, 5000);
        })();
    </script>
    @endif
    @endguest

    {{-- Dynamic theme-color based on footer --}}
    <script>
        (function() {
            const meta = document.querySelector('#themeColorMeta');
            if (!meta) return;

            const TOP_COLOR = '{{ $themeColor }}';
            const FOOTER_COLOR = '{{ $themeColor }}';

            const footer = document.querySelector('.footer-nature');
            if (!footer) return;

            const onScroll = () => {
                const rect = footer.getBoundingClientRect();
                const nearFooter = rect.top < (window.innerHeight * 1.2);
                meta.setAttribute('content', nearFooter ? FOOTER_COLOR : TOP_COLOR);
            };

            document.addEventListener('scroll', onScroll, {
                passive: true
            });
            onScroll();
        })();
    </script>

    @stack('scripts')

    {{-- Signal Alignet modal to close (if payment was completed/cancelled) --}}
    @if (session('close_alignet_modal'))
    <script>
        // Set flag to signal Alignet modal closure across tabs/windows
        try {
            localStorage.setItem('alignet_payment_complete', 'true');
            @if(config('app.debug'))
            console.log('✅ Alignet modal close signal set');
            @endif
        } catch (e) {
            @if(config('app.debug'))
            console.warn('Could not set localStorage signal:', e);
            @endif
        }
    </script>
    @endif

    {{-- Cookie consent banner (logic inside partial handles visibility) --}}
    @include('partials.cookie-consent')

</body>

</html>
