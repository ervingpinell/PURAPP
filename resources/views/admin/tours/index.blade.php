{{-- resources/views/admin/tours/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_tours.tour.ui.page_title'))

@section('content_header')
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1 class="m-0">{{ __('m_tours.tour.ui.page_heading') }}</h1>
    {{-- ANTES: route('admin.tours.create') --}}
    <a href="{{ route('admin.tours.wizard.create') }}" class="btn btn-success">
      <i class="fas fa-plus"></i> {{ __('m_tours.tour.ui.add_tour') }}
    </a>
  </div>
@stop

@section('content')
  {{-- Flash alerts (usamos close accesible) --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <span class="fw-semibold">{{ __('m_tours.common.success') }}: </span>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <span class="fw-semibold">{{ __('m_tours.common.error') }}: </span>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  {{-- Barra superior: filtros + acceso al carrito --}}
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="btn-group" role="group" aria-label="{{ __('m_tours.tour.ui.filter_status') }}">
      <a href="{{ route('admin.tours.index', ['status' => 'active']) }}"
         class="btn btn-outline-primary {{ ($status ?? '') === 'active' ? 'active' : '' }}">
        {{ __('m_tours.tour.ui.actives') }}
      </a>
      <a href="{{ route('admin.tours.index', ['status' => 'inactive']) }}"
         class="btn btn-outline-primary {{ ($status ?? '') === 'inactive' ? 'active' : '' }}">
        {{ __('m_tours.tour.ui.inactives') }}
      </a>
      <a href="{{ route('admin.tours.index', ['status' => 'archived']) }}"
         class="btn btn-outline-primary {{ ($status ?? '') === 'archived' ? 'active' : '' }}">
        {{ __('m_tours.tour.ui.archived') }}
      </a>
      <a href="{{ route('admin.tours.index', ['status' => 'all']) }}"
         class="btn btn-outline-secondary {{ ($status ?? '') === 'all' ? 'active' : '' }}">
        {{ __('m_tours.tour.ui.all') }}
      </a>
    </div>

    <a href="{{ route('admin.carts.index') }}" class="btn btn-primary">
      <i class="fas fa-shopping-cart"></i> {{ __('m_tours.tour.ui.view_cart') }}
    </a>
  </div>

  {{-- Tabla de tours --}}
  <div class="card">
    <div class="card-body p-0">
      @include('admin.tours.tourlist')
    </div>
  </div>
@stop

@push('js')
  {{-- Centralizamos librerías base; el resto se inyecta desde parciales via @push('js') --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      @if(session('success'))
        if (window.Swal) {
          Swal.fire({ icon: 'success', title: @json(session('success')), timer: 2000, showConfirmButton: false });
        }
      @endif
      @if(session('error'))
        if (window.Swal) {
          Swal.fire({ icon: 'error', title: @json(session('error')), timer: 2500, showConfirmButton: false });
        }
      @endif
    });
  </script>
@endpush
