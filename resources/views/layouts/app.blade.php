
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Vacations</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logoCompanyWhite.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@vite([
  'resources/css/home.css',
  'resources/js/public.js',
])

      @stack('styles') <!-- 👈 Esto habilita tus CSS adicionales -->

</head>

<body>
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>
    @stack('scripts')

    @if(session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Acceso Denegado',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });

document.addEventListener('DOMContentLoaded', () => {
  const badgeEls = document.querySelectorAll('.cart-count-badge');

  function updateCartCount() {
    fetch('{{ route('public.cart.count') }}')
      .then(res => res.json())
      .then(data => {
        badgeEls.forEach(el => {
          el.textContent = data.count;
          el.style.display = data.count > 0 ? 'inline-block' : 'none';
        });
      })
      .catch(err => console.error('Error al obtener la cantidad del carrito', err));
  }

  // Llamada inicial
  updateCartCount();

  // Puedes llamar a `updateCartCount()` manualmente cuando se agregue un ítem vía JS/AJAX.
});
    </script>
@endif
</body>
</html>
