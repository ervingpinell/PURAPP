@extends('adminlte::page')

@section('title', __('m_tours.tour.ui.page_title'))

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1>{{ __('m_tours.tour.ui.page_heading') }}</h1>
    <a href="{{ route('admin.tours.create') }}" class="btn btn-success">
      <i class="fas fa-plus"></i> {{ __('m_tours.tour.ui.add_tour') }}
    </a>
  </div>
@stop

@section('content')
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  {{-- Filtros de estado --}}
  <div class="btn-group mb-3" role="group" aria-label="{{ __('m_tours.tour.ui.filter_status') }}">
    <a href="{{ route('admin.tours.index', ['status' => 'active']) }}"
       class="btn btn-outline-primary {{ ($status ?? '')==='active' ? 'active' : '' }}">
      {{ __('m_tours.tour.ui.actives') }}
    </a>
    <a href="{{ route('admin.tours.index', ['status' => 'inactive']) }}"
       class="btn btn-outline-primary {{ ($status ?? '')==='inactive' ? 'active' : '' }}">
      {{ __('m_tours.tour.ui.inactives') }}
    </a>
    <a href="{{ route('admin.tours.index', ['status' => 'archived']) }}"
       class="btn btn-outline-primary {{ ($status ?? '')==='archived' ? 'active' : '' }}">
      {{ __('m_tours.tour.ui.archived') }}
    </a>
    <a href="{{ route('admin.tours.index', ['status' => 'all']) }}"
       class="btn btn-outline-secondary {{ ($status ?? '')==='all' ? 'active' : '' }}">
      {{ __('m_tours.tour.ui.all') }}
    </a>
  </div>

  {{-- Botón carrito --}}
  <a href="{{ route('admin.carts.index') }}" class="btn btn-primary mb-3 float-end">
    <i class="fas fa-shopping-cart"></i> {{ __('m_tours.tour.ui.view_cart') }}
  </a>
  <div class="clearfix"></div>

  {{-- Tabla de tours --}}
  <div class="card">
    <div class="card-body p-0">
      @include('admin.tours.tourlist')
    </div>
  </div>
@stop

@push('js')
  {{-- Centraliza librerías aquí para evitar duplicados en partials --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    @if(session('success'))
      Swal.fire({ icon: 'success', title: @json(session('success')), timer: 2000, showConfirmButton: false });
    @endif
    @if(session('error'))
      Swal.fire({ icon: 'error', title: @json(session('error')), timer: 2500, showConfirmButton: false });
    @endif
  </script>
@endpush
