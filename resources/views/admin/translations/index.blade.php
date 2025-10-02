@extends('adminlte::page')

@section('title', __('m_config.translations.index_title'))

@section('content_header')
  <h1 class="mb-0">
    <i class="fas fa-language mr-2"></i>
    {{ __('m_config.translations.index_title') }}
  </h1>
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
      $keys = [
        'tours'           => 'm_config.translations.entities.tours',
        'itineraries'     => 'm_config.translations.entities.itineraries',
        'itinerary_items' => 'm_config.translations.entities.itinerary_items',
        'amenities'       => 'm_config.translations.entities.amenities',
        'faqs'            => 'm_config.translations.entities.faqs',
        'policies'        => 'm_config.translations.entities.policies',
        'tour_types'      => 'm_config.translations.entities.tour_types',
      ];
    @endphp

    @foreach($keys as $type => $labelKey)
      <div class="col-md-6 col-lg-4">
        <div class="card mb-3">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <h5 class="card-title mb-1">{{ __($labelKey) }}</h5>
              <small class="text-muted">{{ __('m_config.translations.click_to_continue') }}</small>
            </div>
            <a href="{{ route('admin.translations.select', $type) }}" class="btn btn-primary">
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const ok = @json(__('m_config.translations.ok'));
    const s  = @json(session('success'));
    const e  = @json(session('error'));
    if (s) Swal.fire({icon:'success',title:s,confirmButtonText:ok});
    if (e) Swal.fire({icon:'error',title:e,confirmButtonText:ok});
  });
  </script>
@stop
