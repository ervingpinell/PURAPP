<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Green Vacations CR') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts & Styles -->
    @vite([
    'resources/css/gv.css',
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            background-image: url("{{ cdn('backgrounds/auth-bg.jpg') }}");
            /* Fallback or specific bg */
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }

        .auth-overlay {
            background-color: rgba(37, 109, 27, 0.85);
            /* Green overlay */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 28rem;
            /* sm:max-w-md */
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .auth-header {
            text-align: center;
            padding: 2rem 2rem 1rem;
        }

        .auth-logo {
            height: 60px;
            width: auto;
            margin-bottom: 1rem;
        }

        .auth-body {
            padding: 0 2rem 2rem;
        }

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
        }

        .form-control:focus {
            border-color: #256d1b;
            box-shadow: 0 0 0 3px rgba(37, 109, 27, 0.1);
        }

        .btn-primary {
            background-color: #256d1b;
            border-color: #256d1b;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #1a5013;
            border-color: #1a5013;
        }

        .auth-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: #d1d5db;
        }

        .auth-footer a {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="auth-overlay">
        <div class="auth-card">
            <div class="auth-header">
                <a href="/">
                    <img src="{{ cdn('logos/logo-green.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
                </a>
                <h2 class="h5 text-gray-800 font-weight-bold mb-0">@yield('title')</h2>
            </div>

            <div class="auth-body">
                @if (session('status'))
                <div class="alert alert-success mb-4 text-sm" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                @yield('content')
            </div>
        </div>

        <div class="auth-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}.
            <a href="{{ url('/') }}">{{ __('Go to Home') }}</a>
        </div>
    </div>

    @stack('scripts')
</body>

</html>