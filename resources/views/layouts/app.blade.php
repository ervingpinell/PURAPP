{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Route as RouteFacade;

$appLocale = str_replace('_','-', app()->getLocale() ?? 'es');
$ASSET_ROOT = rtrim(asset(''), '/');

$pageTitle = trim($__env->yieldContent('title') ?? '');
$fullTitle = 'GV | ' . ($pageTitle !== '' ? $pageTitle : 'Green Vacations CR');

$metaDescSlot = $__env->yieldContent('meta_description');
$metaDesc = $metaDescSlot ?: 'Descubre los mejores tours sostenibles en La Fortuna y Arenal con Green Vacations Costa Rica. Reserva tu aventura con responsabilidad ecológica.';

$isProd = app()->environment('production');

$consentCookie = request()->cookie('gv_cookie_consent');
$hasConsent = ! is_null($consentCookie);
$cookiesOk = ($consentCookie === '1') || (bool) session('cookies.accepted', false);

$gaId = config('services.google.analytics_id');
$pixelId = config('services.meta.pixel_id');

$themeColor = $__env->yieldContent('theme_color') ?: '#0f2419';
$isHome = request()->routeIs('home');

$homeEs = function_exists('localized_route') ? localized_route('home', 'es') : url('/');
$homeEn = function_exists('localized_route') ? localized_route('home', 'en') : url('/en');
$homeFr = function_exists('localized_route') ? localized_route('home', 'fr') : url('/fr');
$homeDe = function_exists('localized_route') ? localized_route('home', 'de') : url('/de');
$homePt = function_exists('localized_route') ? localized_route('home', 'pt_BR') : url('/pt');

// Ruta del contador de carrito con fallback seguro
if (RouteFacade::has('cart.count')) {
    $cartCountUrl = route('cart.count');
} elseif (RouteFacade::has('cart.count.public')) {
    $cartCountUrl = route('cart.count.public');
} else {
    $cartCountUrl = url('/cart/count');
}

// Determinar body classes dinámicamente
$bodyClasses = [];

// Agregar clase según ruta
if (request()->routeIs('public.checkout.show')) {
    $bodyClasses[] = 'checkout-page';
} elseif (request()->routeIs('payment.process')) {
    $bodyClasses[] = 'payment-page';
}

// Agregar clase home si es necesario
if ($isHome) {
    $bodyClasses[] = 'is-home';
}

// Agregar clases adicionales desde la sección
$additionalBodyClass = trim($__env->yieldContent('body_class') ?? '');
if ($additionalBodyClass) {
    $bodyClasses[] = $additionalBodyClass;
}

$bodyClassString = implode(' ', array_unique(array_filter($bodyClasses)));
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
    <meta name="keywords" content="tours Costa Rica, turismo ecológico, La Fortuna, Arenal, viajes sostenibles, Green Vacations CR">
    <link rel="canonical" href="{{ url()->current() }}">

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
    <meta property="og:site_name" content="Green Vacations Costa Rica">
    <meta property="og:locale" content="{{ $appLocale }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $ASSET_ROOT }}/images/og-image.jpg">

    {{-- Favicon / PWA --}}
    <link rel="icon" href="{{ $ASSET_ROOT }}/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ $ASSET_ROOT }}/favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ $ASSET_ROOT }}/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $ASSET_ROOT }}/apple-touch-icon.png">
    <link rel="manifest" href="{{ $ASSET_ROOT }}/site.webmanifest">

    <meta id="themeColorMeta" name="theme-color" content="{{ $themeColor }}">
    <meta name="theme-color" content="#0b2e13" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    @if ($isHome)
    <style>
        html {
            background-color: {{ $themeColor }};
        }

        @supports (padding: max(0px)) {
            body {
                padding-bottom: max(0px, env(safe-area-inset-bottom));
            }
        }
    </style>
    @endif

    {{-- Iconos y Bootstrap público --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('meta')

    @vite([
        'resources/js/app.js',
        'resources/css/gv.css',
        'resources/css/app.css',
        'resources/css/checkout.css',
    ])

    @stack('styles')

    {{-- GA / Pixel solo si hay consentimiento --}}
    @if ($isProd && $cookiesOk)
        @if (!empty($gaId))
        <link rel="preconnect" href="https://www.google-analytics.com" crossorigin>
        <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}', { 'anonymize_ip': true });
        </script>
        @endif

        @if (!empty($pixelId))
        <link rel="preconnect" href="https://connect.facebook.net" crossorigin>
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $pixelId }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1" />
        </noscript>
        @endif
    @endif

    {{-- JSON-LD --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'TravelAgency',
        'name' => 'Green Vacations Costa Rica',
        'url' => url('/'),
        'logo' => $ASSET_ROOT.'/images/logo.png',
        'sameAs' => [
            'https://www.facebook.com/greenvacationscr',
            'https://www.instagram.com/greenvacationscr',
        ],
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'La Fortuna',
            'addressRegion' => 'Alajuela',
            'addressCountry' => 'CR',
        ],
        'description' => $metaDesc,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

</head>

<body class="d-flex flex-column min-vh-100 {{ $bodyClassString }}">
    @if (config('gv.public_readonly'))
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

    @if (! request()->routeIs('contact'))
        @include('partials.ws-widget', ['variant' => 'floating'])
    @endif

    {{-- Librerías JS globales --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Badge del carrito --}}
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
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
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
    @endphp

    @if ($hasActiveCart)
    <script>
        (function() {
            const expiresAtStr = @json($userCart->expires_at);
            const totalMinutes = @json($userCart->expiryMinutes());

            if (!expiresAtStr) return;

            let serverExpires = new Date(expiresAtStr).getTime();
            const totalSeconds = totalMinutes * 60;

            window.cartCountdown = {
                getRemainingSeconds: () => Math.max(0, Math.ceil((serverExpires - Date.now()) / 1000)),
                getTotalSeconds: () => totalSeconds,
                getExpiresAt: () => new Date(serverExpires),
                isExpired: () => window.cartCountdown.getRemainingSeconds() <= 0
            };
        })();
    </script>
    @endif
    @endauth

    {{-- cartCountdown para invitados --}}
    @guest
    @php
        $guestCartCreated = session('guest_cart_created_at');
        $hasGuestCart = !empty(session('guest_cart_items')) && $guestCartCreated;
    @endphp

    @if($hasGuestCart)
    <script>
        (function() {
            const createdAt = @json($guestCartCreated);
            const expiryMinutes = {{ \App\Models\Setting::getValue('cart.expiration_minutes', 30) }};
            if (!createdAt) return;

            const created = new Date(createdAt).getTime();
            const expires = created + (expiryMinutes * 60 * 1000);
            const totalSeconds = expiryMinutes * 60;

            window.cartCountdown = {
                getRemainingSeconds: () => Math.max(0, Math.ceil((expires - Date.now()) / 1000)),
                getTotalSeconds: () => totalSeconds,
                getExpiresAt: () => new Date(expires),
                isExpired: () => window.cartCountdown.getRemainingSeconds() <= 0
            };

            let hasReloaded = false;
            const expireUrl = '{{ route("public.guest-carts.expire") }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const homeUrl = '{{ route(app()->getLocale() . ".home") }}';

            const checkExpiration = () => {
                if (window.cartCountdown.isExpired() && !hasReloaded) {
                    hasReloaded = true;
                    fetch(expireUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }).finally(() => {
                        window.location.href = homeUrl;
                    });
                }
            };

            setInterval(checkExpiration, 5000);
        })();
    </script>
    @endif
    @endguest

    {{-- Theme-color dinámico según footer --}}
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

            document.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>

    @stack('scripts')

    {{-- Signal Alignet modal to close (if payment was completed/cancelled) --}}
    @if(session('close_alignet_modal'))
    <script>
        // Set flag to signal Alignet modal closure across tabs/windows
        try {
            localStorage.setItem('alignet_payment_complete', 'true');
            console.log('🔔 Señal de cierre de modal Alignet establecida');
        } catch (e) {
            console.warn('No se pudo establecer señal de localStorage:', e);
        }
    </script>
    @endif

    {{-- Banner de cookies solo si aún no hay decisión --}}
    @if (! $hasConsent)
        @include('partials.cookie-consent')
    @endif
</body>

</html>
