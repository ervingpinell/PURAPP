{{-- resources/views/admin/tours/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_tours.tour.ui.page_title'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
  <h1 class="m-0">{{ __('m_tours.tour.ui.page_heading') }}</h1>
  {{-- ANTES: route('admin.products.create') --}}
  @can('create-tours')
  <a href="{{ route('admin.products.wizard.create') }}" class="btn btn-success">
    <i class="fas fa-plus"></i> {{ __('m_tours.tour.ui.add_tour') }}
  </a>
  @endcan
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

{{-- Tabs: Activos / Papelera --}}
<ul class="nav nav-tabs mb-3" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link {{ !request()->routeIs('admin.tours.trash') ? 'active' : '' }}"
      href="{{ route('admin.products.index') }}"
      role="tab">
      Activos
    </a>
  </li>
  @can('restore-tours')
  <li class="nav-item" role="presentation">
    <a class="nav-link {{ request()->routeIs('admin.tours.trash') ? 'active' : '' }}"
      href="{{ route('admin.products.trash') }}"
      role="tab">
      Papelera
      @if(isset($trashedCount) && $trashedCount > 0)
      <span class="badge bg-danger ms-1">{{ $trashedCount }}</span>
      @endif
    </a>
  </li>
  @endcan
</ul>

{{-- Barra superior: filtros + acceso al carrito (solo en vista activos) --}}


{{-- Tabla de tours --}}
<div class="card">
  <div class="card-body p-0">
    @include('admin.products.tourlist')
  </div>
</div>
@stop

@push('js')
{{-- Centralizamos librerías base; el resto se inyecta desde parciales via @push('js') --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    if (window.Swal) {
      Swal.fire({
        icon: 'success',
        title: @json(session('success')),
        timer: 2000,
        showConfirmButton: false
      });
    }
    @endif
    @if(session('error'))
    if (window.Swal) {
      Swal.fire({
        icon: 'error',
        title: @json(session('error')),
        timer: 2500,
        showConfirmButton: false
      });
    }
    @endif
  });
</script>
@endpush