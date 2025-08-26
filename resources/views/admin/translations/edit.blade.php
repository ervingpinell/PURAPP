@extends('adminlte::page')

@section('title', 'Editar Traducción')

@section('content_header')
  <h1 class="mb-2">
    <i class="fas fa-language"></i>
    {{ __('adminlte::adminlte.edit_translation') ?? 'Editar Traducción' }}
    <span class="badge bg-dark ms-2">{{ strtoupper($locale) }}</span>
  </h1>
@stop

@section('content')
@php
  $entityLabel = match ($type) {
      'tours'           => 'Tour',
      'itineraries'     => 'Itinerario',
      'itinerary_items' => 'Ítem del Itinerario',
      'amenities'       => 'Amenidad',
      'faqs'            => 'Pregunta Frecuente',
      'policies'        => 'Política',
      'tour_types'      => 'Tipo de Tour',
      default           => 'Elemento',
  };
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
      <strong>{{ __('adminlte::adminlte.validation_errors') ?? 'Please review the highlighted fields.' }}</strong>
      <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</noscript>

<form action="{{ route('admin.translations.update', [$type, $item->getKey()]) }}" method="POST">
  @csrf
  <input type="hidden" name="locale" value="{{ $locale }}">

  {{-- Bloque principal --}}
  <div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
      <strong>
        <i class="fas fa-info-circle me-2"></i>
        {{ __('adminlte::adminlte.main_information') ?? "Información de {$entityLabel}" }}
        <span class="ms-2 badge bg-dark">{{ strtoupper($locale) }}</span>
      </strong>
    </div>

    <div class="card-body">
      @foreach ($fields as $field)
        <div class="form-group mb-3">
          <label for="{{ $field }}">
            <i class="far fa-edit me-1"></i> {{ ucfirst($field) }} ({{ strtoupper($locale) }})
          </label>
          <textarea
            name="translations[{{ $field }}]"
            class="form-control"
            rows="{{ in_array($field, ['content','overview','description']) ? 6 : 3 }}"
          >{{ old("translations.$field", $translations[$field] ?? '') }}</textarea>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Partials por tipo --}}
  @includeWhen($type === 'tours', 'admin.translations.partials.edit-tour-translations', [
      'item'   => $item,
      'locale' => $locale,
  ])

  @includeWhen($type === 'policies', 'admin.translations.partials.edit-policy-translations', [
      'item'   => $item,
      'locale' => $locale,
      'type'   => $type,
  ])

  <div class="text-end mt-4">
    <button type="submit" class="btn btn-success">
      <i class="fas fa-save me-1"></i> {{ __('adminlte::adminlte.save') ?? 'Guardar' }}
    </button>
  </div>
</form>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const flashSuccess = @json(session('success'));
    const flashError   = @json(session('error'));
    const valErrors    = @json($errors->any() ? $errors->all() : []);

    if (flashSuccess) {
      Swal.fire({
        icon: 'success',
        title: flashSuccess,
        confirmButtonText: 'OK'
      });
    }
    if (flashError) {
      Swal.fire({
        icon: 'error',
        title: flashError,
        confirmButtonText: 'OK'
      });
    }
    if (valErrors && valErrors.length) {
      const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      Swal.fire({
        icon: 'warning',
        title: @json(__('adminlte::adminlte.validation_errors') ?? 'Please review the highlighted fields.'),
        html: list,
        confirmButtonText: 'OK'
      });
    }
  });
  </script>
@stop
