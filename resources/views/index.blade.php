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
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('{{ asset('images/volcano.png') }}') center/cover no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 2rem;
        }
    </style>
</head>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<body>
<nav class="navbar-custom">
    <div class="navbar-logo">
        <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations" style="height: 70px; margin-right: 30px;">
        <strong>Green Vacations</strong>
    </div>
    <div class="navbar-links">
        <a href="#">{{ __('adminlte::adminlte.home') }}</a>
        <a href="#">{{ __('adminlte::adminlte.tours') }}</a>
        <!--<a href="#">{{ __('adminlte::adminlte.restaurant') }}</a>-->
        <a href="#">{{ __('adminlte::adminlte.reviews') }}</a>
        <a href="#">{{ __('adminlte::adminlte.faq') }}</a>
        <a href="#">{{ __('adminlte::adminlte.contact') }}</a>
        <a href="#">{{ __('adminlte::adminlte.about') }}</a>
    </div>
    <div class="navbar-actions">
        <i class="fas fa-shopping-cart" title="{{ __('adminlte::adminlte.cart') }}"></i>
        @auth
            <div class="dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> {{ Auth::user()->full_name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    @if(Auth::user()->id_role === 3)
                        <li><a class="dropdown-item" href="#">{{ __('adminlte::adminlte.my_reservations') }}</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ route('user.profile.edit') }}">{{ __('adminlte::adminlte.profile') }}</a></li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">{{ __('adminlte::adminlte.logout') }}</button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}">
                <i class="fas fa-user" title="Login"></i>
            </a>
        @endauth
        @if(Auth::check() && in_array(Auth::user()->id_role, [1, 2]))
            <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2" style="border-radius: 25px;">
                <i class="fas fa-toolbox"></i> Admin
            </a>
        @endif
        {{-- Dropdown de idiomas --}}
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{-- Mostrar bandera actual --}}
                @php
                    $flag = match(app()->getLocale()) {
                        'es' => 'es.png',
                        'en' => 'gb.png',
                        'fr' => 'fr.png',
                        default => 'es.png'
                    };
                @endphp
                <img src="{{ asset('images/' . $flag) }}" alt="Idioma actual" width="20" height="15">
                {{ strtoupper(app()->getLocale()) }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('cambiar.idioma', 'es') }}">
                        <img src="{{ asset('images/es.png') }}" width="20" height="15"> Español
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('cambiar.idioma', 'en') }}">
                        <img src="{{ asset('images/gb.png') }}" width="20" height="15"> English
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('cambiar.idioma', 'fr') }}">
                        <img src="{{ asset('images/fr.png') }}" width="20" height="15"> Français
                    </a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">{{ __('adminlte::adminlte.hero_title') }}</h1>
        <p class="hero-subtext">{{ __('adminlte::adminlte.hero_subtext') }}</p>
        <div class="hero-buttons">
            <a href="#" class="btn-primary">{{ __('adminlte::adminlte.book_now') }}</a>
        </div>
    </div>
</section>

<section class="tours-section">
    <h2>{{ __('adminlte::adminlte.our_tours') }}</h2>
    <div class="tour-cards">
        <div class="card">
            <h3>{{ __('adminlte::adminlte.half_day') }}</h3>
            <img src="/images/logoCompanyWhite.png" alt="Tour 1">
            <button>{{ __('adminlte::adminlte.see_tour') }}</button>
        </div>
        <div class="card">
            <h3>{{ __('adminlte::adminlte.full_day') }}</h3>
            <img src="/images/logoCompanyWhite.png" alt="Tour 2">
            <button>{{ __('adminlte::adminlte.see_tour') }}</button>
        </div>
    </div>
</section>

<section class="compact-testimonials">
    <h2 class="big-title">{{ __('adminlte::adminlte.what_visitors_say') }}</h2>
    <div class="testimonial-cards">
        <div class="testimonial-card">
            <div class="rating">★★★★★</div>
            <p class="quote">{{ __('adminlte::adminlte.quote_1') }}</p>
            <div class="guest-info">
                <span class="guest-name">{{ __('adminlte::adminlte.guest_1') }}</span>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="rating">★★★★★</div>
            <p class="quote">{{ __('adminlte::adminlte.quote_2') }}</p>
            <div class="guest-info">
                <span class="guest-name">{{ __('adminlte::adminlte.guest_2') }}</span>
            </div>
        </div>
    </div>
</section>

<footer class="footer-nature">
    <div class="footer-main-content">
        <div class="footer-brand">
            <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations">
            <p>{{ __('adminlte::adminlte.footer_text') }}</p>
        </div>
        <div class="footer-links">
            <h4>{{ __('adminlte::adminlte.quick_links') }}</h4>
            <ul>
                <li><a href="#">{{ __('adminlte::adminlte.home') }}</a></li>
                <li><a href="#">{{ __('adminlte::adminlte.tours') }}</a></li>
                <li><a href="#">{{ __('adminlte::adminlte.reviews') }}</a></li>
            </ul>
        </div>
        <div class="contact-info">
            <h4>{{ __('adminlte::adminlte.contact_us') }}</h4>
            <p>📍{{ __('adminlte::adminlte.location') }}</p>
            <p>📞{{ __('adminlte::adminlte.phone') }}</p>
            <p>📩{{ __('adminlte::adminlte.email') }}</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2023 Green Vacations Costa Rica. {{ __('adminlte::adminlte.rights_reserved') }}
    </div>
</footer>
</body>
</html>
