@extends('adminlte::page')

@section('title', 'Category cover')

@section('content_header')
  <h1>
    Category cover
    <small class="text-muted">Upload / replace the cover image</small>
  </h1>
@stop

@section('content')
  <div class="row">
    <div class="col-md-5 mb-3">
      <div class="card">
        <div class="card-header">Current cover</div>
        <div class="card-body text-center">
          <img src="{{ $coverUrl }}" alt="Current cover" class="img-fluid rounded">
          <div class="mt-2 text-muted small">
            {{ $tourType->name }} (ID: {{ $tourType->tour_type_id }})
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card">
        <div class="card-header">Upload new cover</div>
        <div class="card-body">
          {{-- CORREGIDO: usar la ruta que SÍ existe y método PUT --}}
          <form method="POST" action="{{ route('admin.types.images.update', $tourType) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <input type="file" name="cover" class="form-control" accept="image/*" required>
              @error('cover') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
              <div class="form-text">JPEG/PNG/WebP, máximo 30MB.</div>
            </div>

            <button class="btn btn-primary">
              <i class="fas fa-upload"></i> Save cover
            </button>

            <a href="{{ route('admin.types.images.pick') }}" class="btn btn-secondary">
              Back to list
            </a>
          </form>
        </div>
      </div>
    </div>
  </div>
@stop
