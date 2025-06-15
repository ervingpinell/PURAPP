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
            <x-adminlte-info-box title="Categories" text="{{ $totalCategorias }}" icon="fas fa-tags" theme="success"/>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-success btn-block mt-2">Ver Categorías</a>
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
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="Itinerarios" text="{{ $totalItinerarios }}" icon="fas fa-list" theme="danger"/>
            <a href="{{ route('admin.tours.itinerary.index') }}" class="btn btn-danger btn-block mt-2">Ver Itinerarios</a>
        </div>
    </div>

@stop
