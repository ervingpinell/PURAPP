@extends('adminlte::page')

@section('title', 'Gestión de Traducciones')

@section('content_header')
  <h1><i class="fas fa-language"></i> Gestión de Traducciones</h1>
@stop

@section('content')
  {{-- Fallback sin JS --}}
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
      $options = [
        'tours'            => 'Tours',
        'itineraries'      => 'Itinerarios',
        'itinerary_items'  => 'Ítems del Itinerario',
        'amenities'        => 'Amenidades',
        'faqs'             => 'Preguntas Frecuentes',
        'policies'         => 'Políticas',
        'tour_types'       => 'Tipos de Tour',
      ];
    @endphp

    @foreach ($options as $key => $label)
      <div class="col-md-4 mb-3">
        <a href="{{ route('admin.translations.select', ['type' => $key]) }}" class="btn btn-primary w-100 py-3">
          <i class="fas fa-globe me-2"></i> {{ $label }}
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
  });
  </script>
@stop
