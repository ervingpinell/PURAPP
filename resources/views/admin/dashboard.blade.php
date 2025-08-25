@extends('adminlte::page')

@section('title', __('adminlte::adminlte.dashboard.title'))

@section('content_header')
  <div class="mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-body text-center">
        <h3 class="mb-2">
          {{ __('adminlte::adminlte.dashboard.greeting', ['name' => Auth::user()->full_name]) }}
        </h3>
        <p class="mb-0">
          {{ __('adminlte::adminlte.dashboard.welcome_to', ['app' => 'Green Vacations']) }}
          {{ __('adminlte::adminlte.dashboard.hint') }}
        </p>
      </div>
    </div>
  </div>
@stop

@section('content')

  <div class="row">
    <!-- Users -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.users') }}" text="{{ $totalUsers }}" icon="fas fa-users" theme="info"/>
      <a href="{{ route('admin.users.index') }}" class="btn btn-info btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.users') }}
      </a>
    </div>

    <!-- Tours -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.tours') }}" text="{{ $totalTours }}" icon="fas fa-map" theme="warning"/>
      <a href="{{ route('admin.tours.index') }}" class="btn btn-warning btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.tours') }}
      </a>
    </div>

    <!-- Tour Types -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.tour_types') }}" text="{{ $totalTourTypes }}" icon="fas fa-tags" theme="success"/>
      <a href="{{ route('admin.tourtypes.index') }}" class="btn btn-success btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.tour_types') }}
      </a>
    </div>

    <!-- Languages -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.languages') }}" text="{{ $totalLanguages }}" icon="fas fa-globe" theme="primary"/>
      <a href="{{ route('admin.languages.index') }}" class="btn btn-primary btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.languages') }}
      </a>
    </div>

    <!-- Schedules -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.schedules') }}" text="{{ $totalSchedules }}" icon="fas fa-clock" theme="dark"/>
      <a href="{{ route('admin.tours.schedule.index') }}" class="btn btn-dark btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.schedules') }}
      </a>
    </div>

    <!-- Amenities -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.amenities') }}" text="{{ $totalAmenities }}" icon="fas fa-concierge-bell" theme="secondary"/>
      <a href="{{ route('admin.tours.amenities.index') }}" class="btn btn-secondary btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.amenities') }}
      </a>
    </div>

    <!-- Total Bookings -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.total_bookings') }}" text="{{ $totalBookings }}" icon="fas fa-calendar-check" theme="success"/>
      <a href="{{ route('admin.reservas.index') }}" class="btn btn-success btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.bookings') }}
      </a>
    </div>
  </div>

  <!-- Itineraries -->
  <div class="col-md-12 mb-3">
    <div class="card">
      <div class="card-header bg-danger text-white">
        <h4 class="mb-0">{{ __('adminlte::adminlte.sections.available_itineraries') }}</h4>
      </div>
      <div class="card-body">
        @forelse ($itineraries as $itinerary)
          <div class="mb-2">
            <button class="btn btn-outline-danger w-100 text-start"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse{{ $itinerary->itinerary_id }}">
              {{ $itinerary->name }} <i class="fas fa-chevron-down float-end"></i>
            </button>
            <div id="collapse{{ $itinerary->itinerary_id }}" class="collapse mt-2">
              @if ($itinerary->items->isEmpty())
                <p class="text-muted">{{ __('adminlte::adminlte.empty.itinerary_items') }}</p>
              @else
                <ul class="list-group">
                  @foreach ($itinerary->items->sortBy('order') as $item)
                    <li class="list-group-item">
                      <strong>{{ $item->title }}</strong><br>
                      <span class="text-muted">{{ $item->description }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        @empty
          <p class="text-muted">{{ __('adminlte::adminlte.empty.itineraries') }}</p>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Upcoming Bookings -->
  <div class="col-md-12 mb-3">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ __('adminlte::adminlte.sections.upcoming_bookings') }}</h5>
      </div>
      <div class="card-body">
        @forelse ($upcomingBookings as $booking)
          <div class="mb-2">
            <strong>{{ $booking->user->full_name }}</strong>
            – {{ $booking->tour->name ?? $booking->detail->tour->name ?? '' }}<br>
            <small class="text-muted">{{ __('adminlte::adminlte.labels.reference') }}: {{ $booking->booking_reference }}</small><br>
            <span class="text-muted">{{ __('adminlte::adminlte.labels.date') }}: {{ $booking->detail->tour_date->format('d/m/Y') }}</span>
          </div>
          <hr>
        @empty
          <p class="text-muted">{{ __('adminlte::adminlte.empty.upcoming_bookings') }}</p>
        @endforelse
      </div>
    </div>
  </div>

@stop
