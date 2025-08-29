@extends('adminlte::page')

@section('title', $title)

@section('content_header')
  <h1><i class="fas fa-language me-2"></i>{{ $title }}</h1>
@stop

@section('content')
@php
  use Illuminate\Support\Str;

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

<div class="card shadow-sm">
  <div class="card-body">
    @if ($items->isEmpty())
      <p class="text-muted mb-0">{{ __('m_config.translations.no_items', ['entity' => Str::lower($labelPlural)]) }}</p>
    @else
      <ul class="list-group">
        @foreach ($items as $item)
          @php
            $itemId = $item->getKey();
            $hasId  = !empty($itemId);

            $displayText = match($type) {
              'tours'            => $item->name ?? '—',
              'itineraries'      => $item->name ?? '—',
              'itinerary_items'  => $item->title ?? '—',
              'amenities'        => $item->name ?? '—',
              'faqs'             => Str::limit($item->question ?? '—', 60),
              // Para políticas, usar SIEMPRE el nombre base
              'policies'         => $item->name ?? '—',
              'tour_types'       => $item->name ?? '—',
              default            => '—'
            };
          @endphp

          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="text-truncate">{{ $displayText }}</span>

            @if ($hasId)
              <a href="{{ route('admin.translations.locale', ['type' => $type, 'id' => $itemId]) }}"
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
