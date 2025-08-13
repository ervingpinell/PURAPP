@extends('adminlte::page')

@section('title', 'Editar Traducción')

@section('content_header')
    <h1><i class="fas fa-language"></i> Editar Traducción - {{ strtoupper($locale) }}</h1>
@stop

@section('content')
@php
    // Etiqueta del bloque “Información”
    $entityLabel = match ($type) {
        'tours'           => 'Tour',
        'itineraries'     => 'Itinerario',
        'itinerary_items' => 'Ítem del Itinerario',
        'amenities'       => 'Amenidad',
        'faqs'            => 'Pregunta Frecuente',
        'policies'        => 'Política',
        default           => 'Elemento',
    };
@endphp

<form action="{{ route('admin.translations.update', [$type, $item->getKey()]) }}" method="POST">
    @csrf
    <input type="hidden" name="locale" value="{{ $locale }}">

    {{-- Campos principales del objeto --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong><i class="fas fa-info-circle me-1"></i> Información de {{ $entityLabel }}</strong>
        </div>
        <div class="card-body">
            @foreach ($fields as $field)
                <div class="form-group mb-3">
                    <label for="{{ $field }}">
                        <i class="far fa-edit me-1"></i> {{ ucfirst($field) }} ({{ strtoupper($locale) }})
                    </label>
                    <textarea name="translations[{{ $field }}]" class="form-control" rows="3">{{ old("translations.$field", $translations[$field] ?? '') }}</textarea>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Partials específicos por tipo --}}
    @includeWhen($type === 'tours', 'admin.translations.partials.edit-tour-translations', [
        'item'   => $item,
        'locale' => $locale,
    ])

    @includeWhen($type === 'policies', 'admin.translations.partials.edit-policy-translations', [
        'item'   => $item,
        'locale' => $locale,
    ])

    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar Traducciones
        </button>
    </div>
</form>
@stop
