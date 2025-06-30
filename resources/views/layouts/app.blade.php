
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Vacations</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logoCompanyWhite.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url("{{ asset('images/volcano.png') }}") center/cover no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 2rem;
        }
        .overview-text {
            max-height: 4.5em; /* Altura para unas 3 líneas, ajusta según tu fuente */
            overflow: hidden;
            position: relative;
            transition: max-height 0.3s ease;
        }
        /* El botón para mostrar más / menos */
        .toggle-label {
            display: inline-block;
            color: rgb(4, 0, 255);
            cursor: pointer;
            margin-top: 0.5em;
        }
        /* Checkbox oculto */
        input.toggle-overview {
            display: none;
        }
        .overview-container {
            display: -webkit-box;
            -webkit-line-clamp: 3; /* muestra 3 líneas */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: none; /* quita max-height */
            transition: max-height 0.3s ease;
            position: relative;
        }
        input.toggle-overview:checked ~ .overview-container {
            display: block; /* mostrar todo el texto expandido */
            max-height: none;
            -webkit-line-clamp: unset;
            overflow: visible;
        }
        /* El label es un botón que se muestra debajo del texto */
        .toggle-label {
            display: block;
            margin-top: 0.25em;
            color: rgb(17, 0, 255);
            cursor: pointer;
            user-select: none;
            text-align: center;
        }
    </style>
</head>

<body>
    @include('partials.header') 
    
    <main>
        @yield('content') 
    </main>
    
    @include('partials.footer') 
    
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
    </script>
@endif
</body>
</html>
