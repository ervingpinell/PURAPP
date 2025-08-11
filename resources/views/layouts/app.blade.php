<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Green Vacations</title>

    {{-- Favicon --}}
<link rel="icon" href="{{ asset('favicons/favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" href="{{ asset('favicons/favicon-32x32.png') }}">
<link rel="apple-touch-icon" href="{{ asset('favicons/apple-touch-icon.png') }}">


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
    {{-- Contenido principal --}}
 <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Scripts externos --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Scripts adicionales (por vista) --}}
    @stack('scripts')

    {{-- Error con SweetAlert --}}
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Acceso Denegado',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d33'
            });
        </script>
    @endif

    {{-- Actualización de cantidad del carrito --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const badgeEls = document.querySelectorAll('.cart-count-badge');

            function updateCartCount() {
                fetch('{{ route('cart.count.public') }}')
                    .then(res => res.json())
                    .then(data => {
                        badgeEls.forEach(el => {
                            el.textContent = data.count;
                            el.style.display = data.count > 0 ? 'inline-block' : 'none';
                        });
                    })
                    .catch(err => console.error('Error al obtener la cantidad del carrito', err));
            }

            updateCartCount();
        });
    </script>
</body>
</html>
