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
        main { flex: 1; }

        /* Reservation Card Styles */
        .card-img-top-custom { height: 200px; object-fit: cover; width: 100%; border-top-left-radius: calc(0.5rem - 1px); border-top-right-radius: calc(0.5rem - 1px); }
        .card { border: none; border-radius: 0.5rem; transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
        .card-body { padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; }
        .card-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 0.75rem; color: #0d4b1a; }
        .card-text i { width: 20px; text-align: center; margin-right: 8px; color: #0d4b1a; }
        .badge-status { padding: 0.5em 0.8em; border-radius: 0.25rem; font-weight: bold; font-size: 0.9em; }
        .badge-pending { background-color: #ffc107; color: #343a40; }
        .badge-confirmed { background-color: #28a745; color: #fff; }
        .badge-cancelled { background-color: #dc3545; color: #fff; }
        .btn-primary { background-color: #0d4b1a; border-color: #0d4b1a; transition: background-color 0.3s ease, border-color 0.3s ease; }
        .btn-primary:hover { background-color: #0a3a14; border-color: #0a3a14; }

        /* Empty State Alert */
        .alert-info { background-color: #e2f3e8; border-color: #b0e0c8; color: #0d4b1a; padding: 2rem; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .alert-heading { color: #0d4b1a; font-size: 1.75rem; }
        .alert-info .btn-primary { background-color: #28a745; border-color: #28a745; }
        .alert-info .btn-primary:hover { background-color: #218838; border-color: #1e7e34; }

        /* Footer responsiveness */
        @media (max-width: 992px) {
            .footer-brand, .footer-links, .footer-tours, .contact-info { min-width: 45%; }
        }
        @media (max-width: 768px) {
            .footer-main-content { flex-direction: column; align-items: center; }
            .footer-brand, .footer-links, .footer-tours, .contact-info { margin: 20px 0; text-align: center; min-width: unset; width: 90%; }
            .footer-links h4::after, .footer-tours h4::after, .contact-info h4::after { left: 50%; transform: translateX(-50%); }
            .contact-info p { justify-content: center; }
        }
    </style>
</head>

@section('content')
<body>
<main class="container my-5">
    <h1 class="mb-4 text-center">{{ __('adminlte::adminlte.my_reservations') }}</h1>

    @if($bookings->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            <h4 class="alert-heading">{{ __('adminlte::adminlte.no_reservations_yet') }}</h4>
            <p>{{ __('adminlte::adminlte.no_reservations_message') }}</p>
            <hr>
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fas fa-compass me-2"></i>{{ __('adminlte::adminlte.view_available_tours') }}
            </a>
        </div>
    @else
        @php
            $groupedBookings = collect($bookings)->groupBy('status');
            $statusOrder = ['pending', 'confirmed', 'cancelled'];
        @endphp

        @foreach($statusOrder as $statusKey)
            @if($groupedBookings->has($statusKey) && $groupedBookings[$statusKey]->count() > 0)
                <section class="mb-5">
                    <h3 class="mb-4 text-center">
                        @switch($statusKey)
                            @case('pending')
                                <i class="fas fa-clock text-warning me-2"></i> {{ __('adminlte::adminlte.pending_reservations') }}
                                @break
                            @case('confirmed')
                                <i class="fas fa-check-circle text-success me-2"></i> {{ __('adminlte::adminlte.confirmed_reservations') }}
                                @break
                            @case('cancelled')
                                <i class="fas fa-times-circle text-danger me-2"></i> {{ __('adminlte::adminlte.cancelled_reservations') }}
                                @break
                            @default
                                {{ __('adminlte::adminlte.reservations_generic') }}
                        @endswitch
                    </h3>

                    <div class="row justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach($groupedBookings[$statusKey] as $booking)
                            <div class="col mx-auto">
                                <div class="card h-100">
                                    {{-- Tour Image --}}
                                    @if(optional($booking->tour)->image_path)
                                        <img src="{{ asset('storage/' . $booking->tour->image_path) }}" class="card-img-top card-img-top-custom" alt="{{ optional($booking->tour)->name }}">
                                    @else
                                        <img src="{{ asset('images/volcano.png') }}" class="card-img-top card-img-top-custom" alt="{{ __('adminlte::adminlte.generic_tour') }}">
                                    @endif

                                    <div class="card-body">
                                        {{-- Tour Name --}}
                                        <h5 class="card-title">{{ optional($booking->tour)->name ?? __('adminlte::adminlte.unknown_tour') }}</h5>

                                        {{-- Booking Details --}}
                                        <p class="card-text text-muted small mb-1">
                                            <i class="fas fa-calendar-alt"></i>
                                            <strong>{{ __('adminlte::adminlte.booking_date') }}:</strong>
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                        </p>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="fas fa-calendar-check"></i>
                                            <strong>{{ __('adminlte::adminlte.tour_date') }}:</strong>
                                            {{ \Carbon\Carbon::parse(optional($booking->detail)->tour_date)->format('M d, Y') }}
                                        </p>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="fas fa-users"></i>
                                            <strong>{{ __('adminlte::adminlte.participants') }}:</strong>
                                            {{ optional($booking->detail)->adults_quantity ?? 0 }} {{ __('adminlte::adminlte.adults') }}
                                            @if(optional($booking->detail)->kids_quantity > 0)
                                                , {{ optional($booking->detail)->kids_quantity }} {{ __('adminlte::adminlte.children') }}
                                            @endif
                                        </p>
                                        <p class="card-text text-muted small mb-2">
                                            <i class="fas fa-hotel"></i>
                                            <strong>{{ __('adminlte::adminlte.hotel') }}:</strong>
                                            @if(optional($booking->detail)->is_other_hotel)
                                                {{ $booking->detail->other_hotel_name }}
                                            @else
                                                {{ optional(optional($booking->detail)->hotel)->name ?? __('adminlte::adminlte.not_specified') }}
                                            @endif
                                        </p>

                                        {{-- Status and Total --}}
                                        <div class="d-flex justify-content-between align-items-center mb-3 mt-auto pt-2">
                                            <div>
                                                <strong>{{ __('adminlte::adminlte.status') }}:</strong>
                                                <span class="badge badge-status
                                                    @switch($booking->status)
                                                        @case('pending') badge-pending @break
                                                        @case('confirmed') badge-confirmed @break
                                                        @case('cancelled') badge-cancelled @break
                                                        @default bg-secondary @break
                                                    @endswitch">
                                                    @switch($booking->status)
                                                        @case('pending')   {{ __('adminlte::adminlte.status_pending') }} @break
                                                        @case('confirmed') {{ __('adminlte::adminlte.status_confirmed') }} @break
                                                        @case('cancelled') {{ __('adminlte::adminlte.status_cancelled') }} @break
                                                        @default {{ __('adminlte::adminlte.status_unknown') }} @break
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="fw-bold fs-5 text-success">
                                                ${{ number_format($booking->total, 2) }}
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="d-grid">
                                            <a href="{{ route('my-reservations.receipt', $booking->booking_id) }}" class="btn btn-primary btn-lg mb-2">
                                                <i class="fas fa-file-invoice me-2"></i> {{ __('adminlte::adminlte.view_receipt') }}
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
