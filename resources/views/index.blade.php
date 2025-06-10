<!DOCTYPE html>
<html lang="es">
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
            <img src="{{ asset('images/logoCompanyWhite.png') }}"
            alt="Green Vacations"
            style="height: 70px; margin-right: 30px;">
            <strong>Green Vacations</strong>
        </div>
        <div class="navbar-links">
            <a href="#">Home</a>
            <a href="#">Tours</a>
            <!--<a href="#">Restaurant</a>-->
            <a href="#">Reviews</a>
            <a href="#">FAQ</a>
            <a href="#">Contact</a>
            <a href="#">About Us</a>
        </div>
        <div class="navbar-actions">
            <i class="fas fa-shopping-cart" title="Cart"></i>
            @auth
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->full_name }}
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        @if(Auth::user()->id_role === 3)
                            <li><a class="dropdown-item" href="#">Mis reservas</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('user.profile.edit') }}">Mi perfil</a></li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">Cerrar sesión</button>
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
            <i class="fas fa-language" title="Language"></i>
        </div>



    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Discover the Magic of Costa Rica</h1>
            <p class="hero-subtext">Unforgettable eco-adventures in the heart of the rainforest</p>
            <div class="hero-buttons">
                <a href="#" class="btn-primary">Book Now</a>
            </div>
        </div>
    </section>

    <section class="tours-section">
        <h2>Our Tours</h2>
        <div class="tour-cards">
            <div class="card">
                <h3>Half Day Tours</h3>
                <img src="/images/logoCompanyWhite.png" alt="Tour 1">
                <button>See Tour</button>
            </div>
            <div class="card">
                <h3>Full Day Tours</h3>
                <img src="/images/logoCompanyWhite.png" alt="Tour 2">

                <button>See Tour</button>
            </div>
            <!--
            <div class="card">
                <h3>Restaurant</h3>
                <img src="restaurant.jpg" alt="Comida">
                <button>See Menu</button>
            </div>
            -->
        </div>
    </section>

    <section class="compact-testimonials">
        <h2 class="big-title">WHAT OUR VISITORS SAY</h2>
        <div class="testimonial-cards">
            <div class="testimonial-card">
                <div class="rating">★★★★★</div>
                <p class="quote">"Los puentes colgantes fueron una experiencia única. ¡Nuestro guía conocía cada planta y animal!"</p>
                <div class="guest-info">
                    <span class="guest-name">- Ana M., España</span>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="rating">★★★★★</div>
                <p class="quote">"El canopy superó todas mis expectativas. Personal profesional y vistas increíbles del volcán."</p>
                <div class="guest-info">
                    <span class="guest-name">- Carlos R., México</span>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-nature">
        <div class="footer-main-content">
            <div class="footer-brand">
                <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations">
                <p>"Connecting you with the heart of Costa Rica's nature"</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Tours</a></li>
                    <!--<li><a href="#">Restaurant</a></li>-->
                    <li><a href="#">Reviews</a></li>
                </ul>
            </div>
            <div class="contact-info">
                <h4>Contact Us</h4>
                <p>📍La Fortuna, San Carlos, Costa Rica</p>
                <p>📞+506 2479-1471</p>
                <p>📩info@greenvacationscr.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2023 Green Vacations Costa Rica. All rights reserved.
        </div>
    </footer>
</body>
</html>
