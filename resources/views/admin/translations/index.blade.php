@extends('adminlte::page')

@section('title', 'Gestión de Traducciones')

@section('content_header')
    <h1><i class="fas fa-language"></i> Gestión de Traducciones</h1>
@stop

@section('content')
<div class="row">
    @php
        $options = [
            'tours' => 'Tours',
            'itineraries' => 'Itinerarios',
            'itinerary_items' => 'Ítems del Itinerario',
            'amenities' => 'Amenidades',
            'faqs' => 'Preguntas Frecuentes',
        ];
    @endphp

    @foreach ($options as $key => $label)
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.translations.select', ['type' => $key]) }}" class="btn btn-primary w-100 py-3">
                <i class="fas fa-globe me-2"></i> {{ $label }}
            </a>
        </div>
    @endforeach
</div>
@stop
