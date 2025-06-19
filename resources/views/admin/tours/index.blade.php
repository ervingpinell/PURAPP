@extends('adminlte::page')

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('css/gv.css') }}">
@stop

@section('title', 'Gestión de Tours')

@section('content_header')
    <h1>Gestión de Tours</h1>
@stop

@section('content')
    {{-- Botón para abrir modal --}}
    <div class="p-3">
        <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
            <i class="fas fa-plus"></i> Añadir Tour
        </a>

        {{-- Tabla de tours (ahora sí envuelta correctamente para ser responsive) --}}
        <div class="table-responsive">
            @include('admin.tours.tourlist')
        </div>
    </div>

    {{-- Template para ítems de itinerario --}}
    @include('admin.tours.itinerary.template')

    {{-- Modal crear --}}
    @include('admin.tours.create')

    {{-- Modales de edición --}}
    @include('admin.tours.edit')
@endsection

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
        // Reposicionar scroll cuando se abre un modal
        document.addEventListener('shown.bs.modal', function (event) {
            const modal = event.target;
            const rect = modal.getBoundingClientRect();
            window.scrollTo({
                top: rect.top + window.scrollY - 50,
                behavior: 'smooth'
            });
        }, true);
    </script>
@endsection
