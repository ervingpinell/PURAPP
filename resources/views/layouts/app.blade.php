<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="locale" content="{{ app()->getLocale() }}">

  @php
      $ASSET_ROOT = rtrim(asset(''), '/');
      $pageTitle = $__env->yieldContent('title') ?: 'Green Vacations CR';
      $fullTitle = 'GV | ' . trim($pageTitle);
      $metaDesc  = $__env->yieldContent('meta_description')
                  ?? 'Descubre los mejores tours sostenibles en La Fortuna y Arenal con Green Vacations Costa Rica. Reserva tu aventura con responsabilidad ecológica.';
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
  <meta name="theme-color" content="#0f5132">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  @stack('meta')

  @vite([
    'resources/js/app.js',
    'resources/css/gv.css',
    'resources/css/home.css',
    'resources/css/app.css',
  ])

  @stack('styles')

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

<body class="d-flex flex-column min-vh-100">
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
</body>
</html>
