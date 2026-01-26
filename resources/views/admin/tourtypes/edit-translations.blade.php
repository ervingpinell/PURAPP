@extends('adminlte::page')

@section('title', 'Traducciones - ' . $tourType->name)

@section('content_header')
<h1>
    <i class="fas fa-language"></i> Traducciones: {{ $tourType->name }}
</h1>
@stop

@section('content')
<div class="container-fluid">
    {{-- Mensajes de éxito/error --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('error') }}
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Gestionar Traducciones</h3>
            <div class="card-tools">
                <a href="{{ route('admin.product-types.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Pestañas de idiomas --}}
            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                @foreach($supportedLocales as $locale => $languageName)
                @php
                $hasTranslation = $translationsByLocale->has($locale);
                $isActive = session('active_locale') === $locale || (!session('active_locale') && $locale === 'es');
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isActive ? 'active' : '' }}"
                        id="tab-{{ $locale }}"
                        data-toggle="tab"
                        href="#content-{{ $locale }}"
                        role="tab">
                        {{ $languageName }}
                        @if($hasTranslation)
                        <span class="badge badge-success ml-1">
                            <i class="fas fa-check"></i>
                        </span>
                        @else
                        <span class="badge badge-secondary ml-1">
                            <i class="fas fa-times"></i>
                        </span>
                        @endif
                    </a>
                </li>
                @endforeach
            </ul>

            {{-- Contenido de las pestañas --}}
            <div class="tab-content mt-3" id="languageTabsContent">
                @foreach($supportedLocales as $locale => $languageName)
                @php
                $translation = $translationsByLocale->get($locale);
                $isActive = session('active_locale') === $locale || (!session('active_locale') && $locale === 'es');
                @endphp
                <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                    id="content-{{ $locale }}"
                    role="tabpanel">

                    <form action="{{ route('admin.product-types.translations.update', [$tourType, $locale]) }}"
                        method="POST"
                        autocomplete="off">
                        @csrf
                        @method('PUT')

                        @if(!$translation)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Esta traducción aún no existe. Complete los campos para crearla.
                        </div>
                        @endif

                        {{-- Campo: Nombre --}}
                        <div class="form-group">
                            <label for="name-{{ $locale }}">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name-{{ $locale }}"
                                name="name"
                                value="{{ old('name', $translation->name ?? '') }}"
                                required
                                placeholder="Ej: City Tour, Guided Tour, etc.">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo: Descripción --}}
                        <div class="form-group">
                            <label for="description-{{ $locale }}">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description-{{ $locale }}"
                                name="description"
                                rows="4"
                                placeholder="Descripción del tipo de tour (opcional)">{{ old('description', $translation->description ?? '') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo: Duración --}}
                        <div class="form-group">
                            <label for="duration-{{ $locale }}">Duración</label>
                            <input type="text"
                                class="form-control @error('duration') is-invalid @enderror"
                                id="duration-{{ $locale }}"
                                name="duration"
                                list="durationOptions-{{ $locale }}"
                                value="{{ old('duration', $translation->duration ?? '') }}"
                                placeholder="Ej: 4 horas, 6 horas, etc.">
                            <datalist id="durationOptions-{{ $locale }}">
                                <option value="4 horas"></option>
                                <option value="6 horas"></option>
                                <option value="8 horas"></option>
                                <option value="10 horas"></option>
                            </datalist>
                            <small class="form-text text-muted">
                                Duración sugerida del tour (opcional)
                            </small>
                            @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                {{ $translation ? 'Actualizar' : 'Crear' }} Traducción
                            </button>
                            <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .nav-tabs .nav-link {
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        font-weight: bold;
    }

    .badge {
        font-size: 0.7rem;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@stop
