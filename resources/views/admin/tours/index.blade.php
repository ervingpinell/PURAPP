@extends('adminlte::page')

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('css/gv.css') }}">
@stop

@section('title', 'Gestión de Tours')

@section('content_header')
    <h1>Gestión de Tours</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
        <i class="fas fa-plus"></i> Añadir Tour
    </a>

    
</div>

{{-- Template para ítems de itinerario dinámico --}}
    @include('admin.tours.itinerary.template')

    {{-- Tabla principal con todos los tours --}}
    @include('admin.tours.tourlist')

    {{-- Modales de edición para cada tour --}}
    @include('admin.tours.edit')

    {{-- Modal para crear nuevo tour --}}
    @include('admin.tours.create')

@endsection

@section('plugins.Sweetalert2', true)

@section('js')
    @include('admin.tours.scripts')
@endsection