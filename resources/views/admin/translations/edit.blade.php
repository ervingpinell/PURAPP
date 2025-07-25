@extends('adminlte::page')

@section('title', 'Editar Traducción')

@section('content_header')
    <h1><i class="fas fa-language"></i> Editar Traducción - {{ strtoupper($locale) }}</h1>
@stop

@section('content')
<form action="{{ route('admin.translations.update', [$type, $item->getKey()]) }}" method="POST">
    @csrf
    <input type="hidden" name="locale" value="{{ $locale }}">

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong><i class="fas fa-info-circle me-1"></i> Información del Tour</strong>
        </div>
        <div class="card-body">
            @foreach ($fields as $field)
                <div class="form-group mb-3">
                    <label for="{{ $field }}"><i class="far fa-edit me-1"></i> {{ ucfirst($field) }} ({{ strtoupper($locale) }})</label>
                    <textarea name="translations[{{ $field }}]" class="form-control" rows="3">{{ old("translations.$field", $translations[$field] ?? '') }}</textarea>
                </div>
            @endforeach
        </div>
    </div>

    @if($type === 'tours' && $item->itinerary)
        <!-- Itinerario -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white" data-toggle="collapse" data-target="#collapseItinerary" style="cursor: pointer;">
                <h5 class="mb-0">
                    <i class="fas fa-route me-2"></i> Itinerario
                </h5>
            </div>
            <div id="collapseItinerary" class="collapse show">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="itinerary_name"><i class="far fa-file-alt me-1"></i> Nombre del Itinerario ({{ strtoupper($locale) }})</label>
                        <textarea name="itinerary_translations[name]" class="form-control" rows="2">{{ old('itinerary_translations.name', $item->itinerary->translate($locale)?->name ?? '') }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="itinerary_description"><i class="far fa-file-alt me-1"></i> Descripción ({{ strtoupper($locale) }})</label>
                        <textarea name="itinerary_translations[description]" class="form-control" rows="4">{{ old('itinerary_translations.description', $item->itinerary->translate($locale)?->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ítems del Itinerario -->
        @foreach ($item->itinerary->items as $index => $it)
            <div class="card mb-2">
                <div class="card-header bg-secondary text-white" data-toggle="collapse" data-target="#collapseItem{{ $it->id }}" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i> Ítem {{ $index + 1 }}: {{ $it->title }}
                    </h6>
                </div>
                <div id="collapseItem{{ $it->id }}" class="collapse">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="item_title_{{ $it->id }}"><i class="far fa-edit me-1"></i> Título ({{ strtoupper($locale) }})</label>
                            <textarea name="item_translations[{{ $it->id }}][title]" class="form-control" rows="2">{{ old("item_translations.$it->id.title", $it->translate($locale)?->title ?? '') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="item_description_{{ $it->id }}"><i class="far fa-edit me-1"></i> Descripción ({{ strtoupper($locale) }})</label>
                            <textarea name="item_translations[{{ $it->id }}][description]" class="form-control" rows="3">{{ old("item_translations.$it->id.description", $it->translate($locale)?->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar Traducciones
        </button>
    </div>
</form>
@stop
