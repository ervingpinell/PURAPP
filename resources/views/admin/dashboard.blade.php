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

    </div>
@stop
