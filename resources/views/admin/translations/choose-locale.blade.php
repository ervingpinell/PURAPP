@extends('adminlte::page')

@section('title', __('m_config.translations.choose_locale_title'))

@section('content_header')
  <h1 class="mb-0"><i class="fas fa-language mr-2"></i> {{ __('m_config.translations.choose_locale_title') }}</h1>
@stop

@section('content')
  <div class="card">
    <div class="card-body">
      <p class="mb-4 text-muted">{{ __('m_config.translations.choose_locale_hint') }}</p>

      @php
        // Idiomas disponibles para editar (independiente del idioma de la UI)
        $locales = ['es' => 'Español', 'en' => 'English', 'fr' => 'Français', 'pt' => 'Português', 'de' => 'Deutsch'];
      @endphp

      <div class="row">
        @foreach ($locales as $code => $name)
          <div class="col-md-3 mb-3">
            {{-- Lleva a SELECT con edit_locale, para listar artículos en el locale de la UI pero ya fijando el idioma de edición --}}
            <a href="{{ route('admin.translations.select', ['type' => $type, 'edit_locale' => $code]) }}"
               class="btn btn-outline-primary w-100">
              <i class="fas fa-flag mr-1"></i> {{ $name }}
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@stop
