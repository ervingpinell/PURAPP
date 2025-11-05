<div class="row">
  {{-- ====== Incluido ====== --}}
  <div class="col-md-6">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-check"></i>
          {{ __('m_tours.tour.ui.amenities_included') }}
        </h3>
      </div>

      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('m_tours.tour.ui.amenities_included') }}</label>
          <div class="form-text">{{ __('m_tours.tour.ui.amenities_included_hint') }}</div>

          @php
            $includedAmenities = ($tour ?? null) ? $tour->amenities->pluck('amenity_id')->toArray() : [];
          @endphp

          @forelse(($amenities ?? []) as $amenity)
            <div class="form-check mb-2">
              <input
                type="checkbox"
                class="form-check-input"
                id="included_{{ $amenity->amenity_id }}"
                name="included_amenities[]"
                value="{{ $amenity->amenity_id }}"
                {{ in_array($amenity->amenity_id, old('included_amenities', $includedAmenities)) ? 'checked' : '' }}>
              <label class="form-check-label" for="included_{{ $amenity->amenity_id }}">
                @if($amenity->icon)
                  <i class="{{ $amenity->icon }}"></i>
                @endif
                {{ $amenity->name }}
              </label>
            </div>
          @empty
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle"></i>
              {{ __('m_tours.tour.ui.none.amenities') }}
            </div>
          @endforelse
        </div>

        @error('included_amenities')
          <div class="alert alert-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
  </div>

  {{-- ====== No Incluido ====== --}}
  <div class="col-md-6">
    <div class="card card-danger">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-times"></i>
          {{ __('m_tours.tour.ui.amenities_excluded') }}
        </h3>
      </div>

      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('m_tours.tour.ui.amenities_excluded') }}</label>
          <div class="form-text">{{ __('m_tours.tour.ui.amenities_excluded_hint') }}</div>

          @php
            $excludedAmenities = ($tour ?? null) ? $tour->excludedAmenities->pluck('amenity_id')->toArray() : [];
          @endphp

          @forelse(($amenities ?? []) as $amenity)
            <div class="form-check mb-2">
              <input
                type="checkbox"
                class="form-check-input"
                id="excluded_{{ $amenity->amenity_id }}"
                name="excluded_amenities[]"
                value="{{ $amenity->amenity_id }}"
                {{ in_array($amenity->amenity_id, old('excluded_amenities', $excludedAmenities)) ? 'checked' : '' }}>
              <label class="form-check-label" for="excluded_{{ $amenity->amenity_id }}">
                @if($amenity->icon)
                  <i class="{{ $amenity->icon }}"></i>
                @endif
                {{ $amenity->name }}
              </label>
            </div>
          @empty
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle"></i>
              {{ __('m_tours.tour.ui.none.amenities') }}
            </div>
          @endforelse
        </div>

        @error('excluded_amenities')
          <div class="alert alert-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
  </div>
</div>

{{-- ====== Ayuda ====== --}}
<div class="row">
  <div class="col-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle"></i>
          {{ __('m_tours.tour.ui.help_title') }}
        </h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h5>{{ __('m_tours.tour.ui.help_included_title') }}</h5>
            <p class="small">{{ __('m_tours.tour.ui.help_included_text') }}</p>
          </div>
          <div class="col-md-6">
            <h5>{{ __('m_tours.tour.ui.help_excluded_title') }}</h5>
            <p class="small">{{ __('m_tours.tour.ui.help_excluded_text') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ====== Resumen actuales (solo en editar) ====== --}}
@if($tour ?? false)
  <div class="row">
    <div class="col-md-6">
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">{{ __('m_tours.tour.ui.amenities_included') }}</h3>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse($tour->amenities as $amenity)
              <li class="list-group-item">
                @if($amenity->icon) <i class="{{ $amenity->icon }}"></i> @endif
                {{ $amenity->name }}
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('m_tours.tour.ui.none.amenities') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">{{ __('m_tours.tour.ui.amenities_excluded') }}</h3>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse($tour->excludedAmenities as $amenity)
              <li class="list-group-item">
                @if($amenity->icon) <i class="{{ $amenity->icon }}"></i> @endif
                {{ $amenity->name }}
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('m_tours.tour.ui.none.amenities') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
@endif
