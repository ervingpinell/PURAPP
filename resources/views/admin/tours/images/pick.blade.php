@extends('adminlte::page')

@section('title', __('m_tours.image.ui.page_title_pick'))

@section('content_header')
  <h1>
    {{ __('m_tours.image.ui.page_heading') }}
    <small class="text-muted">{{ __('m_tours.image.ui.choose_tour') }}</small>
  </h1>
@stop

@section('content')
  <form method="GET" class="mb-3">
    <div class="input-group">
      <input type="text"
             name="q"
             class="form-control"
             placeholder="{{ __('m_tours.image.ui.search_placeholder') }}"
             value="{{ $q }}">
      <button class="btn btn-primary" type="submit">
        <i class="fas fa-search"></i> {{ __('m_tours.image.ui.search_button') }}
      </button>
    </div>
  </form>

  @if($tours->count() === 0)
    <div class="alert alert-info">{{ __('m_tours.image.ui.no_results') }}</div>
  @else
    <div class="row g-3">
      @foreach($tours as $tour)
        @php $cover = $tour->cover_url ?? asset('images/volcano.png'); @endphp

        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
          <div class="card h-100">
            <img src="{{ $cover }}" class="card-img-top" alt="{{ __('m_tours.image.ui.cover_alt') }}">
            <div class="card-body p-2 d-flex flex-column">
              <div class="small fw-bold mb-1">
                #{{ $tour->tour_id }} — {{ $tour->name }}
              </div>
              <a href="{{ route('admin.tours.images.index', $tour->tour_id) }}"
                 class="btn btn-success btn-sm mt-auto">
                {{ __('m_tours.image.ui.manage_images') }}
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
