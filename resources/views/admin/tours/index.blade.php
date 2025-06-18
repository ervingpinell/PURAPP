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
  <div class="p-3 table-responsive">
      <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
          <i class="fas fa-plus"></i> Añadir Tour
      </a>
  </div>

  {{-- Template para ítems de itinerario (antes del JS) --}}
  @include('admin.tours.itinerary.template')

  {{-- Tabla de tours --}}
  @include('admin.tours.tourlist')

  {{-- Modal crear --}}
  @include('admin.tours.create')

  {{-- Modales de edición --}}
  @include('admin.tours.edit')

@endsection

@section('js')
  @include('admin.tours.scripts')
@endsection
