@extends('layouts.app')

<head>
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

@section('content')
<body>

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
</body>
@endsection
