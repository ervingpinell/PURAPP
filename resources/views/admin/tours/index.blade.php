@extends('adminlte::page')
<style>
  #modalRegistrar .card { border: 1px solid rgba(255,255,255,.08); }
  #modalRegistrar .schedule-row:last-child { border-bottom: 0 !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
  .form-control-color { height: 38px; }
</style>

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('css/gv.css') }}">
@stop

@section('title', __('m_tours.tour.ui.page_title'))
@section('content_header')
    <h1>{{ __('m_tours.tour.ui.page_heading') }}</h1>
@stop

@section('content')
    <div class="p-3 table-responsive">

        {{-- Botón para registrar un nuevo tour --}}
        <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
            <i class="fas fa-plus"></i> {{ __('m_tours.tour.ui.add_tour') }} {{-- i18n: agregar ui.add_tour --}}
        </a>

        {{-- Botón para ver el carrito (si aplica) --}}
        <a href="{{ route('admin.cart.index') }}" class="btn btn-primary mb-3">
            <i class="fas fa-shopping-cart"></i> {{ __('m_tours.tour.ui.view_cart') }} {{-- i18n: agregar ui.view_cart --}}
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
    // JSON para previsualizar itinerarios
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
@stop
