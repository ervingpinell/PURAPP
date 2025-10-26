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
          {{ __('adminlte::adminlte.dashboard.welcome_to', ['app' => 'Green Vacations CR']) }}
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
      <x-adminlte-info-box
        title="{{ __('adminlte::adminlte.entities.users') }}"
        text="{{ $totalUsers ?? 0 }}"
        icon="fas fa-users"
        theme="info"
      />
      <a href="{{ route('admin.users.index') }}" class="btn btn-info btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.users') }}
      </a>
    </div>

    <!-- Tours -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box
        title="{{ __('adminlte::adminlte.entities.tours') }}"
        text="{{ $totalTours ?? 0 }}"
        icon="fas fa-map"
        theme="warning"
      />
      <a href="{{ route('admin.tours.index') }}" class="btn btn-warning btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.tours') }}
      </a>
    </div>

    <!-- Tour Types -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box
        title="{{ __('adminlte::adminlte.entities.tour_types') }}"
        text="{{ $totalTourTypes ?? ($tourTypes ?? 0) }}"
        icon="fas fa-tags"
        theme="success"
      />
      <a href="{{ route('admin.tourtypes.index') }}" class="btn btn-success btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.tour_types') }}
      </a>
    </div>

    <!-- Languages -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box
        title="{{ __('adminlte::adminlte.entities.languages') }}"
        text="{{ $totalLanguages ?? 0 }}"
        icon="fas fa-globe"
        theme="primary"
      />
      <a href="{{ route('admin.languages.index') }}" class="btn btn-primary btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.languages') }}
      </a>
    </div>

    <!-- Schedules -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box
        title="{{ __('adminlte::adminlte.entities.schedules') }}"
        text="{{ $totalSchedules ?? 0 }}"
        icon="fas fa-clock"
        theme="dark"
      />
      <a href="{{ route('admin.tours.schedule.index') }}" class="btn btn-dark btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.schedules') }}
      </a>
    </div>

    <!-- Amenidades -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box
        title="Amenidades"
        text="{{ $totalAmenities ?? 0 }}"
        icon="fas fa-concierge-bell"
        theme="secondary"
      />
      <a href="{{ route('admin.tours.amenities.index') }}" class="btn btn-secondary btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} Amenidades
      </a>
    </div>

    <!-- Total Bookings -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box
        title="{{ __('adminlte::adminlte.entities.total_bookings') }}"
        text="{{ $totalBookings ?? 0 }}"
        icon="fas fa-calendar-check"
        theme="success"
      />
      <a href="{{ route('admin.bookings.index') }}" class="btn btn-success btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.bookings') }}
      </a>
    </div>
  </div>

  <!-- Tours Disponibles (mismo diseño; toggle propio para abrir/cerrar) -->
  <div class="row">
    <div class="col-md-12 mb-3">
      <div class="card">
        <div class="card-header bg-danger text-white">
          <h4 class="mb-0">Tours Disponibles</h4>
        </div>
        <div class="card-body">
          @forelse (($itineraries ?? collect()) as $itinerary)
            @php
              $cid = 'collapseItin_' . $itinerary->itinerary_id;
            @endphp

            <div class="mb-2">
              <button
                type="button"
                class="btn btn-outline-danger btn-block text-center js-simple-toggle"
                data-target="#{{ $cid }}"
                aria-expanded="false"
                aria-controls="{{ $cid }}"
              >
                {{ $itinerary->name }}
                <i class="fas fa-chevron-down ml-2 itin-chevron" aria-hidden="true"></i>
              </button>

              <div id="{{ $cid }}" class="mt-2 simple-collapse">
                @php $items = $itinerary->items ?? collect(); @endphp
                @if ($items->isEmpty())
                  <p class="text-muted text-center mb-0">{{ __('adminlte::adminlte.empty.itinerary_items') }}</p>
                @else
                  <ul class="list-group">
                    @foreach ($items->sortBy('order') as $item)
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
            <p class="text-muted text-center">{{ __('adminlte::adminlte.empty.itineraries') }}</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- Próximas reservas (solo mañana) -->
  <div class="col-md-12 mb-3">
    <div class="card shadow">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          {{ __('adminlte::adminlte.sections.upcoming_bookings') }}
          @isset($tomorrowC)
            <small class="d-block fw-normal">
              ({{ __('adminlte::adminlte.labels.date') }}: {{ $tomorrowC->format('d/m/Y') }})
            </small>
          @endisset
        </h5>
      </div>
      <div class="card-body">
        @forelse (($upcomingBookings ?? collect()) as $booking)
          <div class="mb-2">
            <strong>{{ $booking->user->full_name ?? '—' }}</strong>
            – {{ optional(optional($booking->detail)->tour)->name ?? '—' }}<br>
            <small class="text-muted">
              {{ __('adminlte::adminlte.labels.reference') }}: {{ $booking->booking_reference ?? '—' }}
            </small><br>
            <span class="text-muted">
              {{ __('adminlte::adminlte.labels.date') }}:
              {{ optional(optional($booking->detail)->tour_date)->format('d/m/Y') ?? '—' }}
            </span>
          </div>
          <hr>
        @empty
          <p class="text-muted">{{ __('adminlte::adminlte.empty.upcoming_bookings') }}</p>
        @endforelse
      </div>
    </div>
  </div>

@stop

@push('css')
<style>
  /* "Collapse" simple sin Bootstrap JS */
  .simple-collapse { display: none; }
  .simple-collapse.show { display: block; }

  /* Chevron rotación suave */
  .itin-chevron { transition: transform .2s ease-in-out; }
  .js-simple-toggle[aria-expanded="true"] .itin-chevron { transform: rotate(180deg); }
</style>
@endpush

@push('js')
<script>
(function () {
  // Toggle propio: sin depender de Bootstrap (evita cualquier conflicto)
  document.querySelectorAll('.js-simple-toggle').forEach(function(btn){
    var sel = btn.getAttribute('data-target');
    if (!sel) return;
    var panel = document.querySelector(sel);
    if (!panel) return;

    btn.addEventListener('click', function(){
      var open = panel.classList.toggle('show');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });
})();
</script>
@endpush
