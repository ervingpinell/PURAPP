@extends('adminlte::page')

@section('title', __('m_config.translations.title'))

@section('content_header')
  <h1><i class="fas fa-language"></i> {{ __('m_config.translations.title') }}</h1>
@stop

@section('content')
  <noscript>
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
  </noscript>

  <div class="row">
    @php
      $keys = ['tours','itineraries','itinerary_items','amenities','faqs','policies','tour_types'];
    @endphp

    @foreach ($keys as $key)
      <div class="col-md-4 mb-3">
        {{-- Ahora dirigimos al paso de elegir idioma de edición (choose-locale) --}}
        <a href="{{ route('admin.translations.choose-locale', ['type' => $key]) }}" class="btn btn-primary w-100 py-3">
          <i class="fas fa-globe me-2"></i> {{ __('m_config.translations.entities.' . $key) }}
        </a>
      </div>
    @endforeach
  </div>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const flashSuccess = @json(session('success'));
    const flashError   = @json(session('error'));

    if (flashSuccess) {
      Swal.fire({ icon: 'success', title: flashSuccess, confirmButtonText: @json(__('m_config.translations.ok')) });
    }
    if (flashError) {
      Swal.fire({ icon: 'error', title: flashError, confirmButtonText: @json(__('m_config.translations.ok')) });
    }
  });
  </script>
@stop
