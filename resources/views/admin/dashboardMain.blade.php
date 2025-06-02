@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Green Vacation</h1>
@stop

@section('content')
    <div class="row">
        <!-- Total Clientes -->
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="Usuarios" text="{{ $totalUsuarios }}" icon="fas fa-users" theme="info"/>
            <a href="{{ route('admin.users.index') }}" class="btn btn-info btn-block mt-2">Ver Usuarios</a>
        </div>

        <!-- Total Reservas -->
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="Reservas" text="{{ $totalReservas }}" icon="fas fa-calendar-check" theme="success"/>
            <a href="{{ route('admin.reservas.index') }}" class="btn btn-success btn-block mt-2">Ver Reservas</a>
        </div>

        <!-- Total Tours -->
        <div class="col-md-4 mb-3">
            <x-adminlte-info-box title="Tours" text="{{ $totalTours }}" icon="fas fa-map" theme="warning"/>
            <a href="{{ route('admin.tours.index') }}" class="btn btn-warning btn-block mt-2">Ver Tours</a>
        </div>
    </div>
@stop
