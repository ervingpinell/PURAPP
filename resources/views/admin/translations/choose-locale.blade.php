@extends('adminlte::page')

@section('title', 'Seleccionar idioma')

@section('content_header')
    <h1><i class="fas fa-language"></i> Seleccionar idioma para traducir</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <p class="mb-4">
            Selecciona el idioma al que deseas traducir este elemento.
        </p>

        <div class="row">
            @foreach (['en' => 'Inglés', 'pt' => 'Portugués', 'fr' => 'Francés', 'de' => 'Alemán'] as $code => $lang)
                <div class="col-md-3 mb-3">
                    <a href="{{ route('admin.translations.edit', ['type' => $type, 'id' => $item->getKey(), 'locale' => $code]) }}"
                       class="btn btn-outline-success w-100">
                        <i class="fas fa-flag me-1"></i> {{ $lang }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@stop
