<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="locale" content="{{ app()->getLocale() }}">

  @php
      $ASSET_ROOT = rtrim(asset(''), '/');
      $pageTitle  = $__env->yieldContent('title') ?: 'Green Vacations CR';
      $fullTitle  = 'GV | ' . trim($pageTitle);
      $metaDesc   = $__env->yieldContent('meta_description')
                  ?? 'Descubre los mejores tours sostenibles en La Fortuna y Arenal con Green Vacations Costa Rica. Reserva tu aventura con responsabilidad ecológica.';

      $isProd = app()->environment('production');

      // ⚠️ Nuevo: separar "hay decisión" de "aceptado"
      $consentCookie = request()->cookie('gv_cookie_consent'); // null | '0' | '1'
      $hasConsent    = !is_null($consentCookie);               // decidió o no
      $cookiesOk     = ($consentCookie === '1') || (bool) session('cookies.accepted', false); // aceptado

      $gaId    = config('services.google.analytics_id');
      $pixelId = config('services.meta.pixel_id');

      // Color de tinte por defecto (usa el verde del footer)
      $themeColor = '#0f2419';
      // Si quieres un color distinto por página, puedes setear $themeColor vía @section('theme_color', '#xxxxxx')
      $themeColor = $__env->yieldContent('theme_color') ?: $themeColor;

      // Detecta si es la Home para activar fondo sólido y evitar “flash” blanco
      $isHome = request()->routeIs('home');
  @endphp

  <title>{{ $fullTitle }}</title>
  <meta name="description" content="{{ $metaDesc }}">
  <meta name="keywords" content="tours Costa Rica, turismo ecológico, La Fortuna, Arenal, viajes sostenibles, Green Vacations CR">
  <link rel="canonical" href="{{ url()->current() }}">

  @php
    $homeEs = function_exists('localized_route') ? localized_route('home','es') : url('/');
    $homeEn = function_exists('localized_route') ? localized_route('home','en') : url('/en');
    $homeFr = function_exists('localized_route') ? localized_route('home','fr') : url('/fr');
    $homeDe = function_exists('localized_route') ? localized_route('home','de') : url('/de');
    $homePt = function_exists('localized_route') ? localized_route('home','pt_BR') : url('/pt');
  @endphp
  <link rel="alternate" hreflang="es" href="{{ $homeEs }}">
  <link rel="alternate" hreflang="en" href="{{ $homeEn }}">
  <link rel="alternate" hreflang="fr" href="{{ $homeFr }}">
  <link rel="alternate" hreflang="de" href="{{ $homeDe }}">
  <link rel="alternate" hreflang="pt-BR" href="{{ $homePt }}">
  <link rel="alternate" hreflang="x-default" href="{{ $homeEs }}">

  <meta property="og:title" content="{{ $fullTitle }}">
  <meta property="og:description" content="{{ $metaDesc }}">
  <meta property="og:image" content="{{ $ASSET_ROOT }}/images/og-image.jpg">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Green Vacations Costa Rica">
  <meta property="og:locale" content="{{ app()->getLocale() }}">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $fullTitle }}">
  <meta name="twitter:description" content="{{ $metaDesc }}">
  <meta name="twitter:image" content="{{ $ASSET_ROOT }}/images/og-image.jpg">

  <link rel="icon" href="{{ $ASSET_ROOT }}/favicon.ico" sizes="any">
  <link rel="icon" type="image/svg+xml" href="{{ $ASSET_ROOT }}/favicon.svg">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ $ASSET_ROOT }}/favicon-96x96.png">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ $ASSET_ROOT }}/apple-touch-icon.png">
  <link rel="manifest" href="{{ $ASSET_ROOT }}/site.webmanifest">

  {{-- iOS/Safari: tinte de la barra y PWA status bar --}}
  <meta id="themeColorMeta" name="theme-color" content="{{ $themeColor }}">
  <meta name="theme-color" content="#0b2e13" media="(prefers-color-scheme: dark)">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

  {{-- CSS críticos mínimos para evitar flash blanco en iOS en la Home --}}
  @if ($isHome)
  <style>
    html { background-color: {{ $themeColor }}; }
    @@supports (padding: max(0px)) {
      body { padding-bottom: max(0px, env(safe-area-inset-bottom)); }
    }
  </style>
  @endif

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  @stack('meta')

  @vite([
    'resources/js/app.js',
    'resources/css/gv.css',
    'resources/css/app.css',
  ])

  @stack('styles')

  {{-- ✅ Inyectar analítica SOLO si aceptó --}}
  @if ($isProd && $cookiesOk)
      @if (!empty($gaId))
          <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
          <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '{{ $gaId }}', { 'anonymize_ip': true });
          </script>
      @endif

      @if (!empty($pixelId))
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
                   src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"/>
          </noscript>
      @endif
  @endif

  <script type="application/ld+json">
  {!! json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'TravelAgency',
    'name'     => 'Green Vacations Costa Rica',
    'url'      => url('/'),
    'logo'     => $ASSET_ROOT . '/images/logo.png',
    'sameAs'   => [
      'https://www.facebook.com/greenvacationscr',
      'https://www.instagram.com/greenvacationscr',
    ],
    'address'  => [
      '@type'           => 'PostalAddress',
      'addressLocality' => 'La Fortuna',
      'addressRegion'   => 'Alajuela',
      'addressCountry'  => 'CR',
    ],
    'description' => $metaDesc,
  ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
  </script>
</head>

{{-- Añadimos clase is-home para estilos específicos si estás en la Home --}}
<body class="d-flex flex-column min-vh-100 {{ $isHome ? 'is-home' : '' }}">

    @if(config('gv.public_readonly'))
  <div class="alert alert-warning text-center mb-0 rounded-0">
    {{ __('Site is under maintenance. Registration and purchases are temporarily disabled.') }}
  </div>
@endif

  @include('partials.header')

  <main class="flex-grow-1">
    @yield('content')
  </main>

  @include('partials.footer')

  @if (!request()->routeIs('contact'))
    @include('partials.ws-widget', ['variant' => 'floating'])
  @endif

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <script>
    window.setCartCount = function(count) {
      const n = Number(count || 0);
      document.querySelectorAll('.cart-count-badge').forEach(el => {
        el.textContent = n;
        el.style.display = n > 0 ? 'inline-block' : 'none';
      });
    };

    // (Opcional) Cambiar theme-color cuando te acercas al footer (para evitar contraste raro)
    (function(){
      const meta = document.querySelector('#themeColorMeta');
      if(!meta) return;

      const TOP_COLOR = '{{ $themeColor }}';
      const FOOTER_COLOR = '{{ $themeColor }}'; // mismo verde; cambia si tu footer difiere

      const footer = document.querySelector('.footer-nature');
      if(!footer) return;

      const onScroll = () => {
        const rect = footer.getBoundingClientRect();
        const nearFooter = rect.top < (window.innerHeight * 1.2);
        meta.setAttribute('content', nearFooter ? FOOTER_COLOR : TOP_COLOR);
      };

      document.addEventListener('scroll', onScroll, {passive:true});
      onScroll();
    })();
  </script>

  @stack('scripts')

  @if (session('error'))
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Acceso Denegado',
        text: @json(session('error')),
        confirmButtonColor: '#d33'
      });
    </script>
  @endif

  {{-- ✅ Mostrar banner SOLO si NO hay decisión aún --}}
  @if (! $hasConsent)
      @include('partials.cookie-consent')
  @endif
</body>
</html>
