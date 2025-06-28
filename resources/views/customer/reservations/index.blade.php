{{-- resources/views/customer/reservations/index.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('My Reservations') }} – Green Vacations</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logoCompanyWhite.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Custom Styles - Consider moving these to a dedicated CSS file like public/css/app.css or public/css/customer.css --}}
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures footer stays at the bottom */
            background-color: #f8f9fa; /* Light background for the page */
        }
        main {
            flex: 1; /* Occupies remaining space */
        }

        /* Navbar Styles */
        .navbar-custom {
            background-color: #0d4b1a; /* Dark green */
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .navbar-logo img {
            height: 70px;
            margin-right: 15px;
        }
        .navbar-links > a,
        .navbar-actions > a,
        .navbar-actions > .dropdown > .nav-link {
        color: white;
        text-decoration: none;
        margin-left: 25px;
        font-size: 1.1rem;
        transition: color 0.3s ease;
        }
        .navbar-links a:hover,
        .navbar-actions a:hover,
        .navbar-actions .dropdown .nav-link:hover {
            color: #a8e6cf; /* Lighter green on hover */
        }
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar-actions .dropdown .nav-link {
            padding: 0;
        }

        /* Flag Icons for Language Switcher */
        .flag-icon {
            width: 20px;
            height: auto;
            margin-right: 8px;
            vertical-align: middle;
            border-radius: 3px;
            box-shadow: 0 0 2px rgba(0,0,0,0.2);
        }
        .navbar-actions .nav-link .flag-icon {
            width: 25px;
            margin-right: 5px;
        }

        /* Reservation Card Styles */
        .card-img-top-custom {
            height: 200px; /* Make image taller for better visual impact */
            object-fit: cover;
            width: 100%;
            border-top-left-radius: calc(0.5rem - 1px);
            border-top-right-radius: calc(0.5rem - 1px);
        }
        .card {
            border: none;
            border-radius: 0.5rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Stronger shadow */
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Pushes button to bottom */
        }
        .card-title {
            font-size: 1.5rem; /* Larger title */
            font-weight: bold;
            margin-bottom: 0.75rem;
            color: #0d4b1a; /* Dark green for titles */
        }
        .card-text i {
            width: 20px; /* Align icons */
            text-align: center;
            margin-right: 8px;
            color: #0d4b1a; /* Icon color */
        }
        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 0.25rem;
            font-weight: bold;
            font-size: 0.9em;
        }
        .badge-pending { background-color: #ffc107; color: #343a40; } /* Warning */
        .badge-confirmed { background-color: #28a745; color: white; } /* Success */
        .badge-cancelled { background-color: #dc3545; color: white; } /* Danger */
        .btn-primary {
            background-color: #0d4b1a;
            border-color: #0d4b1a;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0a3a14;
            border-color: #0a3a14;
        }

        /* Empty State Alert */
        .alert-info {
            background-color: #e2f3e8; /* Light green background for info alert */
            border-color: #b0e0c8;
            color: #0d4b1a;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .alert-heading {
            color: #0d4b1a;
            font-size: 1.75rem;
        }
        .alert-info .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .alert-info .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* WhatsApp Widget */
        .whatsapp-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
        .whatsapp-float-btn {
            background-color: #25D366; /* WhatsApp green */
            color: white;
            border: none;
            border-radius: 50%;
            width: 65px; /* Slightly larger */
            height: 65px;
            font-size: 28px; /* Larger icon */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Deeper shadow */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .whatsapp-float-btn:hover {
            transform: scale(1.15) translateY(-3px); /* More pronounced hover */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }
        .whatsapp-panel {
            position: absolute;
            bottom: 90px;
            right: 0;
            width: 320px; /* Slightly wider */
            background-color: white;
            border-radius: 12px; /* More rounded */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25); /* Stronger shadow */
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .whatsapp-header {
            background-color: #075E54; /* Darker WhatsApp green */
            color: white;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1rem;
        }
        .whatsapp-icon-circle {
            background-color: #25D366;
            border-radius: 50%;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .whatsapp-greeting {
            background-color: #DCF8C6; /* Light chat bubble color */
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        .whatsapp-footer-note {
            font-size: 0.8em;
            color: #888;
        }
        [x-cloak] { display: none !important; }

        /* Footer Styles */
        .footer-nature {
            background-color: #0d4b1a; /* Dark green, consistent with navbar */
            color: white;
            padding: 50px 0 25px; /* More padding */
            font-size: 0.95rem;
            margin-top: 50px; /* Space from main content */
        }
        .footer-main-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            max-width: 1300px; /* Wider content area */
            margin: 0 auto;
            padding: 0 30px;
        }
        .footer-brand,
        .footer-links,
        .footer-tours,
        .contact-info {
            flex: 1;
            min-width: 250px; /* Ensure columns don't get too narrow */
            margin: 20px; /* More margin */
        }
        .footer-brand img {
            height: 90px; /* Larger logo */
            margin-bottom: 20px;
        }
        .footer-brand p {
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.8);
        }
        .footer-links h4,
        .footer-tours h4,
        .contact-info h4 {
            color: #a8e6cf;
            margin-bottom: 25px;
            font-size: 1.3rem; /* Larger headings */
            position: relative;
            padding-bottom: 8px;
        }
        .footer-links h4::after,
        .footer-tours h4::after,
        .contact-info h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px; /* Longer underline */
            height: 3px; /* Thicker underline */
            background-color: #25D366;
        }
        .footer-links ul,
        .footer-tours ul {
            list-style: none;
            padding: 0;
        }
        .footer-links ul li,
        .footer-tours ul li {
            margin-bottom: 12px;
        }
        .footer-links a,
        .footer-tours a,
        .contact-info p a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .footer-links a:hover,
        .footer-tours a:hover,
        .contact-info p a:hover {
            color: #a8e6cf;
        }
        .contact-info p {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            color: rgba(255, 255, 255, 0.8);
        }
        .contact-info p i {
            margin-top: 4px;
            margin-right: 12px;
            color: #25D366; /* Icon color */
        }
        .footer-bottom {
            text-align: center;
            padding-top: 25px;
            margin-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Media queries for footer responsiveness */
        @media (max-width: 992px) {
            .footer-brand,
            .footer-links,
            .footer-tours,
            .contact-info {
                min-width: 45%; /* Two columns on medium screens */
            }
        }
        @media (max-width: 768px) {
            .footer-main-content {
                flex-direction: column;
                align-items: center;
            }
            .footer-brand,
            .footer-links,
            .footer-tours,
            .contact-info {
                margin: 20px 0;
                text-align: center;
                min-width: unset;
                width: 90%;
            }
            .footer-links h4::after,
            .footer-tours h4::after,
            .contact-info h4::after {
                left: 50%;
                transform: translateX(-50%);
            }
            .contact-info p {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
<nav class="navbar-custom">
    <div class="navbar-logo">
        <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations">
        <strong>Green Vacations</strong>
    </div>
    <div class="navbar-links">
        <a href="{{ route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
        <a href="#">{{ __('adminlte::adminlte.tours') }}</a>
        <a href="#">{{ __('adminlte::adminlte.reviews') }}</a>
        <a href="#">{{ __('adminlte::adminlte.faq') }}</a>
        <a href="#">{{ __('adminlte::adminlte.contact') }}</a>
    </div>
    <div class="navbar-actions">
        {{-- Cart Link --}}
        <a href="{{ route('admin.cart.index') }}" class="text-white">
            <i class="fas fa-shopping-cart" title="{{ __('adminlte::adminlte.cart') }}"></i>
        </a>

        @auth
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> {{ Auth::user()->full_name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="min-width:180px;">
                    @if(Auth::user()->role_id === 3)
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('my-reservations') }}">
                        <i class="fas fa-calendar-check me-2 text-success"></i>
                        {{ __('My Reservations') }}
                        </a>
                    </li>
                    @endif

                    <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                        <i class="fas fa-id-card-alt me-2 text-primary"></i>
                        {{ __('Profile') }}
                    </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        {{ __('Logout') }}
                        </button>
                    </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="text-white">
                <i class="fas fa-user" title="Login"></i>
            </a>
        @endauth

        @if(Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {{-- Assuming roles 1 and 2 are for admin --}}
            <a href="{{ route('admin.home') }}"
               class="btn btn-outline-light btn-sm d-flex align-items-center gap-2"
               style="border-radius: 25px;">
                <i class="fas fa-toolbox"></i> Admin
            </a>
        @endif

        {{-- Language Dropdown --}}
        <div class="dropdown">
            @include('partials.language-switcher')
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('switch.language','en') }}">
                <img src="{{ asset('images/gb.png') }}" class="flag-icon"> English
                </a></li>
                <li><a class="dropdown-item" href="{{ route('switch.language','es') }}">
                <img src="{{ asset('images/es.png') }}" class="flag-icon"> Español
                </a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-5">
    <h1 class="mb-4 text-center">{{ __('My Reservations') }}</h1>

    @if($bookings->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            <h4 class="alert-heading">{{ __('No reservations yet!') }}</h4>
            <p>{{ __('It seems you haven\'t booked any adventure with us. Why not explore our amazing tours?') }}</p>
            <hr>
            <a href="{{ url('/') }}" class="btn btn-primary"><i class="fas fa-compass me-2"></i>{{ __('View Available Tours') }}</a>
        </div>
    @else
        {{-- Group and display reservations by status --}}
        @php
            // Ensure bookings is a Collection to use groupBy correctly
            $groupedBookings = collect($bookings)->groupBy('status');
            $statusOrder = ['pending', 'confirmed', 'cancelled']; // Define a display order
        @endphp

        @foreach($statusOrder as $statusKey)
            @if($groupedBookings->has($statusKey) && $groupedBookings[$statusKey]->count() > 0)
                <section class="mb-5">
                    <h3 class="mb-4 text-center">
                        @switch($statusKey)
                            @case('pending')
                                <i class="fas fa-clock text-warning me-2"></i> {{ __('Pending Reservations') }}
                                @break
                            @case('confirmed')
                                <i class="fas fa-check-circle text-success me-2"></i> {{ __('Confirmed Reservations') }}
                                @break
                            @case('cancelled')
                                <i class="fas fa-times-circle text-danger me-2"></i> {{ __('Cancelled Reservations') }}
                                @break
                            @default
                                {{ ucfirst($statusKey) }} {{ __('Reservations') }}
                        @endswitch
                    </h3>
                    <div class="row justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"> {{-- Responsive grid of cards --}}
                        @foreach($groupedBookings[$statusKey] as $booking)
                            <div class="col mx-auto">
                                <div class="card h-100">
                                    {{-- Tour Image --}}
                                    @if(optional($booking->tour)->image_path)
                                        <img src="{{ asset('storage/' . $booking->tour->image_path) }}" class="card-img-top card-img-top-custom" alt="{{ optional($booking->tour)->name }}">
                                    @else
                                        <img src="{{ asset('images/default_tour_placeholder.png') }}" class="card-img-top card-img-top-custom" alt="{{ __('Generic Tour') }}">
                                    @endif
                                    
                                    <div class="card-body">
                                        {{-- Tour Name --}}
                                        <h5 class="card-title">{{ optional($booking->tour)->name ?? __('Unknown Tour') }}</h5>
                                        
                                        {{-- Booking Details --}}
                                        <p class="card-text text-muted small mb-1">
                                            <i class="fas fa-calendar-alt"></i> <strong>{{ __('Booking Date') }}:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                        </p>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="fas fa-calendar-check"></i> <strong>{{ __('Tour Date') }}:</strong> {{ \Carbon\Carbon::parse(optional($booking->detail)->tour_date)->format('M d, Y') }}
                                        </p>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="fas fa-users"></i> <strong>{{ __('Participants') }}:</strong>
                                            {{ optional($booking->detail)->adults_quantity ?? 0 }} {{ __('Adults') }}
                                            @if(optional($booking->detail)->kids_quantity > 0)
                                                , {{ optional($booking->detail)->kids_quantity }} {{ __('Children') }}
                                            @endif
                                        </p>
                                        <p class="card-text text-muted small mb-2">
                                            <i class="fas fa-hotel"></i> <strong>{{ __('Hotel') }}:</strong>
                                            @if(optional($booking->detail)->is_other_hotel)
                                                {{ $booking->detail->other_hotel_name }}
                                            @else
                                                {{ optional(optional($booking->detail)->hotel)->name ?? __('Not specified') }}
                                            @endif
                                        </p>

                                        {{-- Status and Total --}}
                                        <div class="d-flex justify-content-between align-items-center mb-3 mt-auto pt-2">
                                            <div>
                                                <strong>{{ __('Status') }}:</strong>
                                                <span class="badge badge-status 
                                                    @switch($booking->status)
                                                        @case('pending') badge-pending @break
                                                        @case('confirmed') badge-confirmed @break
                                                        @case('cancelled') badge-cancelled @break
                                                        @default bg-secondary @break
                                                    @endswitch">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </div>
                                            <div class="fw-bold fs-5 text-success">
                                                ${{ number_format($booking->total, 2) }}
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="d-grid">
                                        {{-- Ver comprobante --}}
                                        <a href="{{ route('my-reservations.receipt', $booking->booking_id) }}"
                                            class="btn btn-primary btn-lg mb-2">
                                            <i class="fas fa-file-invoice me-2"></i> {{ __('View Receipt') }}
                                        </a>

                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    @endif
</main>

<footer class="footer-nature">
    <div class="footer-main-content">
        <div class="footer-brand">
            <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations">
            <p>{{ __('adminlte::adminlte.footer_text') }}</p>
        </div>
        <div class="footer-links">
            <h4>{{ __('adminlte::adminlte.quick_links') }}</h4>
            <ul>
                <li><a href="{{ route('home') }}">{{ __('adminlte::adminlte.home') }}</a></li>
                <li><a href="#">{{ __('adminlte::adminlte.tours') }}</a></li>
                <li><a href="#">{{ __('adminlte::adminlte.reviews') }}</a></li>
                <li><a href="https://www.tripadvisor.com/Attraction_Review-g309226-d6817241-Reviews-Green_Vacations_Costa_Rica-La_Fortuna_de_San_Carlos_Arenal_Volcano_National_Park_.html" target="_blank" rel="noopener noreferrer">TripAdvisor</a></li>
                <li><a href="https://www.getyourguide.es/green-vacations-costa-rica-s26615/" target="_blank" rel="noopener noreferrer">GetYourGuide</a></li>
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
        &copy; {{ date('Y') }} Green Vacations Costa Rica. {{ __('adminlte::adminlte.rights_reserved') }}
    </div>
</footer>

{{-- WhatsApp Floating Widget --}}
<div x-data="{ isOpen: false, message: '' }" x-cloak class="whatsapp-widget">
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="whatsapp-panel">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
{{-- Using Font Awesome Kit (replace with your actual kit ID if you have one) --}}
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 

@if(session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: '{{ __('Access Denied') }}',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    </script>
@endif

</body>
</html>