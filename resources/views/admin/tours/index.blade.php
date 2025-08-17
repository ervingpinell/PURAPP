@extends('adminlte::page')
<style>
  #modalRegistrar .card { border: 1px solid rgba(255,255,255,.08); }
  #modalRegistrar .schedule-row:last-child { border-bottom: 0 !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
  /* ayuda a que el color picker no se vea estrecho en algunos temas */
  .form-control-color { height: 38px; }
</style>

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('css/gv.css') }}">
@stop

@section('title', 'Gestión de Tours')

@section('content_header')
    <h1>Gestión de Tours</h1>
@stop

@section('content')
    <div class="p-3 table-responsive">

        {{-- Botón para registrar un nuevo tour --}}
        <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
            <i class="fas fa-plus"></i> Añadir Tour
        </a>

        {{-- Botón para ver el carrito --}}
        <a href="{{ route('admin.cart.index') }}" class="btn btn-primary mb-3">
            <i class="fas fa-shopping-cart"></i> Ver carrito
        </a>

        {{-- Tabla de tours --}}
        <div class="table-responsive">
            @include('admin.tours.tourlist')
        </div>
    </div>

    {{-- Template de ítems de itinerario (para clonar filas cuando haga falta en otros modales) --}}
    @include('admin.tours.itinerary.template')

    {{-- Modal de creación (versión nueva: asignar itinerario + horarios existentes/nuevos) --}}
    @include('admin.tours.create')

    {{-- Modales de edición (versión nueva: asignar itinerario + horarios existentes/nuevos) --}}
    @include('admin.tours.edit')
@stop

@php
    // JSON para previsualizar itinerarios (descr + ítems) en crear/editar
    $itineraryJson = $itineraries->keyBy('itinerary_id')->map(function ($it) {
        return [
            'description' => $it->description,
            'items' => $it->items->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => $item->description,
                ];
            })->toArray()
        ];
    });
@endphp

@section('js')
    {{-- Tus scripts generales (si los tienes separados) --}}
    @include('admin.tours.scripts')
@stop
