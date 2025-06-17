@extends('adminlte::page')

@section('title', '| Dashboard')

@section('content_header')

    <h1>Dashboard Green Vacations</h1>
    
@stop



@section('content')

    <div class="row">
        <!-- Total Clientes -->
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="Usuarios" text="{{ $totalUsuarios }}" icon="fas fa-users" theme="info"/>
            <a href="{{ route('admin.users.index') }}" class="btn btn-info btn-block mt-2">Ver Usuarios</a>
        </div>

        

        <!-- Total Tours -->
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="Tours" text="{{ $totalTours }}" icon="fas fa-map" theme="warning"/>
            <a href="{{ route('admin.tours.index') }}" class="btn btn-warning btn-block mt-2">Ver Tours</a>
        </div>

        <!-- Total Categorías -->
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="tourtypes" text="{{ $tourTypes }}" icon="fas fa-tags" theme="success"/>
            <a href="{{ route('admin.tourtypes.index') }}" class="btn btn-success btn-block mt-2">Ver Tipos de Tours</a>
        </div>

        <!-- Total Idiomas -->
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="Languages" text="{{ $totalIdiomas }}" icon="fas fa-globe" theme="primary"/>
            <a href="{{ route('admin.languages.index') }}" class="btn btn-primary btn-block mt-2">Ver Idiomas</a>
        </div>

<!-- Total Horarios de Tours -->
<div class="col-md-4 mb-3">
    <x-adminlte-info-box title="Horarios" text="{{ $totalHorarios }}" icon="fas fa-clock" theme="dark"/>
    <a href="{{ route('admin.tours.schedule.index') }}" class="btn btn-dark btn-block mt-2">Ver Horarios</a>
</div>

<div class="col-md-4 mb-3">
    <x-adminlte-info-box title="Amenidades" text="{{ $totalAmenities }}" icon="fas fa-concierge-bell" theme="secondary"/>
    <a href="{{ route('admin.tours.amenities.index') }}" class="btn btn-secondary btn-block mt-2">Ver Amenidades</a>
</div>

        <!-- Total Itinerarios de Tours -->
        <!-- Itinerarios con detalles -->
<div class="col-md-12 mb-3">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Itinerarios disponibles</h4>
        </div>
        <div class="card-body">
            @forelse ($itineraries as $itinerary)
                <div class="mb-2">
                    <button class="btn btn-outline-danger w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapse{{ $itinerary->itinerary_id }}">
                        {{ $itinerary->name }} <i class="fas fa-chevron-down float-end"></i>
                    </button>
                    <div id="collapse{{ $itinerary->itinerary_id }}" class="collapse mt-2">
                        @if ($itinerary->items->isEmpty())
                            <p class="text-muted">Este itinerario no tiene ítems asignados.</p>
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
                <p class="text-muted">No hay itinerarios registrados.</p>
            @endforelse
        </div>
    </div>
</div>

@stop
