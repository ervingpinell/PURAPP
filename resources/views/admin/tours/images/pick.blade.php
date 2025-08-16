@extends('adminlte::page')

@section('title', 'Imágenes de Tours — Elegir tour')

@section('content_header')
  <h1>Imágenes de Tours <small class="text-muted">Elegir tour</small></h1>
@stop

@section('content')
  <form method="GET" class="mb-3">
    <div class="input-group">
      <input type="text" name="q" class="form-control" placeholder="Buscar por ID o nombre…" value="{{ $q }}">
      <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
    </div>
  </form>

  @if($tours->count() === 0)
    <div class="alert alert-info">No se encontraron tours.</div>
  @else
    <div class="row g-3">
      @foreach($tours as $tour)
        @php
          $cover = $tour->image_path
              ? asset('storage/'.$tour->image_path)
              : asset('images/volcano.png');
        @endphp

        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
          <div class="card h-100">
            <img src="{{ $cover }}" class="card-img-top" alt="Portada">
            <div class="card-body p-2 d-flex flex-column">
              <div class="small fw-bold mb-1">
                #{{ $tour->tour_id }} — {{ $tour->name }}
              </div>
              <a href="{{ route('admin.tours.images.index', $tour->tour_id) }}"
                 class="btn btn-success btn-sm mt-auto">
                Administrar imágenes
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $tours->links() }}
    </div>
  @endif
@stop
