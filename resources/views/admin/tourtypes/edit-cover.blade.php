@extends('adminlte::page')

@push('css')
<style>
      /* ===== Header mejorado ===== */
  .gv-header {
    background: linear-gradient(135deg, rgba(13,110,253,.06), rgba(13,110,253,.02));
    border:1px solid var(--border);
    box-shadow: var(--shadow);
    border-radius: var(--radius);
    padding: .9rem 1rem;
  }
  .gv-title {
    display:flex; flex-wrap:wrap; gap:.5rem; align-items:flex-end;
  }
  .gv-title h1 {
    font-size: clamp(1.05rem, 2.2vw, 1.35rem);
    margin:0;
  }
</style>
@endpush
@section('title', __('m_tours.image.cover_updated_title'))

@section('content_header')
  <div class="gv-header">
    <div class="gv-title">
      <h1>
        <i class="fas fa-star me-2 text-primary"></i>
        {{ __('m_tours.image.cover_updated_title') }}
      </h1>
      <span class="gv-kicker">{{ __('m_tours.image.ui.set_cover_btn') }}</span>
    </div>
    @isset($tourType)
      <div class="gv-sub mt-1">
        {{ $tourType->name }} • {{ __('m_tours.image.ui.id_label') }}: {{ $tourType->tour_type_id }}
      </div>
    @endisset
  </div>
@stop

@section('content')
  <div class="row">
    {{-- Portada actual --}}
    <div class="col-md-5 mb-3">
      <div class="card h-100">
        <div class="card-header">
          {{ __('m_tours.image.ui.cover_current_title') }}
        </div>
        <div class="card-body text-center">
          <img
            src="{{ $coverUrl }}"
            alt="{{ __('m_tours.image.ui.cover_alt') }}"
            class="img-fluid rounded mb-2">
          <div class="text-muted small">
            {{ $tourType->name }} ({{ __('m_tours.image.ui.id_label') }}: {{ $tourType->tour_type_id }})
          </div>
        </div>
      </div>
    </div>

    {{-- Subir nueva portada --}}
    <div class="col-md-7">
      <div class="card h-100">
        <div class="card-header">
          {{ __('m_tours.image.ui.upload_new_cover_title') }}
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.types.images.update', $tourType) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label class="form-label" for="coverInput">{{ __('m_tours.image.ui.cover_file_label') }}</label>
              <input id="coverInput" type="file" name="cover" class="form-control" accept="image/*" required>
              @error('cover')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
              <div class="form-text">
                {{ __('m_tours.image.ui.file_help_cover') }}
              </div>
            </div>

            <button class="btn btn-primary">
              <i class="fas fa-upload me-1"></i>
              {{ __('m_tours.image.ui.upload_btn') }}
            </button>

            <a href="{{ route('admin.types.images.pick') }}" class="btn btn-secondary ms-1">
              <i class="fas fa-images me-1"></i>
              {{ __('m_tours.image.ui.manage_images') }}
            </a>
          </form>
        </div>
      </div>
    </div>
  </div>
@stop
