{{-- resources/views/admin/tours/partials/tab-languages.blade.php --}}
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          {{-- m_tours.tour.ui.available_languages --}}
          {{ __('m_tours.tour.ui.available_languages') }}
        </h3>
      </div>

      <div class="card-body">
        <div class="form-group">
          <label class="d-block">
            {{ __('m_tours.tour.ui.available_languages') }}
          </label>
          <small class="text-muted d-block mb-2">
            {{ __('m_tours.tour.ui.multiple_hint_ctrl_cmd') }}
          </small>

          @php
            $existingLanguages = $tour ? $tour->languages->pluck('tour_language_id')->toArray() : [];
          @endphp

          @forelse($languages ?? [] as $language)
            <div class="custom-control custom-checkbox mb-2">
              <input
                type="checkbox"
                class="custom-control-input"
                id="language_{{ $language->tour_language_id }}"
                name="languages[]"
                value="{{ $language->tour_language_id }}"
                {{ in_array($language->tour_language_id, old('languages', $existingLanguages)) ? 'checked' : '' }}
              >
              <label class="custom-control-label" for="language_{{ $language->tour_language_id }}">
                <i class="fas fa-language"></i>
                <strong>{{ $language->name }}</strong>
                @if($language->code)
                  <code>{{ strtoupper($language->code) }}</code>
                @endif
              </label>
            </div>
          @empty
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle"></i>
              {{ __('m_tours.tour.ui.none.languages') }}
              <a href="{{ route('admin.languages.index') }}" target="_blank" class="alert-link">
                {{ __('m_tours.language.ui.page_heading') }}
              </a>
            </div>
          @endforelse
        </div>

        @error('languages')
          <div class="alert alert-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle"></i>
          {{-- Usamos el heading general de idiomas como rótulo informativo --}}
          {{ __('m_tours.language.ui.page_heading') }}
        </h3>
      </div>
      <div class="card-body">
        <h5 class="mb-2">{{ __('m_tours.tour.ui.available_languages') }}</h5>
        <p class="small mb-2">
          {{ __('m_tours.tour.ui.multiple_hint_ctrl_cmd') }}
        </p>
        <p class="small mb-0">
          {{-- Texto informativo libre: no hay clave específica, se mantiene como ayuda genérica --}}
          {{ __('m_tours.tour.ui.available_languages') }} —
          {{ __('m_tours.tour.ui.add_tour') }}
        </p>
      </div>
    </div>

    @if($tour ?? false)
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">
            {{-- Lista “actual” de idiomas asignados --}}
            {{ __('m_tours.language.ui.list_title') }}
          </h3>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse($tour->languages as $language)
              <li class="list-group-item">
                <i class="fas fa-language"></i> {{ $language->name }}
              </li>
            @empty
              <li class="list-group-item text-muted">
                {{ __('m_tours.tour.ui.none.languages') }}
              </li>
            @endforelse
          </ul>
        </div>
      </div>
    @endif
  </div>
</div>
