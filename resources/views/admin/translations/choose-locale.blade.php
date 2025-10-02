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
        $locales = ['es' => 'Español', 'en' => 'English', 'fr' => 'Français', 'pt' => 'Português', 'de' => 'Deutsch'];
      @endphp

      <div class="row">
        @foreach ($locales as $code => $name)
          <div class="col-md-3 mb-3">
            {{-- OJO: usamos edit_locale (no locale) para no pisar el UI-locale --}}
            <a href="{{ route('admin.translations.edit', ['type' => $type, 'id' => $item->getKey(), 'edit_locale' => $code]) }}"
               class="btn btn-outline-primary w-100">
              <i class="fas fa-flag mr-1"></i> {{ $name }}
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@stop
