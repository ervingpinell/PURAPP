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

    {{-- Template de ítems de itinerario --}}
    @include('admin.tours.itinerary.template')

    {{-- Modal de creación --}}
    @include('admin.tours.create')

    {{-- Modales de edición --}}
    @include('admin.tours.edit')
@stop

@php
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
    @include('admin.tours.scripts')

    <script>
        // Reposiciona scroll al abrir modal
        document.addEventListener('shown.bs.modal', function (event) {
            const modal = event.target;
            const rect = modal.getBoundingClientRect();
            window.scrollTo({
                top: rect.top + window.scrollY - 50,
                behavior: 'smooth'
            });
        }, true);
    </script>
@stop
