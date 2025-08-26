@extends('adminlte::page')

@section('title', '| Dashboard')

@section('content_header')
  <div class="mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-body text-center">
        <h3 class="mb-2">
          ¡Hola {{ Auth::user()->full_name }}! 👋
        </h3>
        <p class="mb-0">
          Bienvenido al sistema de administración de <strong>Green Vacations</strong>.
          Usa el menú lateral para comenzar a gestionar.
        </p>
      </div>
    </div>
  </div>
@stop

@section('content')

  <div class="row">
    <!-- Usuarios -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Usuarios" text="{{ $totalUsers ?? 0 }}" icon="fas fa-users" theme="info"/>
      <a href="{{ route('admin.users.index') }}" class="btn btn-info btn-block mt-2">Ver Usuarios</a>
    </div>

    <!-- Tours -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Tours" text="{{ $totalTours ?? 0 }}" icon="fas fa-map" theme="warning"/>
      <a href="{{ route('admin.tours.index') }}" class="btn btn-warning btn-block mt-2">Ver Tours</a>
    </div>

    <!-- Tipos de Tours -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Tipos de Tours" text="{{ $tourTypes ?? 0 }}" icon="fas fa-tags" theme="success"/>
      <a href="{{ route('admin.tourtypes.index') }}" class="btn btn-success btn-block mt-2">Ver Tipos</a>
    </div>

    <!-- Idiomas -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Idiomas" text="{{ $totalLanguages ?? 0 }}" icon="fas fa-globe" theme="primary"/>
      <a href="{{ route('admin.languages.index') }}" class="btn btn-primary btn-block mt-2">Ver Idiomas</a>
    </div>

    <!-- Horarios -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Horarios" text="{{ $totalSchedules ?? 0 }}" icon="fas fa-clock" theme="dark"/>
      <a href="{{ route('admin.tours.schedule.index') }}" class="btn btn-dark btn-block mt-2">Ver Horarios</a>
    </div>

    <!-- Amenidades -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Amenidades" text="{{ $totalAmenities ?? 0 }}" icon="fas fa-concierge-bell" theme="secondary"/>
      <a href="{{ route('admin.tours.amenities.index') }}" class="btn btn-secondary btn-block mt-2">Ver Amenidades</a>
    </div>

    <!-- Reservas Totales -->
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Reservas Totales" text="{{ $totalBookings ?? 0 }}" icon="fas fa-calendar-check" theme="success"/>
      <a href="{{ route('admin.reservas.index') }}" class="btn btn-success btn-block mt-2">Ver Reservas</a>
    </div>
  </div>

  <!-- Itinerarios -->
  <div class="col-md-12 mb-3">
    <div class="card">
      <div class="card-header bg-danger text-white">
        <h4 class="mb-0">Itinerarios disponibles</h4>
      </div>
      <div class="card-body">
        @forelse ($itineraries ?? [] as $itinerary)
          <div class="mb-2">
            <button class="btn btn-outline-danger w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapse{{ $itinerary->itinerary_id }}">
              {{ $itinerary->name }} <i class="fas fa-chevron-down float-end"></i>
            </button>
            <div id="collapse{{ $itinerary->itinerary_id }}" class="collapse mt-2">
              @if (($itinerary->items ?? collect())->isEmpty())
                <p class="text-muted">Este itinerario no tiene ítems asignados.</p>
              @else
                <ul class="list-group">
                  {{-- Si tus items tienen el campo "order", mantenlo; si usan "pivot->item_order", cambia a sortBy('pivot.item_order') --}}
                  @foreach (($itinerary->items->sortBy('order')) as $item)
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
          <p class="text-muted">No hay itinerarios registrados.</p>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Próximas Reservas -->
  <div class="col-md-12 mb-3">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Próximas Reservas</h5>
      </div>
      <div class="card-body">
        @forelse ($upcomingBookings ?? [] as $booking)
          <div class="mb-2">
            <strong>{{ $booking->user->full_name ?? '—' }}</strong> – {{ $booking->tour->name ?? '—' }}<br>
            <small class="text-muted">Referencia: {{ $booking->booking_reference ?? '—' }}</small><br>
            <span class="text-muted">
              Fecha:
              {{ optional(optional($booking->detail)->tour_date)->format('d/m/Y') ?? '—' }}
            </span>
          </div>
          <hr>
        @empty
          <p class="text-muted">No hay reservas próximas.</p>
        @endforelse
      </div>
    </div>
  </div>

@stop
