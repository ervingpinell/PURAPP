@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Green Vacation</h1>
@stop

@section('content')
    <div class="row">
        <!-- Total Clientes -->
        <div class="col-md-4">
            <x-adminlte-info-box title="Clientes" text="{{ $totalClientes }}" icon="fas fa-users" theme="info"/>
        </div>

        <!-- Total Reservas -->
        <div class="col-md-4">
            <x-adminlte-info-box title="Reservas" text="{{ $totalReservas }}" icon="fas fa-calendar-check" theme="success"/>
        </div>

        <!-- Total Tours -->
        <div class="col-md-4">
            <x-adminlte-info-box title="Tours" text="{{ $totalTours }}" icon="fas fa-map" theme="warning"/>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.reservas.index') }}" class="btn btn-primary">Ver Reservas</a>
        <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">Ver Clientes</a>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-success">Ver Tours</a>
        
    </div>
@stop
