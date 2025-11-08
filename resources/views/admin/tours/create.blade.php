{{-- resources/views/admin/tours/create.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_tours.tour.ui.create_title'))

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">{{ __('m_tours.tour.ui.create_title') }}</h1>
    <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> {{ __('m_tours.tour.ui.cancel') }}
    </a>
  </div>
@stop

@section('content')
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
      <strong class="d-block mb-1">{{ __('m_tours.common.form_errors_title') }}</strong>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  <form action="{{ route('admin.tours.store') }}" method="POST" id="tourForm" novalidate>
    @csrf

    <div class="card card-primary card-outline card-outline-tabs">
      <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="tourTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="details-tab" data-bs-toggle="pill" href="#details" role="tab" aria-controls="details" aria-selected="true" title="{{ __('m_tours.tour.fields.details') }}">
              <i class="fas fa-info-circle"></i> {{ __('m_tours.tour.fields.details') }}
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="prices-tab" data-bs-toggle="pill" href="#prices" role="tab" aria-controls="prices" aria-selected="false" title="{{ __('m_tours.tour.fields.price') }}">
              <i class="fas fa-dollar-sign"></i> {{ __('m_tours.tour.fields.price') }}
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="itinerary-tab" data-bs-toggle="pill" href="#itinerary" role="tab" aria-controls="itinerary" aria-selected="false" title="{{ __('m_tours.tour.fields.itinerary') }}">
              <i class="fas fa-route"></i> {{ __('m_tours.tour.fields.itinerary') }}
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="schedules-tab" data-bs-toggle="pill" href="#schedules" role="tab" aria-controls="schedules" aria-selected="false" title="{{ __('m_tours.tour.fields.schedules') }}">
              <i class="fas fa-clock"></i> {{ __('m_tours.tour.fields.schedules') }}
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="languages-tab" data-bs-toggle="pill" href="#languages" role="tab" aria-controls="languages" aria-selected="false" title="{{ __('m_tours.tour.fields.languages') }}">
              <i class="fas fa-language"></i> {{ __('m_tours.tour.fields.languages') }}
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="amenities-tab" data-bs-toggle="pill" href="#amenities" role="tab" aria-controls="amenities" aria-selected="false" title="{{ __('m_tours.tour.fields.amenities') }}">
              <i class="fas fa-check-circle"></i> {{ __('m_tours.tour.fields.amenities') }}
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="summary-tab" data-bs-toggle="pill" href="#summary" role="tab" aria-controls="summary" aria-selected="false" title="{{ __('m_tours.tour.fields.overview') }}">
              <i class="fas fa-eye"></i> {{ __('m_tours.tour.fields.overview') }}
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <div class="tab-content" id="tourTabsContent">
          {{-- Pestaña: Detalles --}}
          <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            @include('admin.tours.partials.tab-details', ['tour' => null])
          </div>

          {{-- Pestaña: Precios --}}
          <div class="tab-pane fade" id="prices" role="tabpanel" aria-labelledby="prices-tab">
            @include('admin.tours.partials.tab-prices', ['tour' => null])
          </div>

          {{-- Pestaña: Itinerario --}}
          <div class="tab-pane fade" id="itinerary" role="tabpanel" aria-labelledby="itinerary-tab">
            @include('admin.tours.partials.tab-itinerary', ['tour' => null])
          </div>

          {{-- Pestaña: Horarios --}}
          <div class="tab-pane fade" id="schedules" role="tabpanel" aria-labelledby="schedules-tab">
            @include('admin.tours.partials.tab-schedules', ['tour' => null])
          </div>

          {{-- Pestaña: Idiomas --}}
          <div class="tab-pane fade" id="languages" role="tabpanel" aria-labelledby="languages-tab">
            @include('admin.tours.partials.tab-languages', ['tour' => null])
          </div>

          {{-- Pestaña: Amenidades --}}
          <div class="tab-pane fade" id="amenities" role="tabpanel" aria-labelledby="amenities-tab">
            @include('admin.tours.partials.tab-amenities', ['tour' => null])
          </div>

          {{-- Pestaña: Resumen --}}
          <div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
            @include('admin.tours.partials.tab-summary', ['tour' => null])
          </div>
        </div>
      </div>

      <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-success btn-lg" title="{{ __('m_tours.tour.ui.save') }}">
          <i class="fas fa-save"></i> {{ __('m_tours.tour.ui.save') }}
        </button>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-lg" title="{{ __('m_tours.tour.ui.cancel') }}">
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

  {{-- Persistir pestaña por hash (igual que en edit) --}}
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
