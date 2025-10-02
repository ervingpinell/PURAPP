@extends('adminlte::page')

@section('title', $title)

@section('content_header')
  <h1 class="mb-0">
    <i class="fas fa-language mr-2"></i>{{ $title }}
  </h1>
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

  <div class="card">
    <div class="card-header">
      <strong>{{ $labelPlural }}</strong>
    </div>
    <div class="card-body">
      @if (!count($items))
        <p class="text-muted mb-0">{{ __('m_config.translations.no_items', ['entity' => Str::lower($labelPlural)]) }}</p>
      @else
        <ul class="list-group">
          @foreach ($items as $item)
            @php
              $itemId = $item->getKey();
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
              <div>
                <div class="fw-bold">{{ $displayText }}</div>
                <small class="text-muted">{{ $labelSingular }} · ID: {{ $itemId }}</small>
              </div>
              <div>
                <a class="btn btn-sm btn-outline-primary"
                   href="{{ route('admin.translations.select-locale', [$type, $itemId]) }}">
                  <i class="fas fa-language mr-1"></i> {{ __('m_config.translations.translate') }}
                </a>
              </div>
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
    const ok = @json(__('m_config.translations.ok'));
    const s  = @json(session('success'));
    const e  = @json(session('error'));
    const ve = @json($errors->any() ? $errors->all() : []);
    if (s) Swal.fire({icon:'success',title:s,confirmButtonText:ok});
    if (e) Swal.fire({icon:'error',title:e,confirmButtonText:ok});
    if (ve && ve.length) {
      const html = '<ul class="text-start mb-0">' + ve.map(x=>`<li>${x}</li>`).join('') + '</ul>';
      Swal.fire({icon:'warning',title:@json(__('m_config.translations.validation_errors')),html,confirmButtonText:ok});
    }
  });
  </script>
@stop
