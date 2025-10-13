@extends('adminlte::page')

@section('title', $title)

@section('content_header')
  <h1><i class="fas fa-language me-2"></i>{{ $title }}</h1>
@stop

@section('content')
@php
  use Illuminate\Support\Str;

  // Asegura Collection por si llega array
  $items = collect($items ?? []);

  // Idioma de EDICIÓN elegido (se usa solo para pasar al edit y mostrar badge, NO para traducir el listado)
  $editLocale = request('edit_locale');

  $labelPlural   = __('m_config.translations.entities.' . $type);
  $labelSingular = __('m_config.translations.entities_singular.' . $type);
@endphp

<noscript>
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-warning">
      <strong>{{ __('m_config.translations.validation_errors') }}</strong>
      <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</noscript>

{{-- Aviso si falta el edit_locale (por acceso directo) --}}
@if (empty($editLocale))
  <div class="alert alert-info">
    <i class="fas fa-info-circle mr-1"></i>
    {{ __('m_config.translations.choose_locale_hint') }}
    <a href="{{ route('admin.translations.choose-locale', ['type' => $type]) }}" class="alert-link">
      {{ __('m_config.translations.choose_locale_title') }}
    </a>
  </div>
@endif

<div class="card shadow-sm">
  <div class="card-body">
    @if ($items->isEmpty())
      <p class="text-muted mb-0">
        {{ __('m_config.translations.no_items', ['entity' => Str::lower($labelPlural)]) }}
      </p>
    @else
      <ul class="list-group">
        @foreach ($items as $item)
          @php
            $itemId = $item->getKey();
            $hasId  = !empty($itemId);

            // Mostrar SIEMPRE en idioma de la UI (no aplicar edit_locale aquí)
            $displayText = match($type) {
              'tours'            => $item->name ?? '—',
              'itineraries'      => $item->name ?? '—',
              'itinerary_items'  => $item->title ?? '—',
              'amenities'        => $item->name ?? '—',
              'faqs'             => Str::limit($item->question ?? '—', 60),
              'policies'         => $item->name ?? '—',
              'tour_types'       => $item->name ?? '—',
              default            => $item->name ?? $item->title ?? ('#'.$itemId),
            };
          @endphp

          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="text-truncate">
              {{ $displayText }}
              @if($editLocale)
                <span class="badge bg-light text-dark border ml-2">{{ strtoupper($editLocale) }}</span>
              @endif
            </span>

            @if ($hasId)
              {{-- Ir a EDIT con el idioma seleccionado --}}
              <a href="{{ route('admin.translations.edit', ['type' => $type, 'id' => $itemId, 'edit_locale' => $editLocale]) }}"
                 class="btn btn-sm btn-primary">
                <i class="fas fa-chevron-right"></i> {{ __('m_config.translations.select') }}
              </a>
            @else
              <span class="badge bg-secondary">{{ __('m_config.translations.id_unavailable') }}</span>
            @endif
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</div>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const flashSuccess = @json(session('success'));
    const flashError   = @json(session('error'));
    const valErrors    = @json($errors->any() ? $errors->all() : []);

    if (flashSuccess) {
      Swal.fire({ icon: 'success', title: flashSuccess, confirmButtonText: @json(__('m_config.translations.ok')) });
    }
    if (flashError) {
      Swal.fire({ icon: 'error', title: flashError, confirmButtonText: @json(__('m_config.translations.ok')) });
    }
    if (valErrors && valErrors.length) {
      const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      Swal.fire({
        icon: 'warning',
        title: @json(__('m_config.translations.validation_errors')),
        html: list,
        confirmButtonText: @json(__('m_config.translations.ok')),
      });
    }
  });
  </script>
@stop
