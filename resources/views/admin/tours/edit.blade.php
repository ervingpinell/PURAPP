@extends('adminlte::page')

@section('title', __('m_tours.tour.ui.edit_title', ['name' => $tour->name]))

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1>{{ __('m_tours.tour.ui.edit_title') }}: {{ $tour->name }}</h1>
    <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> {{ __('m_tours.tour.ui.cancel') }}
    </a>
  </div>
@stop

@section('content')
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <strong>{{ __('m_tours.common.errors') }}</strong>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  <form action="{{ route('admin.tours.update', $tour) }}" method="POST" id="tourForm">
    @csrf
    @method('PUT')

    <div class="card card-primary card-outline card-outline-tabs">
      <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="tourTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="summary-tab" data-bs-toggle="pill" href="#summary" role="tab">
              <i class="fas fa-eye"></i> {{ __('m_tours.tour.fields.overview') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="details-tab" data-bs-toggle="pill" href="#details" role="tab">
              <i class="fas fa-info-circle"></i> {{ __('m_tours.tour.fields.details') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="prices-tab" data-bs-toggle="pill" href="#prices" role="tab">
              <i class="fas fa-dollar-sign"></i> {{ __('m_tours.tour.fields.price') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="itinerary-tab" data-bs-toggle="pill" href="#itinerary" role="tab">
              <i class="fas fa-route"></i> {{ __('m_tours.tour.fields.itinerary') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="schedules-tab" data-bs-toggle="pill" href="#schedules" role="tab">
              <i class="fas fa-clock"></i> {{ __('m_tours.tour.fields.schedules') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="languages-tab" data-bs-toggle="pill" href="#languages" role="tab">
              <i class="fas fa-language"></i> {{ __('m_tours.tour.fields.languages') }}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="amenities-tab" data-bs-toggle="pill" href="#amenities" role="tab">
              <i class="fas fa-check-circle"></i> {{ __('m_tours.tour.fields.amenities') }}
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <div class="tab-content" id="tourTabsContent">
          {{-- Resumen --}}
          <div class="tab-pane fade show active" id="summary" role="tabpanel">
            @include('admin.tours.partials.tab-summary', ['tour' => $tour])
          </div>

          {{-- Detalles --}}
          <div class="tab-pane fade" id="details" role="tabpanel">
            @include('admin.tours.partials.tab-details', ['tour' => $tour])
          </div>

          {{-- Precios --}}
          <div class="tab-pane fade" id="prices" role="tabpanel">
            @include('admin.tours.partials.tab-prices', ['tour' => $tour])
          </div>

          {{-- Itinerario --}}
          <div class="tab-pane fade" id="itinerary" role="tabpanel">
            @include('admin.tours.partials.tab-itinerary', ['tour' => $tour])
          </div>

          {{-- Horarios --}}
          <div class="tab-pane fade" id="schedules" role="tabpanel">
            @include('admin.tours.partials.tab-schedules', ['tour' => $tour])
          </div>

          {{-- Idiomas --}}
          <div class="tab-pane fade" id="languages" role="tabpanel">
            @include('admin.tours.partials.tab-languages', ['tour' => $tour])
          </div>

          {{-- Amenidades --}}
          <div class="tab-pane fade" id="amenities" role="tabpanel">
            @include('admin.tours.partials.tab-amenities', ['tour' => $tour])
          </div>
        </div>
      </div>

      <div class="card-footer">
        <button type="submit" class="btn btn-primary btn-lg">
          <i class="fas fa-save"></i> {{ __('m_tours.tour.ui.save') }}
        </button>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-lg">
          <i class="fas fa-times"></i> {{ __('m_tours.tour.ui.cancel') }}
        </a>
      </div>
    </div>
  </form>
  @include('admin.tours.partials.inline-modals')

@stop

@push('js')
  {{-- Librerías (si no están ya en el layout) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- Reabrir pestaña por hash y persistirla --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tabs = Array.from(document.querySelectorAll('#tourTabs a[data-bs-toggle="pill"]'));
      const urlHash = window.location.hash;
      if (urlHash) {
        const active = tabs.find(a => a.getAttribute('href') === urlHash);
        if (active) new bootstrap.Tab(active).show();
      }
      tabs.forEach(el => {
        el.addEventListener('shown.bs.tab', e => {
          history.replaceState(null, '', e.target.getAttribute('href'));
        });
      });
    });
  </script>

  {{-- Scripts de la pantalla (resumen dinámico, validación, toasts) --}}
  @include('admin.tours.partials.scripts')
  @include('admin.tours.partials.inline-scripts')

@endpush
