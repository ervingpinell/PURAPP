@extends('adminlte::page')

@section('title', $title)

@section('content_header')
  <h1><i class="fas fa-language me-2"></i>{{ $title }}</h1>
@stop

@section('content')
@php
  use Illuminate\Support\Str;

  // Caer en un nombre genérico si no viene desde el controlador
  $label = ($entityLabel ?? ($labelSingular ?? 'elemento'));
@endphp

{{-- Fallback sin JS --}}
<noscript>
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-warning">
      <strong>{{ __('adminlte::adminlte.validation_errors') ?? 'Revisa los campos.' }}</strong>
      <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</noscript>

<div class="card shadow-sm">
  <div class="card-body">
    @if ($items->isEmpty())
      <p class="text-muted mb-0">No hay {{ Str::plural($label) }} disponibles para traducir.</p>
    @else
      <ul class="list-group">
        @foreach ($items as $item)
          @php
            $itemId = $item->getKey();
            $hasId  = !empty($itemId);

            $displayText = match($type) {
              'tours'            => $item->name ?? 'Sin nombre',
              'itineraries'      => $item->name ?? 'Sin nombre',
              'itinerary_items'  => $item->title ?? 'Sin título',
              'amenities'        => $item->name ?? 'Sin nombre',
              'faqs'             => Str::limit($item->question ?? 'Sin pregunta', 60),
              // Para políticas mostramos el nombre traducido si existe; si no, el base
              'policies'         => optional($item->translation())->name ?? ($item->name ?? 'Sin título'),
              'tour_types'       => $item->name ?? 'Sin nombre',
              default            => 'Elemento'
            };
          @endphp

          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="text-truncate">{{ $displayText }}</span>

            @if ($hasId)
              <a href="{{ route('admin.translations.locale', ['type' => $type, 'id' => $itemId]) }}"
                 class="btn btn-sm btn-primary">
                <i class="fas fa-chevron-right"></i> Seleccionar
              </a>
            @else
              <span class="badge bg-secondary">ID no disponible</span>
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
      Swal.fire({ icon: 'success', title: flashSuccess, confirmButtonText: 'OK' });
    }
    if (flashError) {
      Swal.fire({ icon: 'error', title: flashError, confirmButtonText: 'OK' });
    }
    if (valErrors && valErrors.length) {
      const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      Swal.fire({
        icon: 'warning',
        title: @json(__('adminlte::adminlte.validation_errors') ?? 'Revisa los campos.'),
        html: list,
        confirmButtonText: 'OK'
      });
    }
  });
  </script>
@stop
