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
                    @if(Auth::user()->role_id === 3)
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

        @if(Auth::check() && in_array(Auth::user()->role_id, [1, 2]))
            <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2" style="border-radius: 25px;">
                <i class="fas fa-toolbox"></i> Admin
            </a>
        @endif

        {{-- Dropdown de idiomas: Partial --}}
        @include('partials.language-switcher')

        {{-- Botón de WhatsApp --}}
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
                <li><a href="https://www.tripadvisor.com/Attraction_Review-g309226-d6817241-Reviews-Green_Vacations_Costa_Rica-La_Fortuna_de_San_Carlos_Arenal_Volcano_National_Park_.html">TripAdvisor</a></li>
                <li><a href="https://www.getyourguide.es/green-vacations-costa-rica-s26615/"></a>GetYourGuide</li>
            </ul>
        </div>
        <div class="footer-tours">
            <h4><i class="fas fa-map-signs me-2"></i>{{ __('adminlte::adminlte.our_tours') }}</h4>
            <ul>
                <li class="d-flex align-items-center mb-2">
                    <i class="fas fa-sun text-warning me-2"></i>
                    <a href="#">{{ __('adminlte::adminlte.half_day') }}</a>
                </li>
                <li class="d-flex align-items-center mb-2">
                    <i class="fas fa-mountain text-info me-2"></i>
                    <a href="#">{{ __('adminlte::adminlte.full_day') }}</a>
                </li>
            </ul>
        </div>
        <div class="contact-info">
            <h4>{{ __('adminlte::adminlte.contact_us') }}</h4>
            <p><i class="fas fa-map-marker-alt me-2"></i> La Fortuna, San Carlos, Costa Rica</p>
            <p><i class="fas fa-phone me-2"></i>
                <a href="tel:+50624791471" class="text-white text-decoration-none">(+506) 2479 1471</a>
            </p>
            <p><i class="fas fa-envelope me-2"></i>
                <a href="mailto:info@greenvacationscr.com" class="text-white text-decoration-none">info@greenvacationscr.com</a>
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2023 Green Vacations Costa Rica. {{ __('adminlte::adminlte.rights_reserved') }}
    </div>
</footer>

<!-- Widget flotante de WhatsApp -->
<div x-data="{ isOpen: false, message: '' }" x-cloak class="whatsapp-widget z-50">
    <!-- Panel del chat -->
    <div x-show="isOpen" x-transition class="whatsapp-panel">
        <div class="whatsapp-header">
            <div class="d-flex align-items-center gap-2">
                <div class="whatsapp-icon-circle">
                    <i class="fas fa-comment-dots fa-lg"></i>
                </div>
                <div>
                    <strong>{{ __('adminlte::adminlte.whatsapp_title') }}</strong><br>
                    <small>{{ __('adminlte::adminlte.whatsapp_subtitle') }}</small>
                </div>
            </div>
            <button class="btn btn-sm text-white" @click="isOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-3">
            <div class="whatsapp-greeting">
                <small>{{ __('adminlte::adminlte.whatsapp_greeting') }}</small>
            </div>
            <textarea
                x-model="message"
                class="form-control mb-2"
                rows="3"
                placeholder="{{ __('adminlte::adminlte.whatsapp_placeholder') }}"
            ></textarea>
            <button
                @click="
                    const defaultMsg = @json(__('adminlte::adminlte.whatsapp_placeholder'));
                    window.open(`https://wa.me/24791471?text=${encodeURIComponent(message || defaultMsg)}`, '_blank');
                    isOpen = false;
                    message = '';
                "
                class="btn btn-success w-100"
            >
                <i class="fas fa-paper-plane me-2"></i>{{ __('adminlte::adminlte.whatsapp_button') }}
            </button>
            <p class="text-center text-muted mt-2 mb-0 whatsapp-footer-note">
                {{ __('adminlte::adminlte.whatsapp_footer') }}
            </p>
        </div>
    </div>

    <!-- Botón flotante -->
    <button
        @click="isOpen = !isOpen"
        class="whatsapp-float-btn"
    >
        <template x-if="isOpen">
            <i class="fas fa-times fa-lg"></i>
        </template>
        <template x-if="!isOpen">
            <i class="fab fa-whatsapp fa-lg"></i>
        </template>
    </button>
</div>




<!-- Alpine.js y Font Awesome -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script> {{-- reemplaza 'yourkit.js' si no lo tienes --}}

</body>
@if(session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Acceso Denegado',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    </script>
@endif

</html>
