@extends('adminlte::page')

@section('title', __('m_tours.image.cover_updated_title'))

@section('content_header')
  <h1>
    {{ __('m_tours.image.cover_updated_title') }}
    <small class="text-muted">{{ __('m_tours.image.ui.set_cover_btn') }}</small>
  </h1>
@stop

@section('content')
  <div class="row">
    <div class="col-md-5 mb-3">
      <div class="card">
        <div class="card-header">{{ __('m_tours.image.ui.cover_alt') }}</div>
        <div class="card-body text-center">
          <img src="{{ $coverUrl }}" alt="{{ __('m_tours.image.ui.cover_alt') }}" class="img-fluid rounded">
          <div class="mt-2 text-muted small">
            {{ $tourType->name }} (ID: {{ $tourType->tour_type_id }})
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card">
        <div class="card-header">{{ __('m_tours.image.ui.upload_btn') }}</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.types.images.update', $tourType) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <input type="file" name="cover" class="form-control" accept="image/*" required>
              @error('cover') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              <div class="form-text">JPEG/PNG/WebP, 30MB max.</div>
            </div>

            <button class="btn btn-primary">
              <i class="fas fa-upload"></i> {{ __('m_tours.image.saved') }}
            </button>

            <a href="{{ route('admin.types.images.pick') }}" class="btn btn-secondary">
              {{ __('m_tours.image.ui.manage_images') }}
            </a>
          </form>
        </div>
      </div>
    </div>
  </div>
@stop
