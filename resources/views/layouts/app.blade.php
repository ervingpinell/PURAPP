<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php
      // Prefijo absoluto hacia /public (evita problemas en subcarpetas tipo /Project-Green-Vacation/public)
      $ASSET_ROOT = rtrim(asset(''), '/');

      // Título con prefijo fijo "GV | "
      $pageTitle = $__env->yieldContent('title') ?: 'Green Vacations CR';
      $fullTitle = 'GV | ' . trim($pageTitle);
  @endphp
  <title>{{ $fullTitle }}</title>

  {{-- Favicons (archivos en /public) --}}
  <link rel="icon" href="{{ $ASSET_ROOT }}/favicon.ico" sizes="any">
  <link rel="icon" type="image/svg+xml" href="{{ $ASSET_ROOT }}/favicon.svg">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ $ASSET_ROOT }}/favicon-96x96.png">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ $ASSET_ROOT }}/apple-touch-icon.png">
  <link rel="manifest" href="{{ $ASSET_ROOT }}/site.webmanifest">
  <meta name="theme-color" content="#0f5132">

  {{-- Estilos externos --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- Meta opcionales (por vista) --}}
  @stack('meta')

  {{-- Vite: CSS y JS base --}}
  @vite([
    'resources/js/public.js',
    'resources/css/gv.css',
    'resources/css/home.css',
  ])

  {{-- Estilos adicionales (por vista) --}}
  @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
  @include('partials.header')

  <main class="flex-grow-1">
    @yield('content')
  </main>

  @include('partials.footer')

  {{-- WhatsApp flotante en todo el sitio EXCEPTO en /contact --}}
  @unless (request()->routeIs('contact'))
    @include('partials.ws-widget', [
      'variant' => 'floating',
      // 'phone' => '50624791471',
      // 'defaultMsg' => __('adminlte::adminlte.whatsapp_placeholder'),
    ])
  @endunless

  {{-- Scripts externos --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Helper global para actualizar el badge del carrito --}}
  <script>
    window.setCartCount = function(count) {
      const n = Number(count || 0);
      document.querySelectorAll('.cart-count-badge').forEach(el => {
        el.textContent = n;
        el.style.display = n > 0 ? 'inline-block' : 'none';
      });
    };
  </script>

  {{-- Scripts adicionales (por vista) --}}
  @stack('scripts')

  {{-- Error con SweetAlert por sesión --}}
  @if(session('error'))
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
