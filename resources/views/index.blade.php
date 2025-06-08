<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Vacations</title>
    <link rel="icon" href="{{ asset('Resource/greenVacationLogo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #60A862;
            --primary-dark: #256D1B;
            --primary-light: #8BC34A;
            --text-dark: #333;
            --text-light: #e0e0e0;
            --text-gray: #555;
            --white: #fff;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --border-radius: 15px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
            background: #f4f4f4;
            margin: 0;
        }

        .navbar-custom {
            background-color: rgba(10, 25, 15, 0.9);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .navbar-logo {
            display: flex;
            align-items: center;
        }

        .navbar-logo img {
            height: 40px;
            margin-right: 10px;
        }

        .navbar-links {
            display: flex;
            gap: 1.5rem;
        }

        .navbar-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar-links a:hover {
            color: var(--primary-light);
        }

        .navbar-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .navbar-actions i {
            font-size: 1.25rem;
            cursor: pointer;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .navbar-actions i:hover {
            transform: scale(1.1);
            filter: drop-shadow(0 0 5px rgba(96, 168, 98, 0.8));
        }

        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('{{ asset('Resource/hero-background.jpg') }}') center/cover no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .hero-content {
            max-width: 900px;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.75rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #4d8c4f;
            transform: translateY(-3px);
        }

        .tours-section {
            background: white;
            text-align: center;
            padding: 3rem 1rem 0;
        }

        .tour-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
            padding-bottom: 3rem;
        }

        .card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 280px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            margin: 0.5rem 0 1rem;
            font-size: 1.25rem;
            color: var(--primary-dark);
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }

        .card button {
            background-color: var(--primary-dark);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
        }

        .card button:hover {
            background-color: #1c5413;
            transform: scale(1.05);
        }

        .compact-testimonials {
            padding: 3rem 1rem;
            text-align: center;
            background-color: #f8f9fa;
            border-top: none;
            border-bottom: 3px solid #60A862;
        }

        .big-title {
            font-size: 2.5rem;
            color: #256D1B;
            margin-bottom: 2rem;
            font-weight: 700;
        }

        .testimonial-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .testimonial-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            max-width: 350px;
        }

        .rating {
            color: #FFD700;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .quote {
            font-style: italic;
            color: #555;
            margin-bottom: 1rem;
        }

        .guest-name {
            color: #60A862;
            font-weight: 600;
        }

        .footer-nature {
            background-color: #2e7d32;
            color: white;
            padding: 3rem 2rem;
        }

        .footer-nature .footer-main-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
            max-width: 1200px;
            margin: auto;
        }

        .footer-nature .footer-brand img {
            width: 180px;
            margin-bottom: 1rem;
        }

        .footer-nature .footer-links, .footer-nature .contact-info {
            flex: 1;
            min-width: 200px;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div class="navbar-logo">
            <img src="logo.png" alt="Green Vacations">
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
            <i class="fas fa-user" title="Login"></i>
            <i class="fas fa-language" title="Language"></i>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Discover the Magic of Costa Rica</h1>
            <p>Unforgettable eco-adventures in the heart of the rainforest</p>
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
                <img src="tour1.jpg" alt="Tour 1">
                <button>See Tour</button>
            </div>
            <div class="card">
                <h3>Full Day Tours</h3>
                <img src="tour2.jpg" alt="Tour 2">
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
                <img src="logo.png" alt="Green Vacations">
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
                <p>La Fortuna, San Carlos, Costa Rica</p>
                <p>+506 2479-1471</p>
                <p>info@greenvacationscr.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2023 Green Vacations Costa Rica. All rights reserved.
        </div>
    </footer>
</body>
</html>
